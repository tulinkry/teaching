<?php

namespace App\Controls;

use Nette;
use Latte;

class CodeControl extends Nette\Application\UI\Control
{
	public function render ($code, $dependencies = array ()) {
		$this->template->setFile(__DIR__ . DIRECTORY_SEPARATOR . 'codeControl.latte');
		
		$this->template->main = $code;
		$this->template->dependencies = $dependencies;
		$this->template->options_raw = implode( " ", array_map(function($x) { return $x->name; }, array_filter($dependencies, function($x) {
				return Nette\Utils\Strings::endsWith($x->name, '.c') || Nette\Utils\Strings::endsWith($x->name, '.cpp');
			})));
		$this->template->render();
	}
};