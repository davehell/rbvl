<?php

require_once dirname(__FILE__) . '/BasePresenter.php';

class UzivatelePresenter extends BasePresenter
{
  /********************* view default *********************/

  public function renderDefault()
  {
    $this->template->pageTitle = '„RB“VL - Uživatelé';
    $this->template->pageHeading = 'Uživatelé';
    $this->template->pageDesc = '';

    $uzivatele = new Uzivatele;
    $this->template->rows = $uzivatele->findAll();
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
      $uzivatel = new Uzivatele;
      $row = $uzivatel->find($id)->fetch();
      if (!$row) {
        //throw new BadRequestException('Požadovaný záznam nenalezen.');
        $this->flashMessage('Požadovaný záznam neexistuje.', 'danger');
        $this->redirect('default');
      }
      unset($row['password']);
      $form->setDefaults($row);
    }
  }



  public function userFormSubmitted(AppForm $form)
  {
    if ($form['save']->isSubmittedBy()) {
      $id = (int) $this->getParam('id');
      $uzivatel = new Uzivatele;

      $values = $form->getValues();
      $values['password'] = sha1($values['password'].$values['username']);
      unset($values['password2']);

      if ($id > 0) { //edit
        try {
          $uzivatel->update($id, $values);
          $this->flashMessage('Uživatel byl úspěšně upraven.', 'success');
          $this->redirect('default');
        } catch (DibiException $e) {
          if($e->getCode() === 1062) {
            $this->flashMessage('Uživatel se stejným jménem už existuje. Zadejte jiné uživatelské jméno.', 'danger');
          }
          else {
            $this->flashMessage('Nastala chyba. Uživatel nebyl upraven.', 'danger');
            //$form->addError($e->getMessage());
          }
        }
      } else { //add
        try {
          $uzivatel->insert($values);
          $this->flashMessage('Uživatel byl úspěšně přidán.', 'success');
          $this->redirect('default');
        } catch (DibiException $e) {
          if($e->getCode() === 1062) {
            $this->flashMessage('Uživatel se stejným jménem už existuje. Zadejte jiné uživatelské jméno.', 'danger');
          }
          else {
            $this->flashMessage('Nastala chyba. Uživatel nebyl přidán.', 'danger');
            //$form->addError('');
          }
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
      $uzivatel = new Uzivatele;
      $user = Environment::getUser()->getIdentity();
      $id = $user->id;
      $row = $uzivatel->find($id)->fetch();
      if (!$row) {
        //throw new BadRequestException('Požadovaný záznam nenalezen.');
        $this->flashMessage('Požadovaný záznam neexistuje.', 'danger');
        $this->redirect('default');
      }
      unset($row['password']);
      $form->setDefaults($row);
    }
  }


  public function changeFormSubmitted(AppForm $form)
  {
    if ($form['save']->isSubmittedBy()) {
      $uzivatel = new Uzivatele;
      $user = Environment::getUser()->getIdentity();
      $id = $user->id;

      $values = $form->getValues();
      $values['password'] = sha1($values['password'].$values['username']);
      unset($values['password2']);

      try {
        $uzivatel->update($id, $values);
        $this->flashMessage('Změny byly úspěšně uloženy.', 'success');
        $this->redirect('default');
      } catch (DibiException $e) {
        if($e->getCode() === 1062) {
          $this->flashMessage('Uživatel se stejným jménem už existuje. Zadejte jiné uživatelské jméno.', 'danger');
        }
        else {
          $this->flashMessage('Nastala chyba. Uživatel nebyl upraven.', 'danger');
          $form->addError($e->getMessage());
        }
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
    $uzivatel = new Uzivatele;
    $this->template->uzivatel = $uzivatel->find($id)->fetch();
    if (!$this->template->uzivatel) {
      //throw new BadRequestException('Požadovaný záznam nenalezen.');
      $this->flashMessage('Požadovaný záznam neexistuje.', 'danger');
      $this->redirect('default');
    }
  }



  public function deleteFormSubmitted(AppForm $form)
  {
    if ($form['delete']->isSubmittedBy()) {
      $uzivatel = new Uzivatele;
      $uzivatel->delete($this->getParam('id'));
      $this->flashMessage('Uživatel byl úspěšně odebrán.', 'success');
    }

    $this->redirect('default');
  }


    /********************* facilities *********************/


  protected function createComponent($name)
  {
    switch ($name) {
    case 'userForm':
      $id = $this->getParam('id');
      $form = new AppForm($this, $name);
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

      $roles = array('--- Vyberte oprávnění ---', 'admin'=>'Administrátor', 'member'=>'Uživatel');
      $form->addSelect('role', 'Oprávnění:', $roles)->skipFirst()->getControlPrototype()->class('form-control');
      $form['role']->addRule(Form::FILLED, 'Vyberte oprávnění');

      $form->addSubmit('save', 'Uložit')->getControlPrototype()->class('btn btn-primary');
      $form->addSubmit('cancel', 'Storno')->setValidationScope(NULL)->getControlPrototype()->class('btn btn-default');
      $form->onSubmit[] = array($this, 'userFormSubmitted');

      $form->addProtection('Vypršel ochranný časový limit, odešlete prosím formulář ještě jednou');
      return;

    case 'changeForm':
      $form = new AppForm($this, $name);
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
      $form->onSubmit[] = array($this, 'changeFormSubmitted');

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

