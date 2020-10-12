<?php

namespace App\Presenters;

use Nette\Application\UI,
    Nette\Security as NS;

class AuthPresenter extends BasePresenter
{
    /** @persistent */
    public $backlink = '';

    protected function startup()
    {
        parent::startup();

        // if (!$this->user->isLoggedIn()) {
        //     if ($this->user->logoutReason === NS\IUserStorage::INACTIVITY) {
        //         $this->flashMessage('Proběhlo odhlášení po dlouhé době neaktivity. Prosím, přihlašte se znovu.');
        //     }
        //     $this->redirect('Auth:login', ['backlink' => $this->storeRequest()]);
        // }
    }

    public function actionLogin($backlink)
    {
    $this->template->pageTitle = '„RB“VL - Přihlášení';
    $this->template->pageHeading = 'Přihlášení';
    $this->template->pageDesc = '';

    $form = new \Nette\Application\UI\Form($this, 'form');
    $form->getElementPrototype()->class('form-horizontal');

    $renderer = $form->getRenderer();
    $renderer->wrappers['pair']['container'] = \Nette\Utils\Html::el('div')->class('form-group');
    $renderer->wrappers['controls']['container'] = NULL;
    $renderer->wrappers['control']['container'] = \Nette\Utils\Html::el('div')->class('col-sm-9');
    $renderer->wrappers['label']['container'] = \Nette\Utils\Html::el('div')->class('col-sm-3 control-label');
    $renderer->wrappers['label']['requiredsuffix'] = " *";

        $form->addText('username', 'Uživatelské jméno:')
            ->addRule(\Nette\Forms\Form::FILLED, 'Zadejte uživatelské jméno.')
            ->getControlPrototype()->class('form-control');

        $form->addPassword('password', 'Heslo:')
            ->addRule(\Nette\Forms\Form::FILLED, 'Zadejte heslo.')
          ->getControlPrototype()->class('form-control');

        $form->addSubmit('login', 'Přihlásit se')->getControlPrototype()->class('btn btn-primary');
        $form->onSuccess[] = array($this, 'loginFormSubmitted');

        $form->addProtection('Vypršel ochranný časový limit, odešlete prosím formulář ještě jednou');

        $this->template->form = $form;
    }

    public function actionLogout()
    {
        $this->user->logout();
        $this->flashMessage('Proběhlo úspěšné odhlášení.', 'info');
        $this->redirect('Auth:login');
    }


    public function loginFormSubmitted($form)
    {
        try {
            $values = $form->getValues();
            $this->user->login($values->username, $values->password);
            $this->restoreRequest($this->backlink);
            $this->redirect('Default:');

        } catch (NS\AuthenticationException $e) {
            $form->addError($e->getMessage());
        }
    }


}
