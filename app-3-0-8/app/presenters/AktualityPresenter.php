<?php

namespace App\Presenters;

use App,
    Nette\Application\UI\Form,
    Nette\Utils\Html,
    App\Components\PaginationControl,
    App\Components\IPaginationControlFactory;

final class AktualityPresenter extends BasePresenter
{
  /** @var App\Model\Aktuality */
  private $aktuality;

  /** @var App\Components\PaginationControl */
  private $paginationControlFactory;

  public function __construct(App\Model\Aktuality $aktuality, IPaginationControlFactory $paginationControlFactory)
  {
    parent::__construct();
    $this->aktuality = $aktuality;
    $this->paginationControlFactory = $paginationControlFactory;
  }

  public function renderDefault($id)
  {
    $this->template->pageTitle = '„RB“VL - Novinky';
    $this->template->pageHeading = 'Novinky';
    $this->template->pageDesc = 'Novinky z „RB“VL';

    $rows = $this->aktuality->findAllDateSorted();

    $paginator = $this->getComponent('pagination')->getPaginator();
    $paginator->setItemCount(count($rows));
    $paginator->setItemsPerPage($this->itemsPerPage);
    $paginator->setPage($this->getParameter('page', 1));

    $rows->limit($paginator->getLength(), $paginator->getOffset());

    $this->template->rows = $rows;
  }

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
      $row = $this->aktuality->get($id);
      if (!$row) {
        //throw new BadRequestException('Požadovaný záznam nenalezen.');
        $this->flashMessage('Požadovaný záznam neexistuje.', 'danger');
        $this->redirect('default');
      }
      $form->setDefaults($row);
    }
  }

  public function aktualityFormSubmitted(Form $form)
  {
    if ($form['save']->isSubmittedBy()) {
      $id = (int) $this->getParameter('id');
      $values = $form->getValues();

      if ($id > 0) { //edit
        $row = $this->aktuality->get($id);

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
          $values['vlozeno'] = time();
          $this->aktuality->insert($values);
          $this->flashMessage('Aktualita byla úspěšně přidána.', 'success');
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
    $this->template->pageTitle = '„RB“VL - Aktuality - Smazání příspěvku';
    $this->template->pageHeading = 'Smazání příspěvku';
    $this->template->pageDesc = '';
    $this->template->robots = 'noindex,noarchive';

    $this->template->form = $this->getComponent('deleteForm');

    $row = $this->aktuality->get($id);
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
      $row = $this->aktuality->get($this->getParameter('id'));
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

  protected function createComponentAktualityForm(): Form
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

    $form->addTextArea('text', 'Text:', 0, 20)
      ->addRule(Form::FILLED, 'Zadejte text příspěvku.')
      ->getControlPrototype()->class('form-control');


    $form->addSubmit('save', 'Odeslat')->getControlPrototype()->class('btn btn-primary');
    $form->onSuccess[] = array($this, 'aktualityFormSubmitted');

    $form->addProtection('Vypršel ochranný časový limit, odešlete prosím formulář ještě jednou');
    return $form;
  }

  protected function createComponentDeleteForm(): Form
  {
    $form = new Form;
    $form->addSubmit('delete', 'Smazat')->getControlPrototype()->class('btn btn-primary');
    $form->addSubmit('cancel', 'Storno')->getControlPrototype()->class('btn btn-default');;
    $form->onSuccess[] = array($this, 'deleteFormSubmitted');

    $form->addProtection('Vypršel ochranný časový limit, odešlete prosím formulář ještě jednou');
    return $form;
  }

  protected function createComponentPagination()
  {
    return new PaginationControl( $this->getHttpRequest(), $this->radius );
  }
}
