<?php

namespace App\Entities;


use Nette;
use Nette\Utils\Finder;


class Tutorial extends Nette\Object
{
	const README_PATTERN = '[rR][eE][aA][dD][mM][eE].*';

	protected $id;
	protected $path;
	protected $name;
	protected $description = '';
	protected $readme = false;
	protected $files = null;
	protected $programs = null;
	protected $allFiles = null;

	protected $programsDetected = false;
	protected $hasReadme = false;
	protected $readmeMarkdown = false;

	protected $contents = array ();


	public function __construct ( $path ) {
		$this->path = $path;
		$this->programsDetected = Finder::findFiles('*.neon')->in($path)->count() > 0;
		$this->name = $this->id = basename($path);
		$this->hasReadme = Finder::findFiles(self::README_PATTERN)->in($path)->count() > 0;
	}

	public function getId () {
		return $this->id;
	}

	public function getPath () {
		return $this->path;
	}

	public function getName () {
		return $this->name;
	}

	public function getDescription () {
		return $this->description;
	}

	public function getReadme () {
		if(!$this->readme) {
			foreach(Finder::findFiles(self::README_PATTERN)
					->in($this->path) as $p => $f) {
				if(($this->readme = $this->getFileContent($p)) !== false) {
					$this->readmeMarkdown = Nette\Utils\Strings::endsWith($p, '.md') ||
									   		Nette\Utils\Strings::endsWith($p, '.MD');
			   		break;
				}
			}
		}
		return $this->readme;
	}

	public function isReadmeMarkdown () {
		$this->getReadme();
		return $this->readmeMarkdown;
	}

	public function hasReadme () {
		return $this->hasReadme && $this->getReadme();
	}

	private function getFileContent ( $path ) {
		if(!isset($this->contents[$path])) {
			$this->contents[$path] = @file_get_contents($path);
		}
		return $this->contents[$path];
	}

	public function getFiles () {
		if(!$this->files) {
			$this->getAllFiles();
			$this->getPrograms();
		}
		return $this->files;
	}

	public function getFile($name) {
		$this->getAllFiles();

		if(isset($this->allFiles[$name])) {
			return $this->allFiles[$name];
		}

		foreach($this->allFiles as $p => $file) {
			if($file->name === $name)
				return $file;
		}

		return NULL;
	}

	public function getAllFiles () {
		if(!$this->allFiles) {
			foreach(Finder::findFiles('*.[ch]pp', '*.[ch]', 'Makefile')->in($this->path) as $path => $file) {
				if(($source = $this->getFileContent($path)) === FALSE) {
					continue;
				}

				$this->allFiles [ $path ] = $this->files [ $path ] = $newFile = new CodeFile($file->getFilename(), $path, $source);
			}
			ksort ($this->files);
			ksort ($this->allFiles);
		}
		return $this->allFiles;
	}

	public function hasPrograms () {
		return $this->programsDetected;
	}

	public function getPrograms () {
		if(!$this->programs) {
			$config = false;
			// find program configuration
			foreach(Finder::findFiles('*.neon')->in($this->path) as $path => $file) {
				// to ma poser mame neon
				if(($content = $this->getFileContent($path)) === false)
					continue;

				if(($config = Nette\Neon\Neon::decode($content)) === false)
					continue;
			}		

			if($config && isset($config['programs'])) {
				$this->programs = $config['programs'];

				foreach($this->programs as $key => &$program) {
					try {
						$fname = $this->path . DIRECTORY_SEPARATOR . $program['source'];
						if(!($program['main'] = $this->getFile($fname)))
							throw new \Exception;

						foreach($program['dependency'] as $key => &$dependency) {
							$dpFname = $this->path . DIRECTORY_SEPARATOR . $dependency;
							if(!($dependency = $this->getFile($dpFname)))
								throw new \Exception;
						}

						$program = new ProgramUnit($program['main'], $program['dependency']);
						
						foreach($program->dependencies as $key => &$dependency) {
							unset ( $this->files [ $dependency->path ] );
						}
						unset ( $this->files [ $fname ] );
						
					} catch (\Exception $e) {
						unset($this->programs[$key]);
					}
				}
			}
		}
		return $this->programs;
	}

}