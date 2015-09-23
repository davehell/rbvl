<?php
require_once dirname(__FILE__) . '/../extras/controls/DataGrid.php';

abstract class BasePresenter extends Presenter
{
    public $R = "r1516";

    protected function beforeRender()
    {
      //texy
      $texy = new Texy();

      //helpers
      $this->template->registerHelper('texy', array($texy, 'process'));
      $this->template->registerHelper('currency', 'Helpers::currency');
      $this->template->registerHelper('czechDate', 'Helpers::czechDate');
      $this->template->registerHelper('czechTime', 'Helpers::czechTime');
      $this->template->registerHelper('round3', 'Helpers::round3');
      $this->template->registerHelper('vlna', 'Helpers::vlna');

      //identita prihlaseneho uzivatele
  		$user = Environment::getUser();
  		$this->template->user = $user->isLoggedIn() ? $user->getIdentity() : NULL;
    }

    protected function startup()
    {
      $user = Environment::getUser();

      if (!$user->isAllowed($this->name, $this->view))
      {
  			if ($user->isLoggedIn()) {
          $this->flashMessage('Pro tuto akci nemáte dostatečné oprávnění.', 'error');
        }
        else {
          $this->flashMessage('Vstup do této sekce je možný jen po přihlášení.', 'error');
        }
        $backlink = $this->getApplication()->storeRequest();
        $this->redirect('Auth:login', $backlink);
      }

      Form::extensionMethod('Form::addDatePicker', 'Form_addDatePicker'); // v PHP 5.2

      parent::startup();

    }
}

function Form_addDatePicker(Form $_this, $name, $label, $cols = NULL, $maxLength = NULL)
{
	return $_this[$name] = new DatePicker($label, $cols, $maxLength);
}


