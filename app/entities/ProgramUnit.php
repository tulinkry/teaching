<?php

namespace App\Entities;

use Nette;

class ProgramUnit extends Nette\Object {
	protected $name;
	protected $main;
	protected $dependencies;

	public function __construct(CodeFile $main, $dependencies = array ()) {
		$this->main = $main;
		$this->dependencies = $dependencies;
		$this->name = $main->name;
	}

	public function getName () {
		return $this->name;
	}

	public function setName ($value) {
		$this->name = $value;
		return $this;
	}

	public function getMain () {
		return $this->main;
	}

	public function setMain ($value) {
		$this->main = $value;
		return $this;
	}

	public function getDependencies () {
		return $this->dependencies;
	}

	public function setDependencies ($value) {
		$this->dependencies = $value;
		return $this;
	}

	public function addDependency(CodeFile $value) {
		$this->dependencies[] = $value;
	}
}