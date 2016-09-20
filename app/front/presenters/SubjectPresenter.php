<?php

namespace App\FrontModule\Presenters;

use App;
use Nette;
use Nette\Utils\Finder;

class SubjectPresenter extends BasePresenter
{
	/**
	 * @var App\Model\TutorialModel
	 * @inject
	 */
	public $tutorials;

	protected function fillTutorial($subject, $year, &$tutorial) {
		foreach($tutorial->getAllFiles() as &$file) {
			$file->setLink($this->link('Download!', [$subject, $year, $tutorial->id, $file->name] ));
		}
	}

	public function actionDefault ( $subject ) {
		if(!isset($this->parameters->menu[$subject]['menu'])) {
			$this->error("This subject does not exist");
		}


		$this->template->subject = $subject;
		$this->template->menu = $this->parameters->menu[$subject]['menu'];
	}

	public function actionDetail ( $subject, $year, $tutorial ) {
		if(!isset($this->parameters->menu[$subject]['menu'][$year]['tutorials'])) {
			$this->error('This tutorial does not exist');
		}

		$tutorials = $this->parameters->menu[$subject]['menu'][$year]['tutorials'];
		$directory = $tutorials['directory'] . DIRECTORY_SEPARATOR . $tutorial;

		$this->template->subject = $subject;
		$this->template->year = $year;
		try {
			$this->template->tutorials = $this->tutorials->all($subject, $year);
			$this->template->tutorial = $this->tutorials->item($directory);
			$this->fillTutorial($subject, $year, $this->template->tutorial);
		} catch(\Exception $e) {
			$this->error($e->getMessage());;
		}
	}

	public function handleDownload($subject, $year, $tutorial, $name) {
		if(!isset($this->parameters->menu[$subject]['menu'][$year]['tutorials'])) {
			$this->error('This file does not exist');
		}

		$tutorials = $this->parameters->menu[$subject]['menu'][$year]['tutorials'];
		$directory = $tutorials['directory'] . DIRECTORY_SEPARATOR . $tutorial;
		try {
			$tutorial = $this->tutorials->item($directory);
			$path = $tutorial->getFile($name)->path;
		} catch(\Exception $e) {
			$this->error($e->getMessage());
		}
		$this->forward('File:download', [ 'file' => $path, 'name' => $name ]);
	}

	public function actionYear ( $subject, $year ) {

		if(!isset($this->parameters->menu[$subject]['menu'][$year]['semester'])) {
			$this->error("This subject does not have a semester");
		}


		$this->template->subject = $subject;
		$this->template->year = $year;
		$this->template->semester = (object) $this->parameters->menu[$subject]['menu'][$year]['semester'];

		try {
			$this->template->tutorials = $this->tutorials->all($subject, $year);
		} catch (\Exception $e) {
			$this->error($e->getMessage());
		}
	}

}