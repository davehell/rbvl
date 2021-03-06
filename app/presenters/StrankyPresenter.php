<?php

require_once dirname(__FILE__) . '/BasePresenter.php';


class StrankyPresenter extends BasePresenter
{

  public function renderEdit($id = 0)
  {
    $this->template->pageTitle = '„RB“VL - Stránky - Úprava příspěvku';
    $this->template->pageHeading = 'Úprava příspěvku';
    $this->template->pageDesc = '';
    $this->template->robots = 'noindex,noarchive';

    $form = $this->getComponent('strankyForm');
    $this->template->form = $form;

    if (!$form->isSubmitted()) {
      $stranky = new Stranky;
      $row = $stranky->find($id)->fetch();
      if (!$row) {
        //throw new BadRequestException('Požadovaný záznam nenalezen.');
        $this->flashMessage('Požadovaný záznam neexistuje.', 'danger');
        $this->redirect('Default:default');
      }
      $form->setDefaults($row);
    }
  }

  public function strankyFormSubmitted(AppForm $form)
  {
    if ($form['save']->isSubmittedBy()) {
      $id = (int) $this->getParam('id');
      $stranky = new Stranky;
      $values = $form->getValues();

      try {
        $stranky->update($id, $form->getValues());
        $this->flashMessage('Příspěvek byl úspěšně upraven.', 'success');
        $this->redirect('Default:default');
       } catch (DibiException $e) {
        $this->flashMessage('Nastala chyba. Příspěvek nebyl upraven.', 'danger');
        $form->addError($e->getMessage());
      }
    }
  }




    /********************* facilities *********************/


  protected function createComponent($name)
  {
    switch ($name) {
    case 'strankyForm':
      $id = $this->getParam('id');
      $form = new AppForm($this, $name);

      $renderer = $form->getRenderer();
      $renderer->wrappers['pair']['container'] = Html::el('div')->class('form-group');
      $renderer->wrappers['controls']['container'] = NULL;
      $renderer->wrappers['label']['container'] = Html::el('div')->class('control-label');
      $renderer->wrappers['label']['requiredsuffix'] = " *";

      $form->addHidden('nazev');

      $form->addTextArea('text', 'Text:', 60, 20)
        ->addRule(Form::FILLED, 'Zadejte text příspěvku.')
        ->getControlPrototype()->class('form-control');

      $form->addSubmit('save', 'Odeslat')->getControlPrototype()->class('btn btn-primary');
      $form->onSubmit[] = array($this, 'strankyFormSubmitted');

      $form->addProtection('Vypršel ochranný časový limit, odešlete prosím formulář ještě jednou');
      return;

    default:
      parent::createComponent($name);
    }
  }

}
