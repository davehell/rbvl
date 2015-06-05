<?php

require_once dirname(__FILE__) . '/BasePresenter.php';

class DiskuzePresenter extends BasePresenter
{
  public function actionDefault()
  {
      $this->template->pageTitle = '„RB“VL - Diskuze';
      $this->template->pageHeading = 'Diskuze';
      $this->template->pageDesc = '„RB“VL - Diskuze';
      


      $diskuze = new Diskuze;
      $articles = $diskuze->findAll(array('vlozeno' => 'desc'));
      $this->template->rows = $articles;
      
      $dataGrid = new DataGrid;
      $dataGrid->bindDataTable($articles);
      $this->addComponent($dataGrid, 'dg');
      $this->template->dataGrid = $dataGrid;
  }


    /********************* views add & edit *********************/


  public function renderAdd()
  {
    $this->template->pageTitle = '„RB“VL - Diskuze - Nový příspěvek';
    $this->template->pageHeading = 'Nový příspěvek do diskuze';
    $this->template->pageDesc = '„RB“VL - vložení nového příspěvku do diskuze';
    $this->template->scripts = array('antispam');

    $form = $this->getComponent('diskuzeForm');
    $this->template->form = $form;
  }
  
    
  public function renderEdit($id = 0)
  {
    $this->template->pageTitle = '„RB“VL - Diskuze - Úprava příspěvku';
    $this->template->pageHeading = 'Úprava příspěvku';
    $this->template->pageDesc = '';
    $this->template->robots = 'noindex,noarchive';
    $this->template->scripts = array('antispam');

    $form = $this->getComponent('diskuzeForm');
    $this->template->form = $form;

    if (!$form->isSubmitted()) {
      $diskuze = new Diskuze;
      $row = $diskuze->find($id)->fetch();
      if (!$row) {
        //throw new BadRequestException('Požadovaný záznam nenalezen.');
        $this->flashMessage('Požadovaný záznam neexistuje.', 'error');
        $this->redirect('default');
      }
      $form->setDefaults($row);
    }
  }

  public function diskuzeFormSubmitted(AppForm $form)
  {
    if ($form['save']->isSubmittedBy()) {
      $id = (int) $this->getParam('id');
      $diskuze = new Diskuze;
      $values = $form->getValues();
      unset($values['antiSpam']);

      if ($id > 0) { //edit
        try {
          $diskuze->update($id, $values);
          $this->flashMessage('Příspěvek byl úspěšně upraven.', 'success');
          $this->redirect('default');
         } catch (DibiException $e) {
          $this->flashMessage('Nastala chyba. Příspěvek nebyl upraven.', 'error');
          $form->addError($e->getMessage());
        }
      }
      else { //add
        $mail = new Mail;
        $mail->setFrom('diskuze@rbvl.cz', 'Diskuze na RBVL');
        $mail->setSubject('Diskuze na RBVL - nový příspěvek');
        $mail->addTo('david.hellebrand@seznam.cz', 'David Hellebrand');
        $mail->setBody($values['text']);
        $mail->send();
       try {
          $values['vlozeno'] = time();
          $diskuze->insert($values);
          $this->flashMessage('Příspěvek byl úspěšně přidán.', 'success');
          $this->redirect('default');
        } catch (DibiException $e) {
          $this->flashMessage('Nastala chyba. Příspěvek nebyl vložen.', 'error');
          //$form->addError('');
        }
      }
    }
  }


    /********************* view delete *********************/


  public function renderDelete($id = 0)
  {
    $this->template->pageTitle = '„RB“VL - Diskuze - Smazání příspěvku';
    $this->template->pageHeading = 'Smazání příspěvku';
    $this->template->pageDesc = '';
    $this->template->robots = 'noindex,noarchive';

    $this->template->form = $this->getComponent('deleteForm');
    $diskuze = new Diskuze;

    $this->template->prispevek = $diskuze->find($id)->fetch();
    if (!$this->template->prispevek) {
      //throw new BadRequestException('Požadovaný záznam nenalezen.');
      $this->flashMessage('Požadovaný záznam neexistuje.', 'error');
      $this->redirect('default');
    }
  }



  public function deleteFormSubmitted(AppForm $form)
  {
    if ($form['delete']->isSubmittedBy()) {
      $diskuze = new Diskuze;
      $diskuze->delete($this->getParam('id'));
      $this->flashMessage('Příspěvek byl úspěšně smazán.', 'success');
    }

    $this->redirect('default');
  }
  
  
    /********************* facilities *********************/


  protected function createComponent($name)
  {
    switch ($name) {
    case 'diskuzeForm':
      $id = $this->getParam('id');
      $form = new AppForm($this, $name);
      //$form->getElementPrototype()->class('form-horizontal');

      $renderer = $form->getRenderer();
      $renderer->wrappers['label']['requiredsuffix'] = " *";
      $renderer->wrappers['pair']['container'] = Html::el('div')->class('control-group');
      $renderer->wrappers['controls']['container'] = NULL;
      $renderer->wrappers['control']['container'] = Html::el('div')->class('controls');
      $renderer->wrappers['label']['container'] = NULL;


      $form->addText('jmeno', 'Jméno:', 30)
        ->addRule(Form::FILLED, 'Zadejte uživatelské jméno')
        ->getLabelPrototype()->class('control-label');

      $form->addTextArea('text', 'Text:', 0, 20)
        ->addRule(Form::FILLED, 'Zadejte text příspěvku.')
        ->getControlPrototype()->class('span9');
      $form['text']->getLabelPrototype()->class('control-label');
        

      $form->addText('antiSpam', 'Ochrana proti spamu:  Kolik je dvakrát tři? (výsledek napište číslem)', 30)
        ->addRule(Form::FILLED, 'Vyplňte ochranu proti spamu')
        ->addRule(Form::NUMERIC, 'Špatně vyplněná ochrana proti spamu')
        ->addRule(Form::RANGE, 'Špatně vyplněná ochrana proti spamu', array(6, 6));
      $form['antiSpam']->getControlPrototype()->class('antispam');
      $form['antiSpam']->getLabelPrototype()->class('antispam');



      $form->addSubmit('save', 'Odeslat')->getControlPrototype()->class('btn btn-primary');
      $form->onSubmit[] = array($this, 'diskuzeFormSubmitted');

      $form->addProtection('Vypršel ochranný časový limit, odešlete prosím formulář ještě jednou');
      return;

    case 'deleteForm':
      $form = new AppForm($this, $name);
      $form->addSubmit('delete', 'Smazat')->getControlPrototype()->class('btn btn-primary');
      $form->addSubmit('cancel', 'Zrušit');
      $form->onSubmit[] = array($this, 'deleteFormSubmitted');

      $form->addProtection('Vypršel ochranný časový limit, odešlete prosím formulář ještě jednou');
      return;
      
    default:
      parent::createComponent($name);
    }
  }
  
}
