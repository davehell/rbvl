<?php

require_once dirname(__FILE__) . '/BasePresenter.php';


class HraciPresenter extends BasePresenter
{
  public function actionDefault()
  {
      $this->template->pageTitle = '„RB“VL - Hráči';
      $this->template->pageHeading = 'Hráči';
      $this->template->pageDesc = '';
      $this->template->robots = 'noindex,noarchive';


      $hraci = new Hraci;
      $rows = $hraci->findAll();
      $this->template->rows = $rows;

  }


    /********************* views add & edit *********************/


  public function renderAdd()
  {
    $this->template->pageTitle = '„RB“VL - Hračí - Nový hráč';
    $this->template->pageHeading = 'Nový hráč';
    $this->template->pageDesc = '';
    $this->template->robots = 'noindex,noarchive';

    $form = $this->getComponent('hracForm');
    $this->template->form = $form;
  }


  public function renderEdit($id = 0)
  {
    $this->template->pageTitle = '„RB“VL - Hračí - Úprava hráče';
    $this->template->pageHeading = 'Úprava hráče';
    $this->template->pageDesc = '';
    $this->template->robots = 'noindex,noarchive';

    $form = $this->getComponent('hracForm');
    $this->template->form = $form;

    if (!$form->isSubmitted()) {
      $hraci = new Hraci;
      $row = $hraci->find($id)->fetch();
      $row["narozen"] = preg_replace('~([0-9]{4})-([0-9]{2})-([0-9]{2})~', '$3.$2.$1', $row["narozen"]);
      if (!$row) {
        //throw new BadRequestException('Požadovaný záznam nenalezen.');
        $this->flashMessage('Požadovaný záznam neexistuje.', 'danger');
        $this->redirect('default');
      }
      $form->setDefaults($row);
    }
  }


  public function renderclear()
  {
    $this->template->pageTitle = '„RB“VL - Hráči - Odebrání hráčů ze soupisek';
    $this->template->pageHeading = 'Odebrání hráčů ze soupisek';
    $this->template->pageDesc = '';
    $this->template->robots = 'noindex,noarchive';

    $this->template->form = $this->getComponent('clearForm');

  }



  public function clearFormSubmitted(AppForm $form)
  {
    if ($form['delete']->isSubmittedBy()) {
      $soupisky = new Soupisky;
      $soupisky->deleteAll();
      $this->flashMessage('Hráči byli odebráni ze soupisek.', 'success');
    }

    $this->redirect('default');
  }


  public function hracFormSubmitted(AppForm $form)
  {
    if ($form['save']->isSubmittedBy()) {
      $id = (int) $this->getParam('id');
      $hraci = new hraci;
      $values = $form->getValues();

      if (strlen($values["narozen"])) {
        $tmp = preg_replace('~([[:space:]])~', '', $values["narozen"]);
        $tmp = explode('.', $tmp);
        if(count($tmp) == 3) $values["narozen"] = $tmp[2] . '-' . $tmp[1] . '-' . $tmp[0]; // database format Y-m-d
        else {
          $this->flashMessage('Chybně zadáno datum narození hráče. Hráč nebyl vytvořen.', 'danger');
          return;
        }
      }
      else $values["narozen"] = null;

      if ($id > 0) { //edit
        try {
          $hraci->update($id, $values);
          $this->flashMessage('Hráč byl úspěšně upraven.', 'success');
          $this->redirect('default');
         } catch (DibiException $e) {
          if($e->getCode() == "1062") {
            $this->flashMessage('Tento hráč už v databázi existuje.', 'danger');
          }
          else {
            $this->flashMessage('Nastala chyba. Hráč nebyl upraven.', 'danger');
          }
        }
      }
      else { //add
        try {
          $hraci->insert($values);
          $this->flashMessage('Nový hráč byl úspěšně vytvořen.', 'success');
          $this->redirect('default');
        } catch (DibiException $e) {
          if($e->getCode() == "1062") {
            $this->flashMessage('Tento hráč už v databázi existuje.', 'danger');
          }
          else {
            $this->flashMessage('Nastala chyba. Hráč nebyl vytvořen.', 'danger');
          }
          //$form->addError($e->getMessage());
          return;
        }
      }
    }
  }


    /********************* view delete *********************/


  public function renderDelete($id = 0)
  {
    $this->template->pageTitle = '„RB“VL - Hráči - Smazání hráče';
    $this->template->pageHeading = 'Smazání hráče';
    $this->template->pageDesc = '';
    $this->template->robots = 'noindex,noarchive';

    $this->template->form = $this->getComponent('deleteForm');
    $hraci = new Hraci;

    $this->template->hrac = $hraci->find($id)->fetch();
    if (!$this->template->hrac) {
      //throw new BadRequestException('Požadovaný záznam nenalezen.');
      $this->flashMessage('Požadovaný záznam neexistuje.', 'danger');
      $this->redirect('default');
    }
  }



  public function deleteFormSubmitted(AppForm $form)
  {
    if ($form['delete']->isSubmittedBy()) {
      $hraci = new Hraci;
      $hraci->delete($this->getParam('id'));
      $this->flashMessage('Hráč byl úspěšně smazán.', 'success');
    }

    $this->redirect('default');
  }

    /********************* facilities *********************/


  protected function createComponent($name)
  {
    switch ($name) {
    case 'hracForm':
      $form = new AppForm($this, $name);
      $form->getElementPrototype()->class('form-horizontal');

      $renderer = $form->getRenderer();
      $renderer->wrappers['pair']['container'] = Html::el('div')->class('form-group');
      $renderer->wrappers['controls']['container'] = NULL;
      $renderer->wrappers['control']['container'] = Html::el('div')->class('col-sm-9');
      $renderer->wrappers['label']['container'] = Html::el('div')->class('col-sm-3 control-label');
      $renderer->wrappers['label']['requiredsuffix'] = " *";

      $druzstvo = $this->getParam('id');

      $form->addText('prijmeni', 'Příjmení:', 50)
        ->addRule(Form::MAX_LENGTH, 'Maximální délka příjmení může být %d znaků', 100)
        ->addRule(Form::FILLED, 'Zadejte příjmení hráče.')
        ->getControlPrototype()->class('form-control');

      $form->addText('jmeno', 'Jméno:', 30)
        ->addRule(Form::MAX_LENGTH, 'Maximální délka jména může být %d znaků', 100)
        ->addRule(Form::FILLED, 'Zadejte jméno hráče.')
        ->getControlPrototype()->class('form-control');

      $form->addText('narozen', 'Datum narození:', 10)
        ->getControlPrototype()->class('form-control');

      $form->addSubmit('save', 'Uložit')->getControlPrototype()->class('btn btn-primary');
      $form->onSubmit[] = array($this, 'hracFormSubmitted');

      $form->addProtection('Vypršel ochranný časový limit, odešlete prosím formulář ještě jednou');
      return;

    case 'clearForm':
      $form = new AppForm($this, $name);
      $form->addSubmit('delete', 'Smazat')->getControlPrototype()->class('btn btn-primary');
      $form->addSubmit('cancel', 'Storno')->getControlPrototype()->class('btn btn-default');
      $form->onSubmit[] = array($this, 'clearFormSubmitted');

      $form->addProtection('Vypršel ochranný časový limit, odešlete prosím formulář ještě jednou');
      return;

    case 'deleteForm':
      $form = new AppForm($this, $name);
      $form->addSubmit('delete', 'Smazat')->getControlPrototype()->class('btn btn-primary');
      $form->addSubmit('cancel', 'Storno')->getControlPrototype()->class('btn btn-default');
      $form->onSubmit[] = array($this, 'deleteFormSubmitted');

      $form->addProtection('Vypršel ochranný časový limit, odešlete prosím formulář ještě jednou');
      return;

    default:
      parent::createComponent($name);
    }
  }

}
