<?php

namespace App\FrontModule\Presenters;

use Nette;

class FilePresenter extends BasePresenter
{

	public function renderDownload ($file, $name, $contentType = 'text/plain')
	{
		if(file_exists($file)) {
			$this->sendResponse(new Nette\Application\Responses\FileResponse($file, $name, $contentType));
		}
		$this->error("'$file' not found");
	}

}