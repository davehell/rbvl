<?php

namespace App\Presenters;

use Nette;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    public $R = "r2021";
    public $itemsPerPage = 10;
    public $radius = 5;

    protected function beforeRender()
    {
        $this->template->pageTitle = '';
        $this->template->pageHeading = '';
        $this->template->pageDesc = '';

        //filtry
        $this->template->addFilter("vlna", function ($string) {
            return preg_replace('<([^a-zA-Z0-9])([ksvzaiou])\s([a-zA-Z0-9]{1,})>i', "$1$2\xc2\xa0$3", $string); //&nbsp; === \xc2\xa0
        });

        $this->template->addFilter("currency", function ($value) {
            return str_replace(" ", "\xc2\xa0", number_format($value, 0, "", " ")) . "\xc2\xa0Kč";
        });

        $this->template->addFilter("czechDate", function ($usDate) {
            return $usDate->format("j. n. Y");
        });

        $this->template->addFilter("czechTime", function ($dateInterval) {
            return $dateInterval->format("%H:%I");
        });

        $this->template->addFilter("round3", function ($value) {
            return round($value, 3);
        });

        $this->template->addFilter("aktuality", function ($string) {
            $string = preg_replace('/={2,}/i', "<br>", $string);
            $string = preg_replace('/#{2,}/i', "<br>", $string);
            $string = preg_replace('/\*{2,}/i', "<br>", $string);
            $string = preg_replace('/-{2,}/i', "", $string);
            $string = preg_replace('/\|/i', "", $string);
            $string = preg_replace('/^<br>/i', "", $string);
            return $string;
        });
    }

    protected function startup()
    {
      parent::startup();

      if (!$this->getUser()->isAllowed($this->name, $this->view)) {
        if ($this->getUser()->isLoggedIn()) {
          $this->flashMessage('Pro tuto akci nemáte dostatečné oprávnění.', 'danger');
        }
        else {
          $this->flashMessage('Vstup do této sekce je možný jen po přihlášení.', 'danger');
        }
        $this->redirect('Auth:login', ['backlink' => $this->storeRequest()]);
      }

      \Nette\Forms\Container::extensionMethod('addDatePicker', function ($form, $name, $label = null, $cols = NULL, $maxLength = NULL) {
        return $form[$name] = new \App\Components\DatePicker($label, $cols, $maxLength);
      });
    }

    public function setItemsPerPage($itemsPerPage) {
        $this->itemsPerPage = $itemsPerPage;
    }

    public function setRadius($radius) {
        $this->radius = $radius;
    }
}
