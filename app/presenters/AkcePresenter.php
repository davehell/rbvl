<?php

require_once dirname(__FILE__) . '/BasePresenter.php';


class AkcePresenter extends BasePresenter
{
  public function actionDefault()
  {
      $this->template->pageTitle = '„RB“VL - Turnaje a akce';
      $this->template->pageHeading = 'Volejbalové turnaje a akce v regionu';
      $this->template->pageDesc = 'Volejbalové turnaje a akce v Regionu Beskydy';


      $akce = new Akce;
      $turnaje = $akce->findAll('datum_od', 'asc', date('Y-m-d'));
      $this->template->rows = $turnaje;

      $dataGrid = new DataGrid;
      $dataGrid->bindDataTable($turnaje);
      $this->addComponent($dataGrid, 'dg');
      $this->template->dataGrid = $dataGrid;
  }


    /********************* views add & edit *********************/


  public function renderAdd()
  {
    $this->template->pageTitle = '„RB“VL - Turnaje a akce - Nová akce';
    $this->template->pageHeading = 'Nový turnaj nebo akce';
    $this->template->pageDesc = '„RB“VL - vložení nového turnaje';
    $this->template->scripts = array('datepicker', 'antispam');

    $form = $this->getComponent('akceForm');
    $this->template->form = $form;
  }


  public function renderEdit($id = 0)
  {
    $this->template->pageTitle = '„RB“VL - Turnaje a akce - Úprava akce';
    $this->template->pageHeading = 'Úprava akce';
    $this->template->pageDesc = '';
    $this->template->scripts = array('datepicker', 'antispam');
    $this->template->robots = 'noindex,noarchive';

    $form = $this->getComponent('akceForm');
    $this->template->form = $form;

    if (!$form->isSubmitted()) {
      $akce = new Akce;
      $row = $akce->find($id)->fetch();
      if (!$row) {
        //throw new BadRequestException('Požadovaný záznam nenalezen.');
        $this->flashMessage('Požadovaný záznam neexistuje.', 'error');
        $this->redirect('default');
      }
      $form->setDefaults($row);
    }
  }

  public function akceFormSubmitted(AppForm $form)
  {
    if ($form['save']->isSubmittedBy()) {
      $id = (int) $this->getParam('id');
      $akce = new Akce;

      $values = $form->getValues();
      unset($values['antiSpam']);

      if ($id > 0) { //edit
        try {
          $akce->update($id, $values);
          $this->flashMessage('Příspěvek byl úspěšně upraven.', 'success');
          $this->redirect('default');
         } catch (DibiException $e) {
          $this->flashMessage('Nastala chyba. Příspěvek nebyl upraven.', 'error');
          $form->addError($e->getMessage());
        }
      }
      else { //add
       try {
          $akce->insert($values);
          $this->flashMessage('Akce byla úspěšně přidána.', 'success');
          $this->redirect('default');
        } catch (DibiException $e) {
          $this->flashMessage('Nastala chyba. Příspěvek nebyl vložen.', 'error');
          $form->addError($e->getMessage());
        }
      }
    }
  }


    /********************* view delete *********************/


  public function renderDelete($id = 0)
  {
    $this->template->pageTitle = '„RB“VL - Turnaje a akce - Smazání akce';
    $this->template->pageHeading = 'Smazání akce';
    $this->template->pageDesc = '';
    $this->template->robots = 'noindex,noarchive';

    $this->template->form = $this->getComponent('deleteForm');
    $akce = new Akce;

    $this->template->prispevek = $akce->find($id)->fetch();
    if (!$this->template->prispevek) {
      //throw new BadRequestException('Požadovaný záznam nenalezen.');
      $this->flashMessage('Požadovaný záznam neexistuje.', 'error');
      $this->redirect('default');
    }
  }



  public function deleteFormSubmitted(AppForm $form)
  {
    if ($form['delete']->isSubmittedBy()) {
      $akce = new Akce;
      $akce->delete($this->getParam('id'));
      $this->flashMessage('Akce byla úspěšně smazána.', 'success');
    }

    $this->redirect('default');
  }


    /********************* facilities *********************/


  protected function createComponent($name)
  {
    switch ($name) {
    case 'akceForm':
      $id = $this->getParam('id');
      $form = new AppForm($this, $name);
      $form->getElementPrototype()->class('form-horizontal');

      $renderer = $form->getRenderer();
      $renderer->wrappers['pair']['container'] = Html::el('div')->class('control-group');
      $renderer->wrappers['controls']['container'] = NULL;
      $renderer->wrappers['control']['container'] = Html::el('div')->class('controls');
      $renderer->wrappers['label']['container'] = NULL;
      $renderer->wrappers['label']['requiredsuffix'] = " *";

      $form->addText('nazev', 'Název:', 40)
        ->addRule(Form::MAX_LENGTH, 'Maximální délka názvu akce může být %d znaků', 200)
        ->addRule(Form::FILLED, 'Zadejte název akce.')
        ->getLabelPrototype()->class('control-label');
      $form['nazev']->getControlPrototype()->class('span4');

      $form->addDatePicker('datum_od', 'Datum začátku:', 10)
        ->addRule(Form::FILLED, 'Zadejte datum začátku akce.')
        ->getLabelPrototype()->class('control-label');
      $form['datum_od']->getControlPrototype()->class('span2');

      $form->addDatePicker('datum_do', 'Datum konce:', 10)
        ->getLabelPrototype()->class('control-label');
      $form['datum_do']->getControlPrototype()->class('span2');

      $form->addTextArea('popis', 'Popis:', 0, 20)
        ->addRule(Form::FILLED, 'Zadejte popis akce')
        ->getLabelPrototype()->class('control-label');
      $form['popis']->getControlPrototype()->class('span7');

      $form->addText('startovne', 'Startovné:', 20)
        ->addRule(Form::MAX_LENGTH, 'Maximální délka popisu startovného může být %d znaků', 100)
        ->addRule(Form::FILLED, 'Zadejte startovné na akci')
        ->getLabelPrototype()->class('control-label');
        
      $form->addText('jmeno', 'Kontakní osoba:', 20)
        ->addRule(Form::MAX_LENGTH, 'Maximální délka jména kontaktní osoby může být %d znaků', 50)
        ->addRule(Form::FILLED, 'Zadejte jméno kontaktní osoby')
        ->getLabelPrototype()->class('control-label');

      $form->addText('telefon', 'Telefon:', 20)
        ->addRule(Form::MAX_LENGTH, 'Maximální délka telefonního čísla může být %d znaků', 15)
        ->addRule(Form::FILLED, 'Zadejte kontaktní telefon')
        ->getLabelPrototype()->class('control-label');

      $form->addText('email', 'E-mail:', 20)
        ->addRule(Form::MAX_LENGTH, 'Maximální délka e-mailu může být %d znaků', 100)
        ->setEmptyValue('@') 
        ->addCondition(Form::FILLED)
                ->addRule(Form::EMAIL, 'E-mailová adresa není platná');
      $form['email']->getLabelPrototype()->class('control-label');

      $form->addText('antiSpam', 'Ochrana proti spamu:  Kolik je dvakrát tři? (výsledek napište číslem)', 10)
        ->addRule(Form::FILLED, 'Vyplňte ochranu proti spamu')
        ->addRule(Form::NUMERIC, 'Špatně vyplněná ochrana proti spamu')
        ->addRule(Form::RANGE, 'Špatně vyplněná ochrana proti spamu', array(6, 6));
      $form['antiSpam']->getControlPrototype()->class('antispam');
      $form['antiSpam']->getLabelPrototype()->class('antispam');
        
      $form->addSubmit('save', 'Odeslat')->getControlPrototype()->class('btn btn-primary');
      $form->onSubmit[] = array($this, 'akceFormSubmitted');

      $form->addProtection('Vypršel ochranný časový limit, odešlete prosím formulář ještě jednou');
      return;

    case 'deleteForm':
      $form = new AppForm($this, $name);
      $form->addSubmit('delete', 'Smazat')->getControlPrototype()->class('btn btn-primary');
      $form->addSubmit('cancel', 'Zrušit')->getControlPrototype()->class('btn');
      $form->onSubmit[] = array($this, 'deleteFormSubmitted');

      $form->addProtection('Vypršel ochranný časový limit, odešlete prosím formulář ještě jednou');
      return;

    default:
      parent::createComponent($name);
    }
  }

}
