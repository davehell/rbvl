<?php

namespace App\Presenters;

use App,
    Nette\Application\UI\Form,
    Nette\Utils\Html,
    Nette\Utils\Paginator,
    App\Components\PaginationControl,
    App\Components\IPaginationControlFactory;


final class AkcePresenter extends BasePresenter
{
  /** @var App\Model\Akce */
  private $akce;

  public function __construct(App\Model\Akce $akce)
  {
    parent::__construct();
    $this->akce = $akce;
  }

  public function renderDefault(int $page = 1): void
  {
      $this->template->pageTitle = '„RB“VL - Turnaje a akce';
      $this->template->pageHeading = 'Volejbalové turnaje a akce v regionu';
      $this->template->pageDesc = 'Volejbalové turnaje a akce v Regionu Beskydy';

      // Zjistíme si celkový počet aktuálních akcí
      $rowsCount = $this->akce->getActualActionsCount();

      $paginator = $this->getComponent('pagination')->getPaginator();
      $paginator->setItemCount($rowsCount);
      $paginator->setItemsPerPage($this->itemsPerPage);
      $paginator->setPage($page);

      // Z databáze si vytáhneme omezenou množinu akcí podle výpočtu Paginatoru
      $rows = $this->akce->findActualActions($paginator->getLength(), $paginator->getOffset());

      // kterou předáme do šablony
      $this->template->rows = $rows;
      $this->template->rowsCount = $rowsCount;
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
      $row = $this->akce->get($id);
      if (!$row) {
        //throw new BadRequestException('Požadovaný záznam nenalezen.');
        $this->flashMessage('Požadovaný záznam neexistuje.', 'danger');
        $this->redirect('default');
      }
      $form->setDefaults($row);
    }
  }

  public function akceFormSubmitted(Form $form)
  {
    if ($form['save']->isSubmittedBy()) {
      $id = (int) $this->getParameter('id');
      $values = $form->getValues();

      if ($id > 0) { //edit
        $row = $this->akce->get($id);

        if($row) {
          $row->update($values);
          $this->flashMessage('Příspěvek byl úspěšně upraven.', 'success');
          $this->redirect('default');
        }
        else {
          $this->error();
        }
      }
      else { //add
        try {
          $this->akce->insert($values);
          $this->flashMessage('Akce byla úspěšně přidána.', 'success');
          $this->redirect('default');
        } catch (\Nette\Database\DriverException $e) {
          $this->flashMessage('Nastala chyba. Příspěvek nebyl vložen.', 'danger');
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

    $row = $this->akce->get($id);
    $this->template->prispevek = $row;
    if (!$row) {
      //throw new BadRequestException('Požadovaný záznam nenalezen.');
      $this->flashMessage('Požadovaný záznam neexistuje.', 'danger');
      $this->redirect('default');
    }
  }



  public function deleteFormSubmitted(Form $form)
  {
    if ($form['delete']->isSubmittedBy()) {
      $row = $this->akce->get($this->getParameter('id'));
      if($row) {
        $row->delete();
        $this->flashMessage('Příspěvek byl úspěšně smazán.', 'success');
        $this->redirect('default');
      }
      else {
        $this->error();
      }
    }

    $this->redirect('default');
  }


    /********************* facilities *********************/

  protected function createComponentAkceForm(): Form
  {
      $id = $this->getParameter('id');
      $form = new Form;
      $form->getElementPrototype()->class('form-horizontal');

      $renderer = $form->getRenderer();
      $renderer->wrappers['pair']['container'] = Html::el('div')->class('form-group');
      $renderer->wrappers['controls']['container'] = NULL;
      $renderer->wrappers['control']['container'] = Html::el('div')->class('col-sm-9');
      $renderer->wrappers['label']['container'] = Html::el('div')->class('col-sm-3 control-label');
      $renderer->wrappers['label']['requiredsuffix'] = " *";


      $form->addText('nazev', 'Název:', 40)
        ->addRule(Form::MAX_LENGTH, 'Maximální délka názvu akce může být %d znaků', 200)
        ->addRule(Form::FILLED, 'Zadejte název akce.')
        ->getControlPrototype()->class('form-control');

      $form->addDatePicker('datum_od', 'Datum začátku:', 10)
        ->addRule(Form::FILLED, 'Zadejte datum začátku akce.')
        ->getControlPrototype()->class('form-control');

      $form->addDatePicker('datum_do', 'Datum konce:', 10)
        ->getControlPrototype()->class('form-control');

      $form->addTextArea('popis', 'Popis:', 0, 20)
        ->addRule(Form::FILLED, 'Zadejte popis akce')
        ->getControlPrototype()->class('form-control');

      $form->addText('startovne', 'Startovné:', 20)
        ->addRule(Form::MAX_LENGTH, 'Maximální délka popisu startovného může být %d znaků', 100)
        ->addRule(Form::FILLED, 'Zadejte startovné na akci')
        ->getControlPrototype()->class('form-control');

      $form->addText('jmeno', 'Kontakní osoba:', 20)
        ->addRule(Form::MAX_LENGTH, 'Maximální délka jména kontaktní osoby může být %d znaků', 50)
        ->addRule(Form::FILLED, 'Zadejte jméno kontaktní osoby');
      $form['jmeno']->getControlPrototype()->class('form-control');

      $form->addText('telefon', 'Telefon:', 20)
        ->addRule(Form::MAX_LENGTH, 'Maximální délka telefonního čísla může být %d znaků', 15)
        ->addRule(Form::FILLED, 'Zadejte kontaktní telefon')
        ->getControlPrototype()->class('form-control');

      $form->addText('email', 'E-mail:', 20)
        ->getControlPrototype()->class('form-control')
        ->addRule(Form::MAX_LENGTH, 'Maximální délka e-mailu může být %d znaků', 100)
        ->setEmptyValue('@')
        ->addCondition(Form::FILLED)
          ->addRule(Form::EMAIL, 'E-mailová adresa není platná');

      $form->addText('antiSpam', 'Ochrana proti spamu:  Kolik je dvakrát tři? (výsledek napište číslem)', 10)
        ->setOmitted()
        ->addRule(Form::FILLED, 'Vyplňte ochranu proti spamu')
        ->addRule(Form::NUMERIC, 'Špatně vyplněná ochrana proti spamu')
        ->addRule(Form::RANGE, 'Špatně vyplněná ochrana proti spamu', array(6, 6))
        ->getControlPrototype()->class('antispam');
      $form['antiSpam']->getLabelPrototype()->class('antispam');

      $form->addSubmit('save', 'Odeslat')->getControlPrototype()->class('btn btn-primary');
      $form->onSuccess[] = array($this, 'akceFormSubmitted');

      $form->addProtection('Vypršel ochranný časový limit, odešlete prosím formulář ještě jednou');

    return $form;
  }

  protected function createComponentDeleteForm(): Form
  {
    $form = new Form;
    $form->addSubmit('delete', 'Smazat')->getControlPrototype()->class('btn btn-primary');
    $form->addSubmit('cancel', 'Storno')->getControlPrototype()->class('btn btn-default');
    $form->onSuccess[] = array($this, 'deleteFormSubmitted');

    $form->addProtection('Vypršel ochranný časový limit, odešlete prosím formulář ještě jednou');
    return $form;
  }

  protected function createComponentPagination()
  {
    return new PaginationControl( $this->getHttpRequest(), 5 );
  }
}
