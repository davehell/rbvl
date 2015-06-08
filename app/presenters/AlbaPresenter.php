<?php

require_once dirname(__FILE__) . '/BasePresenter.php';


class AlbaPresenter extends BasePresenter
{
  public function actionDefault()
  {
      $this->template->pageTitle = '„RB“VL - Fotogalerie';
      $this->template->pageHeading = 'Fotogalerie';
      $this->template->pageDesc = 'Fotky z průběhu RBVL';
      $this->template->scripts = array('lightbox');

  }

  public function renderAdd()
  {
    $this->template->pageTitle = '„RB“VL - Fotogalerie - Nové album';
    $this->template->pageHeading = 'Nové album';
    $this->template->pageDesc = '„RB“VL - vložení nového alba';
    $this->template->robots = 'noindex,noarchive';

    $form = $this->getComponent('albumForm');
    $this->template->form = $form;
  }

  public function renderEdit($id = 0)
  {
    $this->template->pageTitle = '„RB“VL - Fotogalerie - Úprava alba';
    $this->template->pageHeading = 'Úprava alba';
    $this->template->pageDesc = '';
    $this->template->robots = 'noindex,noarchive';

    $form = $this->getComponent('albumForm');
    $this->template->form = $form;

    if (!$form->isSubmitted()) {
      $alba = new Alba;
      $row = $alba->find($id)->fetch();
      if (!$row) {
        //throw new BadRequestException('Požadovaný záznam nenalezen.');
        $this->flashMessage('Požadovaný záznam neexistuje.', 'error');
        $this->redirect('Foto:default');
      }
      $form->setDefaults($row);
    }
  }

  public function albumFormSubmitted(AppForm $form)
  {
    if ($form['save']->isSubmittedBy()) {
      $id = (int) $this->getParam('id');
      $alba = new Alba;

      if ($id > 0) { //edit
        try {
          $alba->update($id, $form->getValues());
          $this->flashMessage('Album bylo úspěšně upraveno.', 'success');
          $this->redirect('Foto:default');
         } catch (DibiException $e) {
          $this->flashMessage('Nastala chyba. Album nebylo upraveno.', 'error');
          //$form->addError($e->getMessage());
        }
      }
      else { //add
       try {
          $alba->insert($form->getValues());
          $this->flashMessage('Album bylo úspěšně přidáno.', 'success');
          $this->redirect('Foto:default');
        } catch (DibiException $e) {
          $this->flashMessage('Nastala chyba. Album nebylo přidáno.', 'error');
          //$form->addError('');
        }
      }
    }
  }
    /********************* view delete *********************/


  public function renderDelete($id = 0)
  {
    $this->template->pageTitle = '„RB“VL - Fotogalerie - Smazání alba';
    $this->template->pageHeading = 'Smazání alba';
    $this->template->pageDesc = '';
    $this->template->robots = 'noindex,noarchive';

    $this->template->form = $this->getComponent('deleteForm');
    $alba = new Alba;

    $this->template->album = $alba->find($id)->fetch();
    if (!$this->template->album) {
      //throw new BadRequestException('Požadovaný záznam nenalezen.');
      $this->flashMessage('Požadovaný záznam neexistuje.', 'error');
      $this->redirect('Foto:default');
    }
  }



  public function deleteFormSubmitted(AppForm $form)
  {
    if ($form['delete']->isSubmittedBy()) {
      $alba = new Alba;
      $alba->delete($this->getParam('id'));
      $this->flashMessage('Album bylo úspěšně smazáno.', 'success');
    }

    $this->redirect('Foto:default');
  }

   /********************* facilities *********************/


  protected function createComponent($name)
  {
    switch ($name) {
    case 'albumForm':
      $id = $this->getParam('id');
      $form = new AppForm($this, $name);
      $form->getElementPrototype()->class('form-horizontal');

      $renderer = $form->getRenderer();
      $renderer->wrappers['pair']['container'] = Html::el('div')->class('form-group');
      $renderer->wrappers['controls']['container'] = NULL;
      $renderer->wrappers['control']['container'] = Html::el('div')->class('col-sm-9');
      $renderer->wrappers['label']['container'] = Html::el('div')->class('col-sm-3 control-label');
      $renderer->wrappers['label']['requiredsuffix'] = " *";

      $form->addText('popis', 'Název alba:', 20)
        ->addRule(Form::MAX_LENGTH, 'Maximální délka názvu alba může být %d znaků', 100)
        ->addRule(Form::FILLED, 'Zadejte název alba')
        ->getControlPrototype()->class('form-control');

      $form->addSubmit('save', 'Uložit')->getControlPrototype()->class('btn btn-primary');
      $form->onSubmit[] = array($this, 'albumFormSubmitted');

      $form->addProtection('Vypršel ochranný časový limit, odešlete prosím formulář ještě jednou');
      return;

    case 'deleteForm':
      $form = new AppForm($this, $name);
      $form->addSubmit('delete', 'Smazat')->getControlPrototype()->class('btn btn-primary');
      $form->addSubmit('cancel', 'Storno')->getControlPrototype()->class('btn');
      $form->onSubmit[] = array($this, 'deleteFormSubmitted');

      $form->addProtection('Vypršel ochranný časový limit, odešlete prosím formulář ještě jednou');
      return;

    default:
      parent::createComponent($name);
    }
  }
}
