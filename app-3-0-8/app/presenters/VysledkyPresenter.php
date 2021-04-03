<?php

namespace App\Presenters;

use App,
    Nette\Application\UI\Form,
    Nette\Utils\Html;


final class VysledkyPresenter extends BasePresenter
{
  /** @var App\Model\Terminy */
  private $terminy;

  public function __construct(App\Model\Terminy $terminy)
  {
    parent::__construct();
    $this->terminy = $terminy;
  }

  // protected function startup()
  // {
  //     $terminy = new Terminy;
  //     $this->template->terminy = $terminy->findAllVysledky($this->R)->fetchAll();
  //     $this->template->menuRocnikPopis = '';
  //     parent::startup();
  // }

  public function renderDefault()
  {
      $this->template->pageTitle = '„RB“VL - Výsledky';
      $this->template->pageHeading = 'Výsledky';
      $this->template->pageDesc = 'Výsledky zápasů „Region Beskydy“ volejbalové ligy';

      // $this->template->terminy = $terminy->findAllVysledky($this->R)->fetchAll();
      $rows = $this->terminy->findAllVysledky($this->R);
      $this->template->rows = $rows;
      $this->template->rocnikPopis = '';
  }

  public function renderTermin($id = 0)
  {
      $this->template->pageTitle = '„RB“VL - Výsledky hracího dne';
      $this->template->pageHeading = 'Výsledky';
      $this->template->pageDesc = 'Výsledky zápasů „Region Beskydy“ volejbalové ligy';
      $this->template->scripts = array('vysledky');

      $druzstva = new Druzstva;
      $vysledky= new Vysledky;

      $this->template->druzstva = $druzstva->findAllUnique($this->R, array("nazev" => true));

      $this->template->zapasy = $vysledky->findAllInTermin($id)->fetchAll();
      // Debugger::dump($this->template->zapasy);
      if (!$this->template->zapasy) {
        $this->flashMessage('Požadovaný záznam neexistuje.', 'danger');
      }

      $this->template->datum = '';
      $this->template->skupina = '';
      $this->template->rocnikPopis = '';
  }

  public function renderTabulky($id = 0)
  {

      $this->template->pageTitle = '„RB“VL - Tabulky';
      $this->template->pageHeading = 'Celkové tabulky';
      $this->template->pageDesc = 'Pořadí týmů „Region Beskydy“ volejbalové ligy';


      $tabulky = new Tabulky;
      $this->template->tabulky = $tabulky->getTabulky($id)->fetchAll();
      $this->template->skupina = '';
      $this->template->poradi = 0;
      $this->template->rocnikPopis = '';

      if (!$this->template->tabulky) {
        $this->flashMessage('Požadovaný záznam neexistuje.', 'danger');
        $this->redirect('default');
      }
  }

  public function renderEdit($id = 0)
  {
    $this->template->pageTitle = '„RB“VL - editace výsledků';
    $this->template->pageHeading = 'Editace výsledků';
    $this->template->pageDesc = '';
    $this->template->robots = 'noindex,noarchive';

    $form = $this->getComponent('vysledkyForm');
    $this->template->form = $form;

    if (!$form->isSubmitted()) {
      $vysledky = new Vysledky;
      $row = $vysledky->find($id)->fetch();
      $this->template->vysledek = $row;

      if (!$row) {
        //throw new BadRequestException('Požadovaný záznam nenalezen.');
        $this->flashMessage('Požadovaný záznam neexistuje.', 'danger');
        $this->redirect('default');
      }
      $form->setDefaults($row);
    }
  }

  public function vysledkyFormSubmitted(Nette\Application\UI\Form $form)
  {
    if ($form['save']->isSubmittedBy() || $form['saveAndNext']->isSubmittedBy()) {
      $id = (int) $this->getParameter('id');
      $vysledky = new vysledky;
      $rozlosovani = new Rozlosovani;

      if ($id > 0) { //edit
        try {
          $values = $form->getValues();
          $termin = $values['termin'];

          unset($values['termin']);
          foreach($values as $k => $v) {
            if($v == '') $values[$k] = null;
          }

          $vysledky->update($id, $values);
          $this->flashMessage('Výsledek byl úspěšně upraven.', 'success');

          try{
            $skupina = $vysledky->find($id)->fetch();
            $vysledky->spocitejTabulku($skupina->id_skupina, $this->R);
          } catch (DibiException $e) {
            $this->flashMessage('Nepodařilo se přepočítat tabulku.', 'danger');
            $this->flashMessage($e->getMessage());
          }

          if ($form['save']->isSubmittedBy()) {
            $this->redirect('termin', array($termin));
          } else {
            $this->redirect('edit', array((int)$values['id']+1));
          }
         } catch (DibiException $e) {
          $this->flashMessage('Nastala chyba. Výsledek nebyl upraven.', 'danger');
          $form->addError($e->getMessage());
        }
      }
    }
  }



  public function renderRozlvysl()
  {
      $this->template->pageTitle = '„RB“VL - Rozlosování -> výsledky';
      $this->template->pageHeading = 'Rozlosování -> výsledky';
      $this->template->pageDesc = '';
      $this->template->robots = 'noindex,noarchive';

      $vysledky = new Vysledky;
      $rozlosovani = new Rozlosovani;
      $tabulky = new Tabulky;

      $zapasy = $rozlosovani->findAll($this->R);

      $this->template->vlozeno = 0;
      $this->template->nevlozeno = 0;

      foreach ($zapasy as $zapas)
      {
        $domaci = $tabulky->getDruzstvoId($zapas->skupina_id, $zapas->cislo_domaci)->fetch();
        $hoste  = $tabulky->getDruzstvoId($zapas->skupina_id,  $zapas->cislo_hoste)->fetch();
        $values = array();
        $values['id_zapasu'] = $zapas->id;
        $values['domaci'] = $domaci->druzstvo;
        $values['hoste'] = $hoste->druzstvo;

        try {
          $vysledky->insert($values);
          $this->template->vlozeno++;
        } catch (DibiException $e) {
          $this->template->nevlozeno++;
        }
      }//foreach

      $this->template->celkem = count($zapasy);

  }

    /********************* facilities *********************/


  protected function createComponentVysledkyForm(): Form
  {
    $id = $this->getParameter('id');
    $form = new Form;
    $form->getElementPrototype()->class('form-inline');

    $renderer = $form->getRenderer();
    $renderer->wrappers['pair']['container'] = Nette\Utils\Html::el('div')->class('form-group');
    $renderer->wrappers['controls']['container'] = NULL;
    $renderer->wrappers['control']['container'] = Nette\Utils\Html::el('div')->class('col-sm-9');
    $renderer->wrappers['label']['container'] = Nette\Utils\Html::el('div')->class('col-sm-3 control-label');
    $renderer->wrappers['label']['requiredsuffix'] = " *";

    $form->addText('sety_domaci', 'Sety', 2);
    $form->addText('sety_hoste', ' : ', 2);

    $form->addText('mice1_domaci', 'Míče', 2);
    $form->addText('mice1_hoste', ':', 2);
    $form->addText('mice2_domaci', ',', 2);
    $form->addText('mice2_hoste', ':', 2);
    $form->addText('mice3_domaci', ',', 2);
    $form->addText('mice3_hoste', ':', 2);

    $form->addCheckbox('kontumace_domaci', 'Kontumační prohra domácích');
    $form->addCheckbox('kontumace_hoste', 'Kontumační prohra hostů');

    $form->addSubmit('saveAndNext', 'Uložit a přejít na další');
    $form->addSubmit('save', 'Uložit');

    $form->addHidden('termin');
    $form->addHidden('id', $id);

    $form->onSuccess[] = array($this, 'vysledkyFormSubmitted');

    $form->addProtection('Vypršel ochranný časový limit, odešlete prosím formulář ještě jednou');
    return $form;
  }

}
