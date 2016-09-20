<?php

namespace App\Entities;

use Nette;

class CodeFile extends Nette\Object {

	protected $name;
	protected $path;
	protected $code;
	protected $description;

	protected $link = null;

	public function __construct ($name, $path, $code, $description = '') {
		$this->name = $name;
		$this->code = $code;
		$this->path = $path;
		$this->description = $description;
	}

	public function getLink() {
		return $this->link;
	}

	public function getCode() {
		return $this->code;
	}

	public function getName() {
		return $this->name;
	}

	public function getPath() {
		return $this->path;
	}

	public function getDescription() {
		return $this->description;
	}

	public function setLink($value) {
		$this->link = $value;
		return $this;
	}

	public function setCode($value) {
		$this->code = $value;
		return $this;
	}

	public function setName($value) {
		$this->name = $value;
		return $this;
	}

	public function setPath($value) {
		$this->path = $value;
		return $this;
	}

	public function setDescription($value) {
		$this->description = $value;
		return $this;
	}
}