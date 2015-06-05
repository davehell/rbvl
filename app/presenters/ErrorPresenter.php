<?php
require_once dirname(__FILE__) . '/BasePresenter.php';



class ErrorPresenter extends BasePresenter
{
	/**
	 * @param  Exception
	 * @return void
	 */
	public function renderDefault($exception)
	{
	
		if ($this->isAjax()) { // AJAX request? Just note this error in payload.
			$this->payload->error = TRUE;
			$this->terminate();

		} elseif ($exception instanceof BadRequestException) {
      $this->template->pageTitle = '„RB“VL - 404 Stránka nenalezena';
      $this->template->pageDesc = '';
      $this->template->pageHeading = 'Chyba 404 - Stránka nenalezena';
			$this->setView('404'); // load template 404.phtml

		} else {
      $this->template->pageTitle = '„RB“VL - 500 Chyba na serveru';
      $this->template->pageDesc = '';
      $this->template->pageHeading = 'Chyba 500 - Chyba na serveru';
			$this->setView('500'); // load template 500.phtml
			Debug::processException($exception); // and handle error by Nette\Debug

		}

	}



}
