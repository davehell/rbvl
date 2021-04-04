<?php

namespace App\Presenters;

use App,
    Nette\Database,
    Nette\Application\UI\Form,
    Nette\Utils\Html,
    App\Components\PaginationControl;

final class DiskuzePresenter extends BasePresenter
{
  /** @var App\Model\Diskuze */
  private $diskuze;

  public function __construct(App\Model\Diskuze $diskuze)
  {
    parent::__construct();
    $this->diskuze = $diskuze;
  }

  public function renderDefault(int $page = 1): void
  {
      $this->template->pageTitle = '„RB“VL - Diskuze';
      $this->template->pageHeading = 'Diskuze';
      $this->template->pageDesc = '„RB“VL - Diskuze';

      $rows = $this->diskuze->findAllDateSorted();

      $paginator = $this->getComponent('pagination')->getPaginator();
      $paginator->setItemCount(count($rows));
      $paginator->setItemsPerPage($this->itemsPerPage);
      $paginator->setPage($page);

      $rows->limit($paginator->getLength(), $paginator->getOffset());

      $this->template->rows = $rows;
  }


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
      $row = $this->diskuze->get($id);
      if (!$row) {
        //throw new BadRequestException('Požadovaný záznam nenalezen.');
        $this->flashMessage('Požadovaný záznam neexistuje.', 'danger');
        $this->redirect('default');
      }
      $form->setDefaults($row);
    }
  }

  public function diskuzeFormSubmitted(Form $form)
  {
    if ($form['save']->isSubmittedBy()) {
      $id = (int) $this->getParameter('id');
      $values = $form->getValues();

      if ($id > 0) { //edit
        $row = $this->diskuze->get($id);
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
          $values['id'] = 70;
          $this->diskuze->insert($values);
          $this->flashMessage('Příspěvek byl úspěšně přidán.', 'success');
          $this->redirect('default');
        } catch (Database\DriverException $e) {
          $this->flashMessage('Nastala chyba. Příspěvek nebyl vložen.', 'danger');
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

    $row = $this->diskuze->get($id);
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
      $row = $this->diskuze->get($this->getParameter('id'));
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

  protected function createComponentDiskuzeForm(): Form
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

    $form->addText('jmeno', 'Jméno:')
      ->setRequired('Zadejte Vaše jméno')
      ->getControlPrototype()->class('form-control');

    $form->addTextArea('text', 'Text:', 0, 20)
      ->addRule($form::FILLED, 'Zadejte text příspěvku.')
      ->getControlPrototype()->class('form-control');

    $form->addText('antiSpam', 'Ochrana proti spamu:  Kolik je dvakrát tři? (výsledek napište číslem)', 10)
      ->setOmitted()
      ->addRule($form::FILLED, 'Vyplňte ochranu proti spamu')
      ->addRule($form::NUMERIC, 'Špatně vyplněná ochrana proti spamu')
      ->addRule($form::RANGE, 'Špatně vyplněná ochrana proti spamu', array(6, 6))
      ->getControlPrototype()->class('antispam');
    $form['antiSpam']->getLabelPrototype()->class('antispam');

    $form->addSubmit('save', 'Odeslat')->getControlPrototype()->class('btn btn-primary');
    $form->onSuccess[] = array($this, 'diskuzeFormSubmitted');

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
    return new PaginationControl( $this->getHttpRequest(), $this->radius );
  }
}
