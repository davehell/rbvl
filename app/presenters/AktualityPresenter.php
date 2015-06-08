<?php

require_once dirname(__FILE__) . '/BasePresenter.php';


class AktualityPresenter extends BasePresenter
{
  public function actionDefault()
  {
      $this->template->pageTitle = '„RB“VL - Aktuality';
      $this->template->pageHeading = 'Aktuality';
      $this->template->pageDesc = 'Aktuality z „RB“VL';


      $aktuality = new Aktuality;
      $articles = $aktuality->findAll(array('vlozeno' => 'desc'));
      $this->template->rows = $articles;

      $dataGrid = new DataGrid;
      $dataGrid->bindDataTable($articles);
      $this->addComponent($dataGrid, 'dg');
      $this->template->dataGrid = $dataGrid;
  }


    /********************* views add & edit *********************/


  public function renderAdd()
  {
    $this->template->pageTitle = '„RB“VL - Aktuality - Nový příspěvek';
    $this->template->pageHeading = 'Nová aktualita';
    $this->template->pageDesc = '„RB“VL - vložení nové aktuality';
    $this->template->robots = 'noindex,noarchive';

    $form = $this->getComponent('aktualityForm');
    $this->template->form = $form;
  }


  public function renderEdit($id = 0)
  {
    $this->template->pageTitle = '„RB“VL - Aktuality - Úprava příspěvku';
    $this->template->pageHeading = 'Úprava příspěvku';
    $this->template->pageDesc = '';
    $this->template->robots = 'noindex,noarchive';

    $form = $this->getComponent('aktualityForm');
    $this->template->form = $form;

    if (!$form->isSubmitted()) {
      $aktuality = new Aktuality;
      $row = $aktuality->find($id)->fetch();
      if (!$row) {
        //throw new BadRequestException('Požadovaný záznam nenalezen.');
        $this->flashMessage('Požadovaný záznam neexistuje.', 'danger');
        $this->redirect('default');
      }
      $form->setDefaults($row);
    }
  }

  public function aktualityFormSubmitted(AppForm $form)
  {
    if ($form['save']->isSubmittedBy()) {
      $id = (int) $this->getParam('id');
      $aktuality = new Aktuality;
      $values = $form->getValues();

      if ($id > 0) { //edit
        try {
          $aktuality->update($id, $form->getValues());
          $this->flashMessage('Příspěvek byl úspěšně upraven.', 'success');
          $this->redirect('default');
         } catch (DibiException $e) {
          $this->flashMessage('Nastala chyba. Příspěvek nebyl upraven.', 'danger');
          $form->addError($e->getMessage());
        }
      }
      else { //add
        // $mail = new Mail;
        // $mail->setFrom('aktuality@rbvl.cz', 'Aktuality na RBVL');
        // $mail->setSubject('Aktuality na RBVL - nový příspěvek');
        // $mail->addTo('david.hellebrand@seznam.cz', 'David Hellebrand');
        // $mail->setBody($values['text']);
        // $mail->send();
       try {
          $values['vlozeno'] = time();
          $aktuality->insert($values);
          $this->flashMessage('Aktualita byla úspěšně přidána.', 'success');
          $this->redirect('default');
        } catch (DibiException $e) {
          $this->flashMessage('Nastala chyba. Příspěvek nebyl vložen.', 'danger');
          //$form->addError('');
        }
      }
    }
  }


    /********************* view delete *********************/


  public function renderDelete($id = 0)
  {
    $this->template->pageTitle = '„RB“VL - Aktuality - Smazání příspěvku';
    $this->template->pageHeading = 'Smazání příspěvku';
    $this->template->pageDesc = '';
    $this->template->robots = 'noindex,noarchive';

    $this->template->form = $this->getComponent('deleteForm');
    $aktuality = new Aktuality;

    $this->template->prispevek = $aktuality->find($id)->fetch();
    if (!$this->template->prispevek) {
      //throw new BadRequestException('Požadovaný záznam nenalezen.');
      $this->flashMessage('Požadovaný záznam neexistuje.', 'danger');
      $this->redirect('default');
    }
  }



  public function deleteFormSubmitted(AppForm $form)
  {
    if ($form['delete']->isSubmittedBy()) {
      $aktuality = new Aktuality;
      $aktuality->delete($this->getParam('id'));
      $this->flashMessage('Příspěvek byl úspěšně smazán.', 'success');
    }

    $this->redirect('default');
  }


    /********************* facilities *********************/


  protected function createComponent($name)
  {
    switch ($name) {
    case 'aktualityForm':
      $id = $this->getParam('id');
      $form = new AppForm($this, $name);
      $form->getElementPrototype()->class('form-horizontal');

      $renderer = $form->getRenderer();
      $renderer->wrappers['pair']['container'] = Html::el('div')->class('form-group');
      $renderer->wrappers['controls']['container'] = NULL;
      $renderer->wrappers['control']['container'] = Html::el('div')->class('col-sm-9');
      $renderer->wrappers['label']['container'] = Html::el('div')->class('col-sm-3 control-label');
      $renderer->wrappers['label']['requiredsuffix'] = " *";

      $form->addTextArea('text', 'Text:', 0, 20)
        ->addRule(Form::FILLED, 'Zadejte text příspěvku.')
        ->getControlPrototype()->class('form-control');


      $form->addSubmit('save', 'Odeslat')->getControlPrototype()->class('btn btn-primary');
      $form->onSubmit[] = array($this, 'aktualityFormSubmitted');

      $form->addProtection('Vypršel ochranný časový limit, odešlete prosím formulář ještě jednou');
      return;

    case 'deleteForm':
      $form = new AppForm($this, $name);
      $form->addSubmit('delete', 'Smazat')->getControlPrototype()->class('btn btn-primary');
      $form->addSubmit('cancel', 'Zrušit')->getControlPrototype()->class('btn btn-default');;
      $form->onSubmit[] = array($this, 'deleteFormSubmitted');

      $form->addProtection('Vypršel ochranný časový limit, odešlete prosím formulář ještě jednou');
      return;

    default:
      parent::createComponent($name);
    }
  }

}
