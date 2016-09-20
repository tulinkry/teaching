<?php

namespace App\Model;

use Nette;
use Nette\Utils\Finder;
use App\Entities\Tutorial;
use Tulinkry;

class TutorialModel
{
	/**
	 * @var Nette\Caching\IStorage
	 */
	protected $cache;

	/**
	 * @var Tulinkry\Services\Parameters
	 */
	protected $parameters;

	public function __construct (Nette\Caching\IStorage $cache, Tulinkry\Services\ParameterService $service) {
		$this->cache = $cache;
		$this->parameters = $service;
	}

	public function item ($path) {
		return new Tutorial ( $path );
	}

	public function all ($subject, $year) {
		if(!isset($this->parameters->menu[$subject]['menu'][$year]['tutorials']['directory']))
			$this->error("This subject does not have its period in this year");

		$array = [];

		try {
			$tutorials = $this->parameters->menu[$subject]['menu'][$year]['tutorials'];
			foreach (Finder::findDirectories('*')->in($tutorials['directory']) as $path => $file) {
				$array [ $path ] = new Tutorial ( $path ); 
			}
			ksort($array);
		} catch ( \UnexpectedValueException $e ) {
			throw new \Exception("Couldn't open the target directory");
		}
		return $array;
	}

};