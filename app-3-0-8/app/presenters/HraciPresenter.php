<?php
namespace App\Presenters;

use App,
    Nette\Database,
    Nette\Application\UI\Form,
    Nette\Utils\Html;

final class HraciPresenter extends BasePresenter
{
  /** @var App\Model\Soupisky */
  private $soupisky;

  /** @var App\Model\Hraci */
  private $hraci;

  public function __construct(App\Model\Soupisky $soupisky, App\Model\Hraci $hraci)
  {
    parent::__construct();
    $this->soupisky = $soupisky;
    $this->hraci = $hraci;
  }

  public function renderDefault()
  {
      $this->template->pageTitle = '„RB“VL - Hráči';
      $this->template->pageHeading = 'Hráči';
      $this->template->pageDesc = '';
      $this->template->robots = 'noindex,noarchive';

      $rows = $this->hraci->findAllNameSorted();
      $this->template->rows = $rows;

  }

  public function renderAdd()
  {
    $this->template->pageTitle = '„RB“VL - Hráči - Nový hráč';
    $this->template->pageHeading = 'Nový hráč';
    $this->template->pageDesc = '';
    $this->template->robots = 'noindex,noarchive';
    $this->template->scripts = array('datepicker');

    $form = $this->getComponent('hracForm');
    $this->template->form = $form;
  }


  public function renderEdit($id = 0)
  {
    $this->template->pageTitle = '„RB“VL - Hráči - Úprava hráče';
    $this->template->pageHeading = 'Úprava hráče';
    $this->template->pageDesc = '';
    $this->template->robots = 'noindex,noarchive';
    $this->template->scripts = array('datepicker');

    $form = $this->getComponent('hracForm');
    $this->template->form = $form;

    if (!$form->isSubmitted()) {
      $row = $this->hraci->get($id);
      if (!$row) {
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



  public function clearFormSubmitted(Form $form)
  {
    if ($form['delete']->isSubmittedBy()) {
      $this->soupisky->deleteAll();
      $this->flashMessage('Hráči byli odebráni ze soupisek.', 'success');
    }

    $this->redirect('default');
  }


  public function hracFormSubmitted(Form $form)
  {
    if ($form['save']->isSubmittedBy()) {
      $id = (int) $this->getParameter('id');
      $values = $form->getValues();

      if ($id > 0) { //edit
        $row = $this->hraci->get($id);

        if($row) {
          try {
            $row->update($values);
            $this->flashMessage('Nový hráč byl úspěšně upraven.', 'success');
          } catch (Database\UniqueConstraintViolationException $e) {
            $this->flashMessage('Tento hráč už v databázi existuje.', 'danger');
          } catch (Database\DriverException $e) {
            $this->flashMessage('Nastala chyba. Hráč nebyl upraven.', 'danger');
          }
        }
        else {
          $this->error();
        }
      }
      else { //add
        try {
          $this->hraci->insert($values);
          $this->flashMessage('Nový hráč byl úspěšně vytvořen.', 'success');
        } catch (Database\UniqueConstraintViolationException $e) {
          $this->flashMessage('Tento hráč už v databázi existuje.', 'danger');
          return;
        } catch (Database\DriverException $e) {
          $this->flashMessage('Nastala chyba. Hráč nebyl vytvořen.', 'danger');
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

    $row = $this->hraci->get($id);
    $this->template->hrac = $row;
    if (!$row) {
      $this->flashMessage('Požadovaný záznam neexistuje.', 'danger');
      $this->redirect('default');
    }
  }



  public function deleteFormSubmitted(Form $form)
  {
    if ($form['delete']->isSubmittedBy()) {
      $row = $this->hraci->get($this->getParameter('id'));
        if($row) {
          $row->delete();
          $this->flashMessage('Hráč byl úspěšně smazán.', 'success');
          $this->redirect('default');
        }
        else {
          $this->error();
        }
    }

    $this->redirect('default');
  }

    /********************* facilities *********************/

  protected function createComponentHracForm(): Form
  {
    $form = new Form;
    $form->getElementPrototype()->class('form-horizontal');

    $renderer = $form->getRenderer();
    $renderer->wrappers['pair']['container'] = Html::el('div')->class('form-group');
    $renderer->wrappers['controls']['container'] = NULL;
    $renderer->wrappers['control']['container'] = Html::el('div')->class('col-sm-9');
    $renderer->wrappers['label']['container'] = Html::el('div')->class('col-sm-3 control-label');
    $renderer->wrappers['label']['requiredsuffix'] = " *";

    $druzstvo = $this->getParameter('id');

    $form->addText('prijmeni', 'Příjmení:')
      ->addRule($form::MAX_LENGTH, 'Maximální délka příjmení může být %d znaků', 100)
      ->addRule($form::FILLED, 'Zadejte příjmení hráče.')
      ->getControlPrototype()->class('form-control');

    $form->addText('jmeno', 'Jméno:')
      ->addRule($form::MAX_LENGTH, 'Maximální délka jména může být %d znaků', 100)
      ->addRule($form::FILLED, 'Zadejte jméno hráče.')
      ->getControlPrototype()->class('form-control');

    $form->addDatePicker('narozen', 'Datum narození:', 10)
      ->getControlPrototype()->class('form-control');

    $form->addSubmit('save', 'Uložit')->getControlPrototype()->class('btn btn-primary');
    $form->onSuccess[] = array($this, 'hracFormSubmitted');

    $form->addProtection('Vypršel ochranný časový limit, odešlete prosím formulář ještě jednou');
    return $form;
  }

  protected function createComponentClearForm(): Form
  {
    $form = new Form;
    $form->addSubmit('delete', 'Smazat')->getControlPrototype()->class('btn btn-primary');
    $form->addSubmit('cancel', 'Storno')->getControlPrototype()->class('btn btn-default');
    $form->onSuccess[] = array($this, 'clearFormSubmitted');

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
}
