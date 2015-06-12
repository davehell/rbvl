<?php

require_once dirname(__FILE__) . '/BasePresenter.php';


class DruzstvaPresenter extends BasePresenter
{
  public function actionDefault()
  {
      $this->template->pageTitle = '„RB“VL - Družstva';
      $this->template->pageHeading = 'Rozdělení družstev do skupin';
      $this->template->pageDesc = 'Družstva hrající „Region Beskydy“ volejbalovou ligu';


      $druzstva = new Druzstva;
      $this->template->druzstva  = $druzstva->findAll($this->R);
      $this->template->skupina = '';


  }

  public function actionSoupiskadelete($hrac, $druzstvo)
  {
      //$druzstvo = $this->getParam('id');
      $soupisky = new Soupisky;
      $soupisky->deleteOnePlayerInTeam($hrac, $druzstvo);
      $this->flashMessage('Hráč byl úspešně odebrán ze soupisky', 'success');

      $this->redirect('edit', $druzstvo);

  }


    /********************* views add & edit *********************/


  public function renderAdd()
  {
    $this->template->pageTitle = '„RB“VL - Družstva - Nové družstvo';
    $this->template->pageHeading = 'Nové družstvo';
    $this->template->pageDesc = '„RB“VL - vložení nového družstva';
    $this->template->robots = 'noindex,noarchive';

    $form = $this->getComponent('druzstvaForm');
    $this->template->form = $form;
  }


  public function renderEdit($id = 0)
  {
    $this->template->pageTitle = '„RB“VL - Družstva - Úprava družstva';
    $this->template->pageHeading = 'Úprava družstva';
    $this->template->pageDesc = '';
    $this->template->robots = 'noindex,noarchive';
    $this->template->scripts = array('autocomplete');

    $form = $this->getComponent('druzstvaForm');
    $this->template->form = $form;

    $formSoupiska = $this->getComponent('hracForm');
    $this->template->formSoupiska = $formSoupiska;

    if (!$form->isSubmitted()) {
      $druzstva = new Druzstva;
      $row = $druzstva->find($id)->fetch();
      if (!$row) {
        //throw new BadRequestException('Požadovaný záznam nenalezen.');
        $this->flashMessage('Požadovaný záznam neexistuje.', 'danger');
        $this->redirect('default');
      }
      $soupiska = $druzstva->soupiska($id)->fetchAll();
      $this->template->soupiska = $soupiska;
      $this->template->druzstvo = $id;
      $form->setDefaults($row);

      $hraci = new Hraci;
      $this->template->hraci = $hraci->findAll();
    }


  }

  public function renderSoupiska($id = 0)
  {
    $this->template->pageTitle = '„RB“VL - Družstva - Soupiska družstva';
    $this->template->pageHeading = 'Soupiska družstva';
    $this->template->pageDesc = 'Soupiska družstva';

    $druzstva = new Druzstva;
    $row = $druzstva->find($id)->fetch();
    $this->template->druzstvo = $row;
    if (!$row) {
      //throw new BadRequestException('Požadovaný záznam nenalezen.');
      $this->flashMessage('Požadovaný záznam neexistuje.', 'danger');
      $this->redirect('default');
    }

    $soupisky = new Soupisky;
    $this->template->soupiska = $soupisky->findAllPlayersInTeam($id);

    $this->template->pageTitle .= " " . $row->nazev;
  }

  public function akceFormSubmitted(AppForm $form)
  {
    if ($form['save']->isSubmittedBy()) {
      $id = (int) $this->getParam('id');
      $druzstva = new Druzstva;


      if ($id > 0) { //edit
        try {
          $druzstva->update($id, $form->getValues());
          $this->flashMessage('Družstvo bylo úspěšně upraveno.', 'success');
          $this->redirect('edit', $id);
         } catch (DibiException $e) {
          $this->flashMessage('Nastala chyba. Družstvo nebylo vloženo.', 'danger');
          $form->addError($e->getMessage());
        }
      }
      else { //add
       try {
          $druzstva->insert($form->getValues());
          $this->flashMessage('Družstvo bylo úspěšně přidáno.', 'success');
          $this->redirect('edit', $id);
        } catch (DibiException $e) {
          $this->flashMessage('Nastala chyba. Družstvo nebylo vloženo.', 'danger');
          //$form->addError($e->getMessage());
        }
      }
    }
  }



  public function hracFormSubmitted(AppForm $form)
  {
    if ($form['save']->isSubmittedBy()) {
      $druzstva = new Druzstva;
      $hraci = new Hraci;
      $soupisky = new Soupisky;

      $values = $form->getValues();
      $idHrac = (int) $values["hrac"];
      $idDruzstvo = (int) $this->getParam('id');

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

      //pripadne vytvoreni hrace
      if ($idHrac == 0) {
        try {
          unset($values["hrac"]);
          $idHrac = (int) $hraci->insert($values);
          $this->flashMessage('Nový hráč byl úspěšně vytvořen.', 'success');
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
      else { //ulozeni pripadnych zmen u jiz existujiciho hrace
        try {
          unset($values["hrac"]);
          $hraci->update($idHrac, $values);
        } catch (DibiException $e) {
          $this->flashMessage('Nastala chyba. Hráč nebyl upraven.', 'danger');
          //$form->addError($e->getMessage());
          return;
        }
      }

      //pridani hrace na soupisku
      try {
         $soupisky->insert(array("hrac" => $idHrac, "druzstvo" => $idDruzstvo));
         $this->flashMessage('Hráč byl přidán na soupisku.', 'success');
         $this->redirect('edit', $idDruzstvo);
      } catch (DibiException $e) {
         if($e->getCode() == "1062") {
           $dupl = $soupisky->findPlayer($idHrac)->fetch();
           $this->flashMessage('Hráč už je zapsán na soupisce družstva ' . $dupl["druzstvo"], 'danger');
         }
         else {
           $this->flashMessage('Nastala chyba. Hráč nebyl přidán na soupisku.', 'danger');
         }
         //$form->addError($e->getMessage());
      }

    }
  }



    /********************* view delete *********************/


  public function renderDelete($id = 0)
  {
    $this->template->pageTitle = '„RB“VL - Družstva - Smazání družstva';
    $this->template->pageHeading = 'Smazání družstva';
    $this->template->pageDesc = '';
    $this->template->robots = 'noindex,noarchive';

    $this->template->form = $this->getComponent('deleteForm');
    $druzstva = new Druzstva;

    $this->template->druzstvo = $druzstva->find($id)->fetch();
    if (!$this->template->druzstvo) {
      //throw new BadRequestException('Požadovaný záznam nenalezen.');
      $this->flashMessage('Požadovaný záznam neexistuje.', 'danger');
      $this->redirect('default');
    }
  }



  public function deleteFormSubmitted(AppForm $form)
  {
    if ($form['delete']->isSubmittedBy()) {
      $druzstva = new Druzstva;
      $druzstva->delete($this->getParam('id'));
      $this->flashMessage('Družstvo bylo úspěšně smazáno.', 'success');
    }

    $this->redirect('default');
  }


    /********************* facilities *********************/


  protected function createComponent($name)
  {
    switch ($name) {
    case 'druzstvaForm':
      $id = $this->getParam('id');
      $form = new AppForm($this, $name);
      $form->getElementPrototype()->class('form-horizontal');

      $renderer = $form->getRenderer();
      $renderer->wrappers['pair']['container'] = Html::el('div')->class('form-group');
      $renderer->wrappers['controls']['container'] = NULL;
      $renderer->wrappers['control']['container'] = Html::el('div')->class('col-sm-9');
      $renderer->wrappers['label']['container'] = Html::el('div')->class('col-sm-3 control-label');
      $renderer->wrappers['label']['requiredsuffix'] = " *";

      $form->addText('nazev', 'Název:', 40)
        ->addRule(Form::MAX_LENGTH, 'Maximální délka názvu družstva může být %d znaků', 100)
        ->addRule(Form::FILLED, 'Zadejte název družstva.')
        ->getControlPrototype()->class('form-control');

      $form->addText('vedouci', 'Vedoucí:', 40)
        ->addRule(Form::MAX_LENGTH, 'Maximální délka jména vedoucího může být %d znaků', 100)
        ->getControlPrototype()->class('form-control');

      $form->addText('telefon', 'Telefon:', 20)
        ->addRule(Form::MAX_LENGTH, 'Maximální délka telefonního čísla může být %d znaků', 15)
        ->getControlPrototype()->class('form-control');

      $form->addText('email', 'E-mail:', 40)
        ->addRule(Form::MAX_LENGTH, 'Maximální délka e-mailu může být %d znaků', 100)
        ->getControlPrototype()->class('form-control')
        ->setEmptyValue('@')
        //->addRule(Form::FILLED, 'Zadejte kontaktní e-mail.')
        ->addCondition(Form::FILLED)
            ->addRule(Form::EMAIL, 'E-mailová adresa není platná');

      //$form->addTextArea('soupiska', 'Soupiska:', 60, 20);

      $form->addSubmit('save', 'Uložit')->getControlPrototype()->class('btn btn-primary');
      $form->onSubmit[] = array($this, 'akceFormSubmitted');

      $form->addProtection('Vypršel ochranný časový limit, odešlete prosím formulář ještě jednou');
      return;

    case 'deleteForm':
      $form = new AppForm($this, $name);
      $form->addSubmit('delete', 'Smazat')->getControlPrototype()->class('btn btn-primary');
      $form->addSubmit('cancel', 'Storno')->getControlPrototype()->class('btn btn-default');
      $form->onSubmit[] = array($this, 'deleteFormSubmitted');

      $form->addProtection('Vypršel ochranný časový limit, odešlete prosím formulář ještě jednou');
      return;


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

      $form->addHidden('hrac');

      $form->addSubmit('save', 'Přidat hráče')->getControlPrototype()->class('btn btn-primary');
      $form->onSubmit[] = array($this, 'hracFormSubmitted');

      $form->addProtection('Vypršel ochranný časový limit, odešlete prosím formulář ještě jednou');
      return;

    default:
      parent::createComponent($name);
    }
  }

}
