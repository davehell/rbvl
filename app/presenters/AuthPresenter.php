<?php



require_once dirname(__FILE__) . '/BasePresenter.php';


class AuthPresenter extends BasePresenter
{
	/** @persistent */
	public $backlink = '';

  protected function startup()
  {
    $backlink = $this->getApplication()->storeRequest();
		// user authentication
		$user = Environment::getUser();
		if (!$user->isLoggedIn()) {
			if ($user->getLogoutReason() === User::INACTIVITY) {
				$this->flashMessage('Proběhlo odhlášení po dlouhé době neaktivity. Prosím, přihlašte se znovu.');
				$this->redirect('Auth:login', $backlink);
			}

		}
    Presenter::startup();
  }

	public function actionLogin($backlink)
	{
    $this->template->pageTitle = '„RB“VL - Přihlášení';
    $this->template->pageHeading = 'Přihlášení';
    $this->template->pageDesc = '';
    $this->template->robots = 'noindex,noarchive';
    
		$form = new AppForm($this, 'form');
    $form->getElementPrototype()->class('form-horizontal');

		$form->addText('username', 'Uživatelské jméno:')
			->addRule(Form::FILLED, 'Zadejte uživatelské jméno.');

		$form->addPassword('password', 'Heslo:')
			->addRule(Form::FILLED, 'Zadejte heslo.');

		$form->addSubmit('login', 'Přihlásit se');
		$form->onSubmit[] = array($this, 'loginFormSubmitted');

		$form->addProtection('Vypršel ochranný časový limit, odešlete prosím formulář ještě jednou');

		$this->template->form = $form;
	}

	public function actionLogout()
	{
		Environment::getUser()->logout();
		$this->flashMessage('Proběhlo úspěšné odhlášení.', 'info');
		$this->redirect('Auth:login');
	}


	public function loginFormSubmitted($form)
	{
		try {
			$user = Environment::getUser();
			$user->login($form['username']->getValue(), $form['password']->getValue());
			$this->getApplication()->restoreRequest($this->backlink);
			$this->redirect('Default:');

		} catch (AuthenticationException $e) {
			$form->addError($e->getMessage());
		}
	}


}
