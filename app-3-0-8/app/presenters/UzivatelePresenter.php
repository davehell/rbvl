<?php
namespace App\Presenters;

use App,
    Nette\Application\UI\Form,
    Nette\Utils\Html;

final class UzivatelePresenter extends BasePresenter
{
  /** @var App\Model\Uzivatele */
  private $uzivatele;

  public function __construct(App\Model\Uzivatele $uzivatele)
  {
    parent::__construct();
    $this->uzivatele = $uzivatele;
  }

  /********************* view default *********************/

  public function renderDefault()
  {
    $this->template->pageTitle = '„RB“VL - Uživatelé';
    $this->template->pageHeading = 'Uživatelé';
    $this->template->pageDesc = '';

    $this->template->rows = $this->uzivatele->findAll();
  }


    /********************* views add & edit *********************/


  public function renderAdd()
  {
    $this->template->pageTitle = '„RB“VL - Uživatelé - Nový uživatel';
    $this->template->pageHeading = 'Nový uživatel';
    $this->template->pageDesc = '';
    $this->template->robots = 'noindex,noarchive';

    $form = $this->getComponent('userForm');
    $form['save']->caption = 'OK';
    $this->template->form = $form;
  }



  public function renderEdit($id = 5)
  {
    $this->template->pageTitle = '„RB“VL - Uživatelé - Úprava uživatele';
    $this->template->pageHeading = 'Úprava uživatele';
    $this->template->pageDesc = '';
    $this->template->robots = 'noindex,noarchive';

    $form = $this->getComponent('userForm');
    $this->template->form = $form;

    if (!$form->isSubmitted()) {
      $row = $this->uzivatele->get($id);
      if (!$row) {
        //throw new BadRequestException('Požadovaný záznam nenalezen.');
        $this->flashMessage('Požadovaný záznam neexistuje.', 'danger');
        $this->redirect('default');
      }
      $form->setDefaults($row);
    }
  }



  public function userFormSubmitted(Form $form)
  {
    if ($form['save']->isSubmittedBy()) {
      $id = (int) $this->getParam('id');

      $values = $form->getValues();
      $values['password'] = sha1($values['password'].$values['username']);

      if ($id > 0) { //edit
        $row = $this->uzivatele->get($id);

        if($row) {
          try {
            $row->update($values);
            $this->flashMessage('Změny byly úspěšně uloženy.', 'success');
            $this->redirect('default');
          } catch (\Nette\Database\UniqueConstraintViolationException $e) {
            $this->flashMessage('Uživatel se stejným jménem už existuje. Zadejte jiné uživatelské jméno.', 'danger');
          } catch (\Nette\Database\DriverException $e) {
            $this->flashMessage('Nastala chyba. Uživatel nebyl upraven.', 'danger');
          }
        }
        else {
          $this->error();
        }
      } else { //add
          try {
            $this->uzivatele->insert($values);
            $this->flashMessage('Uživatel byl úspěšně přidán.', 'success');
            $this->redirect('default');
          } catch (\Nette\Database\UniqueConstraintViolationException $e) {
            $this->flashMessage('Uživatel se stejným jménem už existuje. Zadejte jiné uživatelské jméno.', 'danger');
          } catch (\Nette\Database\DriverException $e) {
            $this->flashMessage('Nastala chyba. Uživatel nebyl přidán.', 'danger');
          }
      }
    }
    else if ($form['cancel']->isSubmittedBy()) {
        $this->redirect('default');
    }
  }


    /********************* view change *********************/


  public function renderChange()
  {
    $this->template->pageTitle = '„RB“VL - Uživatelé - Změna hesla';
    $this->template->pageHeading = 'Změna uživatelského jména a hesla';
    $this->template->pageDesc = '';
    $this->template->robots = 'noindex,noarchive';

    $form = $this->getComponent('changeForm');
    $this->template->form = $form;

    if (!$form->isSubmitted()) {
      $id = $this->user->getIdentity()->id;
      $row = $this->uzivatele->get($id);
      if (!$row) {
        $this->flashMessage('Požadovaný záznam neexistuje.', 'danger');
        $this->redirect('default');
      }
      $form->setDefaults($row);
    }
  }


  public function changeFormSubmitted(Form $form)
  {
    if ($form['save']->isSubmittedBy()) {
      $id = $this->user->getIdentity()->id;

      $values = $form->getValues();
      $values['password'] = sha1($values['password'].$values['username']);

      $row = $this->uzivatele->get($id);

      if($row) {
        try {
          $row->update($values);
          $this->flashMessage('Změny byly úspěšně uloženy.', 'success');
          $this->redirect('default');
        } catch (\Nette\Database\UniqueConstraintViolationException $e) {
          $this->flashMessage('Uživatel se stejným jménem už existuje. Zadejte jiné uživatelské jméno.', 'danger');
        } catch (\Nette\Database\DriverException $e) {
          $this->flashMessage('Nastala chyba. Uživatel nebyl upraven.', 'danger');
        }
      }
      else {
        $this->error();
      }
    }
    else if ($form['cancel']->isSubmittedBy()) {
      $this->redirect('default');
    }
  }

    /********************* view delete *********************/


  public function renderDelete($id = 0)
  {
    $this->template->pageTitle = '„RB“VL - Uživatelé - Odebrat uživatele';
    $this->template->pageHeading = 'Odebrat uživatele';
    $this->template->pageDesc = '';

    $this->template->form = $this->getComponent('deleteForm');

    $row = $this->uzivatele->get($id);
    $this->template->uzivatel = $row;

    if (!$row) {
      //throw new BadRequestException('Požadovaný záznam nenalezen.');
      $this->flashMessage('Požadovaný záznam neexistuje.', 'danger');
      $this->redirect('default');
    }
  }



  public function deleteFormSubmitted(Form $form)
  {
    if ($form['delete']->isSubmittedBy()) {
      $row = $this->uzivatele->get($this->getParam('id'));
      if($row) {
        $row->delete();
        $this->flashMessage('Uživatel byl úspěšně smazán.', 'success');
        $this->redirect('default');
      }
      else {
        $this->error();
      }
    }

    $this->redirect('default');
  }


    /********************* facilities *********************/
  protected function createComponentUserForm(): Form
  {
    $id = $this->getParam('id');
    $form = new Form;
    $form->getElementPrototype()->class('form-horizontal');

    $renderer = $form->getRenderer();
    $renderer->wrappers['pair']['container'] = Html::el('div')->class('form-group');
    $renderer->wrappers['controls']['container'] = NULL;
    $renderer->wrappers['control']['container'] = Html::el('div')->class('col-sm-9');
    $renderer->wrappers['label']['container'] = Html::el('div')->class('col-sm-3 control-label');
    $renderer->wrappers['label']['requiredsuffix'] = " *";

    $form->addText('username', 'Uživatelské jméno:', 30)
      ->addRule(Form::FILLED, 'Zadejte uživatelské jméno')
      ->getControlPrototype()->class('form-control');

    $form->addPassword('password', 'Heslo:', 30)
      ->addRule(Form::FILLED, 'Zvolte si heslo')
      ->addRule(Form::MIN_LENGTH, 'Zadané heslo je příliš krátké, zvolte si heslo alespoň o %d znacích', 3)
      ->getControlPrototype()->class('form-control');

    $form->addPassword('password2', 'Heslo pro kontrolu:', 30)
      ->setOmitted()
      ->addRule(Form::FILLED, 'Zadejte heslo ještě jednou pro kontrolu')
      ->addRule(Form::EQUAL, 'Zadané hesla se neshodují', $form['password'])
      ->getControlPrototype()->class('form-control');

    $roles = array('--- Vyberte oprávnění ---', 'admin'=>'Administrátor', 'member'=>'Uživatel');
    $form->addSelect('role', 'Oprávnění:', $roles)->getControlPrototype()->class('form-control');
    $form['role']->addRule(Form::FILLED, 'Vyberte oprávnění');

    $form->addSubmit('save', 'Uložit')->getControlPrototype()->class('btn btn-primary');
    $form->addSubmit('cancel', 'Storno')->setValidationScope(NULL)->getControlPrototype()->class('btn btn-default');
    $form->onSuccess[] = array($this, 'userFormSubmitted');

    $form->addProtection('Vypršel ochranný časový limit, odešlete prosím formulář ještě jednou');
    return $form;
  }

  protected function createComponentChangeForm(): Form
  {
    $form = new Form;
    $form->getElementPrototype()->class('form-horizontal');

    $renderer = $form->getRenderer();
    $renderer->wrappers['pair']['container'] = Html::el('div')->class('form-group');
    $renderer->wrappers['controls']['container'] = NULL;
    $renderer->wrappers['control']['container'] = Html::el('div')->class('col-sm-9');
    $renderer->wrappers['label']['container'] = Html::el('div')->class('col-sm-3 control-label');
    $renderer->wrappers['label']['requiredsuffix'] = " *";

    $form->addText('username', 'Uživatelské jméno:', 30)
        ->addRule(Form::FILLED, 'Zadejte uživatelské jméno')
        ->getControlPrototype()->class('form-control');

    $form->addPassword('password', 'Heslo:', 30)
        ->addRule(Form::FILLED, 'Zvolte si heslo')
        ->addRule(Form::MIN_LENGTH, 'Zadané heslo je příliš krátké, zvolte si heslo alespoň o %d znacích', 3)
        ->getControlPrototype()->class('form-control');

    $form->addPassword('password2', 'Heslo pro kontrolu:', 30)
        ->addRule(Form::FILLED, 'Zadejte heslo ještě jednou pro kontrolu')
        ->addRule(Form::EQUAL, 'Zadané hesla se neshodují', $form['password'])
        ->getControlPrototype()->class('form-control');

    $form->addSubmit('save', 'Uložit')->getControlPrototype()->class('btn btn-primary');
    $form->addSubmit('cancel', 'Storno')->setValidationScope(NULL)->getControlPrototype()->class('btn btn-default');
    $form->onSuccess[] = array($this, 'changeFormSubmitted');

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

