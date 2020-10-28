<?php

namespace App\Presenters;

use App,
    Nette\Application\UI\Form,
    Nette\Utils\Html,
    App\Components\PaginationControl,
    App\Components\IPaginationControlFactory;

final class DiskuzePresenter extends BasePresenter
{
  /** @var App\Model\Diskuze */
  private $diskuze;

  /** @var App\Components\PaginationControl */
  private $paginationControlFactory;

  public function __construct(App\Model\Diskuze $diskuze, IPaginationControlFactory $paginationControlFactory)
  {
    parent::__construct();
    $this->diskuze = $diskuze;
    $this->paginationControlFactory = $paginationControlFactory;
  }

  public function renderDefault($id)
  {
      $this->template->pageTitle = '„RB“VL - Diskuze';
      $this->template->pageHeading = 'Diskuze';
      $this->template->pageDesc = '„RB“VL - Diskuze';

      $rows = $this->diskuze->findAllDateSorted();

      $paginator = $this->getComponent('pagination')->getPaginator();
      $paginator->setItemCount(count($rows));
      $paginator->setItemsPerPage(30);
      $paginator->setPage($this->getParameter('page', 1));

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
      $id = (int) $this->getParam('id');
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
          $this->diskuze->insert($values);
          $this->flashMessage('Příspěvek byl úspěšně přidán.', 'success');
          $this->redirect('default');
        } catch (\Nette\Database\DriverException $e) {
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
      $row = $this->diskuze->get($this->getParam('id'));
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


  protected function createComponent($name)
  {
    switch ($name) {
      case 'pagination':
        return new PaginationControl( $this->getHttpRequest() );

    case 'diskuzeForm':
      $id = $this->getParam('id');
      $form = new Form($this, $name);
      $form->getElementPrototype()->class('form-horizontal');

      $renderer = $form->getRenderer();
      $renderer->wrappers['pair']['container'] = Html::el('div')->class('form-group');
      $renderer->wrappers['controls']['container'] = NULL;
      $renderer->wrappers['control']['container'] = Html::el('div')->class('col-sm-9');
      $renderer->wrappers['label']['container'] = Html::el('div')->class('col-sm-3 control-label');
      $renderer->wrappers['label']['requiredsuffix'] = " *";


      $form->addText('jmeno', 'Jméno:', 30)
        ->addRule(Form::FILLED, 'Zadejte uživatelské jméno')
        ->getControlPrototype()->class('form-control');

      $form->addTextArea('text', 'Text:', 0, 20)
        ->addRule(Form::FILLED, 'Zadejte text příspěvku.')
        ->getControlPrototype()->class('form-control');

      $form->addText('antiSpam', 'Ochrana proti spamu:  Kolik je dvakrát tři? (výsledek napište číslem)', 10)
        ->setOmitted()
        ->addRule(Form::FILLED, 'Vyplňte ochranu proti spamu')
        ->addRule(Form::NUMERIC, 'Špatně vyplněná ochrana proti spamu')
        ->addRule(Form::RANGE, 'Špatně vyplněná ochrana proti spamu', array(6, 6))
        ->getControlPrototype()->class('antispam');
      $form['antiSpam']->getLabelPrototype()->class('antispam');

      $form->addSubmit('save', 'Odeslat')->getControlPrototype()->class('btn btn-primary');
      $form->onSuccess[] = array($this, 'diskuzeFormSubmitted');

      $form->addProtection('Vypršel ochranný časový limit, odešlete prosím formulář ještě jednou');
      return;

    case 'deleteForm':
      $form = new Form($this, $name);
      $form->addSubmit('delete', 'Smazat')->getControlPrototype()->class('btn btn-primary');
      $form->addSubmit('cancel', 'Storno')->getControlPrototype()->class('btn btn-default');
      $form->onSuccess[] = array($this, 'deleteFormSubmitted');

      $form->addProtection('Vypršel ochranný časový limit, odešlete prosím formulář ještě jednou');
      return;

    default:
      parent::createComponent($name);
    }
  }
}
