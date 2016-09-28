<?php

namespace App\FrontModule\Presenters;

use Tulinkry;
use Nette\Utils\DateTime;
use Nette\Utils\FileSystem;
use App;

class BasePresenter extends Tulinkry\Application\UI\Presenter
{
	public function startup () {
		parent::startup();
		$this->template->top_menu = $this->parameters->menu;
		$timestamp = $this->parameters->params['appDir'] . DIRECTORY_SEPARATOR . 'timestamp';
		if(file_exists($timestamp)) {
			$this->template->lastUpdated = DateTime::from(@filemtime($timestamp));
		}
	}

	public static function update() {
		$timestamp = APP_DIR . DIRECTORY_SEPARATOR . 'timestamp';
		if(file_exists($timestamp)) {
			FileSystem::delete($timestamp);
		}
		FileSystem::write($timestamp, "");
	}

	protected function createComponentCodeControl ($name) {
		return $this [ $name ] = new App\Controls\CodeControl;
	}
}