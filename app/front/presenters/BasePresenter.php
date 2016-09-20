<?php

namespace App\FrontModule\Presenters;

use Tulinkry;
use App;

class BasePresenter extends Tulinkry\Application\UI\Presenter
{
	public function startup () {
		parent::startup();
		$this->template->top_menu = $this->parameters->menu;
	}

	protected function createComponentCodeControl ($name) {
		return $this [ $name ] = new App\Controls\CodeControl;
	}
}