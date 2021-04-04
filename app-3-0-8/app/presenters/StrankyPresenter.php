<?php

namespace App\Presenters;

use App,
    Nette\Utils\Html,
    Nette\Application\UI\Form,
    Nette\Application\UI;

final class StrankyPresenter extends BasePresenter
{
  /** @var App\Model\Stranky */
  private $stranky;

  public function __construct(App\Model\Stranky $stranky)
  {
    $this->stranky = $stranky;
  }

  public function renderDefault()
  {
    $this->error();
  }

  public function renderEdit($id = 0)
  {
    $this->template->pageTitle = '„RB“VL - Stránky - Úprava příspěvku';
    $this->template->pageHeading = 'Úprava příspěvku';
    $this->template->pageDesc = '';
    $this->template->robots = 'noindex,noarchive';

    $form = $this->getComponent('strankyForm');
    $this->template->form = $form;

    if (!$form->isSubmitted()) {
      $row = $this->stranky->get($id);
      if (!$row) {
        $this->error();
      }
      $form->setDefaults($row);
    }
  }

  public function strankyFormSubmitted($form)
  {
    if ($form['save']->isSubmittedBy()) {
      $id = (int) $this->getParameter('id');
      $values = $form->getValues();

      $row = $this->stranky->get($id);
      if($row) {
        $row->update($values);
        $this->flashMessage('Příspěvek byl úspěšně upraven.', 'success');
        $this->redirect('Default:default');
      }
      else {
        $this->error();
      }
    }
  }


    /********************* facilities *********************/


  protected function createComponentStrankyForm(): Form
  {
    $id = $this->getParameter('id');
    $form = new Form;

    $renderer = $form->getRenderer();
    $renderer->wrappers['pair']['container'] = Html::el('div')->class('form-group');
    $renderer->wrappers['controls']['container'] = NULL;
    $renderer->wrappers['label']['container'] = Html::el('div')->class('control-label');
    $renderer->wrappers['label']['requiredsuffix'] = " *";

    $form->addHidden('nazev');

    $form->addTextArea('text', 'Text:', 60, 20)
      ->addRule($form::FILLED, 'Zadejte text příspěvku.')
      ->getControlPrototype()->class('form-control');

    $form->addSubmit('save', 'Odeslat')->getControlPrototype()->class('btn btn-primary');
    $form->onSuccess[] = array($this, 'strankyFormSubmitted');

    $form->addProtection('Vypršel ochranný časový limit, odešlete prosím formulář ještě jednou');
    return $form;
  }

}
