<?php

namespace App\Presenters;

use App,
    Nette\Application\UI;

final class StrankyPresenter extends BasePresenter
{
  /** @var App\Model\Stranky */
  private $stranky;

  public function __construct(App\Model\Stranky $stranky)
  {
    $this->stranky = $stranky;
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
        //throw new BadRequestException('Požadovaný záznam nenalezen.');
        $this->flashMessage('Požadovaný záznam neexistuje.', 'danger');
        $this->redirect('Default:default');
      }
      $form->setDefaults($row);
    }
  }

  public function strankyFormSubmitted($form)
  {
    if ($form['save']->isSubmittedBy()) {
      $id = (int) $this->getParam('id');
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


  protected function createComponent($name)
  {
    switch ($name) {
    case 'strankyForm':
      $id = $this->getParam('id');
      $form = new \Nette\Application\UI\Form($this, $name);

      $renderer = $form->getRenderer();
      $renderer->wrappers['pair']['container'] = \Nette\Utils\Html::el('div')->class('form-group');
      $renderer->wrappers['controls']['container'] = NULL;
      $renderer->wrappers['label']['container'] = \Nette\Utils\Html::el('div')->class('control-label');
      $renderer->wrappers['label']['requiredsuffix'] = " *";

      $form->addHidden('nazev');

      $form->addTextArea('text', 'Text:', 60, 20)
        ->addRule(\Nette\Forms\Form::FILLED, 'Zadejte text příspěvku.')
        ->getControlPrototype()->class('form-control');

      $form->addSubmit('save', 'Odeslat')->getControlPrototype()->class('btn btn-primary');
      $form->onSuccess[] = array($this, 'strankyFormSubmitted');

      $form->addProtection('Vypršel ochranný časový limit, odešlete prosím formulář ještě jednou');
      return;

    default:
      parent::createComponent($name);
    }
  }

}
