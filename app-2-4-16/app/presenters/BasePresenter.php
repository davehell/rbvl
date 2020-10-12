<?php

namespace App\Presenters;

use Nette;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    public $R = "r2021";

    protected function beforeRender()
    {
        $this->template->pageTitle = '';
        $this->template->pageHeading = '';
        $this->template->pageDesc = '';

      // //texy
      // $texy = new Texy\Texy;

      // //helpers
      // $this->template->registerHelper('texy', array($texy, 'process'));
      // $this->template->registerHelper('currency', 'Helpers::currency');
      // $this->template->registerHelper('czechDate', 'Helpers::czechDate');
      // $this->template->registerHelper('czechTime', 'Helpers::czechTime');
      // $this->template->registerHelper('round3', 'Helpers::round3');
      // $this->template->registerHelper('vlna', 'Helpers::vlna');
      // $this->template->registerHelper('aktuality', 'Helpers::aktuality');

      //identita prihlaseneho uzivatele
        // $user = Nette\Environment::getUser();
        // $this->template->user = $user->isLoggedIn() ? $user->getIdentity() : NULL;
    }

    protected function startup()
    {
        parent::startup();

      // if (!$this->user->isAllowed($this->name, $this->view))
      // {
      //  if ($this->user->isLoggedIn()) {
      //     $this->flashMessage('Pro tuto akci nemáte dostatečné oprávnění.', 'danger');
      //   }
      //   else {
      //     $this->flashMessage('Vstup do této sekce je možný jen po přihlášení.', 'danger');
      //   }
      //   $this->redirect('Auth:login', ['backlink' => $this->storeRequest()]);
      // }

      // Nette\Forms\Form::extensionMethod('Nette\Forms\Form::addDatePicker', 'Form_addDatePicker'); // v PHP 5.2

      

    }
}

function Form_addDatePicker(Nette\Forms\Form $_this, $name, $label, $cols = NULL, $maxLength = NULL)
{
    return $_this[$name] = new DatePicker($label, $cols, $maxLength);
}