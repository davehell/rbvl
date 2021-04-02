<?php
namespace App\Presenters;

use App,
    Nette\Application\UI\Form,
    Nette\Utils\Html;

final class DruzstvaPresenter extends BasePresenter
{
  /** @var App\Model\Druzstva */
  private $druzstva;

  /** @var App\Model\Soupisky */
  private $soupisky;

  /** @var App\Model\Hraci */
  private $hraci;

  public function __construct(App\Model\Druzstva $druzstva, App\Model\Soupisky $soupisky, App\Model\Hraci $hraci)
  {
    parent::__construct();
    $this->druzstva = $druzstva;
    $this->soupisky = $soupisky;
    $this->hraci = $hraci;
  }

  public function renderDefault()
  {
      $this->template->pageTitle = '„RB“VL - Družstva';
      $this->template->pageHeading = 'Rozdělení družstev do skupin';
      $this->template->pageDesc = 'Družstva hrající „Region Beskydy“ volejbalovou ligu';

      $this->template->druzstva  = $this->druzstva->findAllInRocnik($this->R);
      $this->template->skupina = '';
  }

  public function actionSoupiskadelete($hrac, $druzstvo)
  {
      $this->soupisky->deleteOnePlayerInTeam($hrac, $druzstvo);
      $this->flashMessage('Hráč byl úspešně odebrán ze soupisky', 'success');
      $this->redirect('edit', $druzstvo);
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
      $row = $this->druzstva->get($id);
      if (!$row) {
        $this->flashMessage('Požadovaný záznam neexistuje.', 'danger');
        $this->redirect('default');
      }
      $form->setDefaults($row);

      $soupiska = $this->druzstva->soupiskaDruzstva($id);
      $this->template->soupiska = $soupiska;
      $this->template->druzstvo = $id;

      $this->template->hraci = $this->hraci->findAll();
    }
  }


  public function renderSoupiska($id = 0)
  {
    $this->template->pageTitle = '„RB“VL - Družstva - Soupiska družstva';
    $this->template->pageHeading = 'Soupiska družstva';
    $this->template->pageDesc = 'Soupiska družstva';

    $row = $this->druzstva->get($id);
    $this->template->druzstvo = $row;
    if (!$row) {
      $this->flashMessage('Požadovaný záznam neexistuje.', 'danger');
      $this->redirect('default');
    }

    $this->template->soupiska = $this->soupisky->findAllPlayersInTeam($id);

    $this->template->pageTitle .= " " . $row->nazev;
  }

  public function akceFormSubmitted(Form $form)
  {
    if ($form['save']->isSubmittedBy()) {
      $id = (int) $this->getParam('id');
      $values = $form->getValues();

      if ($id > 0) { //edit
        $row = $this->druzstva->get($id);

        if($row) {
         try {
            $row->update($values);
            $this->flashMessage('Družstvo bylo úspěšně upraveno.', 'success');
            $this->redirect('edit', $id);
          } catch (\Nette\Database\DriverException $e) {
            $this->flashMessage('Nastala chyba. Družstvo nebylo vloženo.', 'danger');
          }
        }
        else {
          $this->error();
        }
      }
      else { //add
       try {
          $this->druzstva->insert($values);
          $this->flashMessage('Družstvo bylo úspěšně přidáno.', 'success');
          $this->redirect('edit', $id);
        } catch (\Nette\Database\DriverException $e) {
          $this->flashMessage('Nastala chyba. Družstvo nebylo vloženo.', 'danger');
        }
      }
    }
  }

  public function hracFormSubmitted(Form $form)
  {
    if ($form['save']->isSubmittedBy()) {
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
          $row = $this->hraci->insert($values);
          $idHrac = $row->id;
          $this->flashMessage('Nový hráč byl úspěšně vytvořen.', 'success');
        } catch (\Nette\Database\UniqueConstraintViolationException $e) {
          $this->flashMessage('Tento hráč už v databázi existuje.', 'danger');
          return;
        } catch (\Nette\Database\DriverException $e) {
          $this->flashMessage('Nastala chyba. Hráč nebyl vytvořen.', 'danger');
          return;
        }
      }
      else { //ulozeni pripadnych zmen u jiz existujiciho hrace
        $row = $this->hraci->get($idHrac);

        if($row) {
         try {
            unset($values["hrac"]);
            $row->update($values);
          } catch (\Nette\Database\UniqueConstraintViolationException $e) {
            $this->flashMessage('Tento hráč už v databázi existuje.', 'danger');
            return;
          } catch (\Nette\Database\DriverException $e) {
            $this->flashMessage('Nastala chyba. Hráč nebyl upraven.', 'danger');
            return;
          }
        }
        else {
          $this->error();
        }
      }

      //pridani hrace na soupisku
      try {
        $this->soupisky->insert(array("hrac" => $idHrac, "druzstvo" => $idDruzstvo));
        $this->flashMessage('Hráč byl přidán na soupisku.', 'success');
        $this->redirect('edit', $idDruzstvo);
      } catch (\Nette\Database\UniqueConstraintViolationException $e) {
        $dupl = $this->soupisky->findPlayer($idHrac);
        $this->flashMessage('Hráč už je zapsán na soupisce družstva ' . $dupl["druzstvo"], 'danger');
        return;
      } catch (\Nette\Database\DriverException $e) {
        $this->flashMessage('Nastala chyba. Hráč nebyl přidán na soupisku.', 'danger');
        return;
      }
    }
  }


  protected function createComponent($name)
  {
    switch ($name) {
    case 'druzstvaForm':
      $id = $this->getParam('id');
      $form = new Form($this, $name);
      $form->getElementPrototype()->class('form-horizontal');

      $renderer = $form->getRenderer();
      $renderer->wrappers['pair']['container'] = Html::el('div')->class('form-group');
      $renderer->wrappers['controls']['container'] = NULL;
      $renderer->wrappers['control']['container'] = Html::el('div')->class('col-sm-9');
      $renderer->wrappers['label']['container'] = Html::el('div')->class('col-sm-3 control-label');
      $renderer->wrappers['label']['requiredsuffix'] = " *";

      $form->addText('nazev', 'Název:', 40)
        ->setRequired(TRUE)
        ->addRule(Form::MAX_LENGTH, 'Maximální délka názvu družstva může být %d znaků', 100)
        ->addRule(Form::FILLED, 'Zadejte název družstva.')
        ->getControlPrototype()->class('form-control');

      $form->addText('vedouci', 'Vedoucí:', 40)
        ->setRequired(FALSE)
        ->addRule(Form::MAX_LENGTH, 'Maximální délka jména vedoucího může být %d znaků', 100)
        ->getControlPrototype()->class('form-control');

      $form->addText('telefon', 'Telefon:', 20)
        ->setRequired(FALSE)
        ->addRule(Form::MAX_LENGTH, 'Maximální délka telefonního čísla může být %d znaků', 15)
        ->getControlPrototype()->class('form-control');

      $form->addText('email', 'E-mail:', 40)
        ->setRequired(FALSE)
        ->addRule(Form::MAX_LENGTH, 'Maximální délka e-mailu může být %d znaků', 100)
        ->getControlPrototype()->class('form-control')
        ->setEmptyValue('@')
        //->addRule(Form::FILLED, 'Zadejte kontaktní e-mail.')
        ->addCondition(Form::FILLED)
            ->addRule(Form::EMAIL, 'E-mailová adresa není platná');

      $form->addSubmit('save', 'Uložit')->getControlPrototype()->class('btn btn-primary');
      $form->onSuccess[] = array($this, 'akceFormSubmitted');

      $form->addProtection('Vypršel ochranný časový limit, odešlete prosím formulář ještě jednou');
      return;

    case 'hracForm':
      $form = new Form($this, $name);
      $form->getElementPrototype()->class('form-horizontal');

      $renderer = $form->getRenderer();
      $renderer->wrappers['pair']['container'] = Html::el('div')->class('form-group');
      $renderer->wrappers['controls']['container'] = NULL;
      $renderer->wrappers['control']['container'] = Html::el('div')->class('col-sm-9');
      $renderer->wrappers['label']['container'] = Html::el('div')->class('col-sm-3 control-label');
      $renderer->wrappers['label']['requiredsuffix'] = " *";

      $druzstvo = $this->getParam('id');

      $form->addText('hrac', 'ID')
        ->setRequired(FALSE)
        ->getControlPrototype()->class('form-control')
        ->setHtmlId('#frm-hracForm-hrac');

      $form->addText('prijmeni', 'Příjmení:', 50)
        ->setRequired(TRUE)
        ->addRule(Form::MAX_LENGTH, 'Maximální délka příjmení může být %d znaků', 100)
        ->addRule(Form::FILLED, 'Zadejte příjmení hráče.')
        ->getControlPrototype()->class('form-control');

      $form->addText('jmeno', 'Jméno:', 30)
        ->setRequired(TRUE)
        ->addRule(Form::MAX_LENGTH, 'Maximální délka jména může být %d znaků', 100)
        ->addRule(Form::FILLED, 'Zadejte jméno hráče.')
        ->getControlPrototype()->class('form-control');

      $form->addText('narozen', 'Datum narození:', 10)
        ->setRequired(FALSE)
        ->getControlPrototype()->class('form-control');

      $form->addSubmit('save', 'Přidat hráče')->getControlPrototype()->class('btn btn-primary');
      $form->onSuccess[] = array($this, 'hracFormSubmitted');

      $form->addProtection('Vypršel ochranný časový limit, odešlete prosím formulář ještě jednou');
      return;

    default:
      parent::createComponent($name);
    }
  }
}
