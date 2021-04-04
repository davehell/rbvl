<?php

namespace App\Presenters;

use Nette\Security,
    Nette\Application\UI\Form,
    Nette\Utils\Html;

final class AuthPresenter extends BasePresenter
{
    /** @persistent */
    public $backlink = '';

    protected function startup()
    {
        parent::startup();
    }

    public function actionLogin($backlink)
    {
        $this->template->pageTitle = '„RB“VL - Přihlášení';
        $this->template->pageHeading = 'Přihlášení';
        $this->template->pageDesc = '';

        $form = new Form($this, 'form');
        $form->getElementPrototype()->class('form-horizontal');

        $renderer = $form->getRenderer();
        $renderer->wrappers['pair']['container'] = Html::el('div')->class('form-group');
        $renderer->wrappers['controls']['container'] = NULL;
        $renderer->wrappers['control']['container'] = Html::el('div')->class('col-sm-9');
        $renderer->wrappers['label']['container'] = Html::el('div')->class('col-sm-3 control-label');
        $renderer->wrappers['label']['requiredsuffix'] = " *";

        $form->addText('username', 'Uživatelské jméno:')
            ->addRule($form::FILLED, 'Zadejte %label')
            ->getControlPrototype()->class('form-control');

        $form->addPassword('password', 'Heslo:')
            ->addRule($form::FILLED, 'Zadejte %label')
          ->getControlPrototype()->class('form-control');

        $form->addSubmit('login', 'Přihlásit se')->getControlPrototype()->class('btn btn-primary');
        $form->onSuccess[] = array($this, 'loginFormSubmitted');

        $form->addProtection('Vypršel ochranný časový limit, odešlete prosím formulář ještě jednou');

        $this->template->form = $form;
    }

    public function actionLogout()
    {
        $this->user->logout();
        $this->flashMessage('Byli jste úspěšně odhlášeni z Vašeho účtu.', 'info');
        $this->redirect('Auth:login');
    }


    public function loginFormSubmitted($form)
    {
        try {
            $values = $form->getValues();
            $this->user->login($values->username, $values->password);
            $this->restoreRequest($this->backlink);
            $this->redirect('Default:');
        } catch (Security\AuthenticationException $e) {
            if ($e->getCode() === Security\IAuthenticator::INVALID_CREDENTIAL) {
                $form->addError($e->getMessage());
            }
            else {
                $this->flashMessage($e->getMessage(), 'danger');
            }
        }
    }
}
