<?php

require_once dirname(__FILE__) . '/BasePresenter.php';


class FotoPresenter extends BasePresenter
{
  public function actionDefault()
  {
      $this->template->pageTitle = '„RB“VL - Fotogalerie';
      $this->template->pageHeading = 'Fotogalerie';
      $this->template->pageDesc = 'Fotky z průběhu RBVL';

      $alba = new Alba;
      $this->template->alba = $alba->findAll()->fetchAll();
  }

  public function renderAlbum($id = 0)
  {
      $this->template->pageTitle = '„RB“VL - Fotogalerie';
      $this->template->pageHeading = 'Fotogalerie';
      $this->template->pageDesc = 'Fotky z průběhu RBVL';
      $this->template->scripts = array('lightbox');

      $fotky = new Fotky;
      $this->template->fotky = $fotky->findAllInAlbum($id)->fetchAll();
      $alba = new Alba;
      $this->template->album = $alba->find($id)->fetch();

  }

  public function renderAdd($id = 0)
  {
    $this->template->pageTitle = '„RB“VL - Fotogalerie - Nová fotka';
    $this->template->pageHeading = 'Nová fotka';
    $this->template->pageDesc = '„RB“VL - vložení nové fotky';
    $this->template->robots = 'noindex,noarchive';

    $form = $this->getComponent('fotoForm');
    $this->template->form = $form;

    $alba = new Alba;
    $this->template->album = $alba->find($id)->fetch();
    if (!$this->template->album) {
      //throw new BadRequestException('Požadovaný záznam nenalezen.');
      $this->flashMessage('Požadované album neexistuje.', 'error');
      $this->redirect('default');
    }
  }

  public function renderEdit($id = 0)
  {
    $this->template->pageTitle = '„RB“VL - Fotogalerie - Úprava fotky';
    $this->template->pageHeading = 'Úprava fotky';
    $this->template->pageDesc = '';
    $this->template->robots = 'noindex,noarchive';

    $form = $this->getComponent('fotoEditForm');
    $this->template->form = $form;

    if (!$form->isSubmitted()) {
      $fotky = new Fotky;
      $this->template->fotka = $fotky->find($id)->fetch();
      if (!$this->template->fotka) {
        //throw new BadRequestException('Požadovaný záznam nenalezen.');
        $this->flashMessage('Požadovaný záznam neexistuje.', 'error');
        $this->redirect('Foto:default');
      }
      $form->setDefaults($this->template->fotka);
    }
  }

	public function actionDownload($id)
	{
    $fotky = new Fotky;
    $fotka = $fotky->find($id)->fetch();
    if(!$fotka) {
      $this->flashMessage('Požadovaný záznam neexistuje.', 'error');
        $this->redirect('Foto:default');
    }

    try {
  	  $filedownload = new FileDownload;
      $filedownload->sourceFile = WWW_DIR . '/photos/' . $fotka->soubor;
      $filedownload->mimeType = "jpg";
      //$filedownload->contentDisposition = FileDownload::CONTENT_DISPOSITION_INLINE;
      $filedownload->download();
    } catch (Exception $e) {
      $this->flashMessage($e->getMessage(), 'error');
    }


    $this->redirect('Foto:album', $fotka->album);
	}
  
  public function fotoFormSubmitted(AppForm $form)
  {
    $newWidth = "";
    $newHeight = "";
    $newName = "";
    
    if ($form['save']->isSubmittedBy()) {
      $id = (int) $this->getParam('id');
      $fotky = new Fotky;

        $file = $form['file']->getValue();
        if ($file->isOK())
        {
          $contentType = $file->getContentType();
          if($contentType == "image/jpeg" || $contentType == "image/png" || $contentType == "image/gif")
          {
            //$fileName = WWW_DIR . "/photos/" . String::webalize($type, ".");
            //$file->move($fileName);
            $image = $file->getImage();
            $image->resize(1024, 1024);
            $newName = "p".time().".jpg";
            $image->save(WWW_DIR . "/photos/".$newName);
            $newWidth = $image->getWidth();
            $newHeight = $image->getHeight();
            $image->resize(150, 150);
            $image->save(WWW_DIR . "/photos/thumb_".$newName);
          }
          else {
            $form->addError("Vybraný soubor není fotografie.");
          }
        }
        else {
          $form->addError("Nahrání souboru se nepodařilo.");
        }
        
        try {
          $values = $form->getValues();
          $values["sirka"] = $newWidth;
          $values["vyska"] = $newHeight;
          $values["soubor"] = $newName;
          unset($values["file"]);
//Debug::dump($form->getValues());
          $fotky->insert($values);

          $this->flashMessage('Fotka byla úspěšně přidána.', 'success');
          //$this->redirect('default');
        } catch (DibiException $e) {
          $this->flashMessage('Nastala chyba. Fotka nebyla přidána.', 'error');
          $form->addError($e->getMessage());
//Debug::dump($e);
        }
    }
  }

  public function fotoEditFormSubmitted(AppForm $form)
  {
    if ($form['save']->isSubmittedBy()) {
      $id = (int) $this->getParam('id');
      $fotky = new Fotky;

        try {
          $fotky->update($id, $form->getValues());
          $fotka = $fotky->find($id)->fetch();
          $this->flashMessage('Fotka byla úspěšně upravena.', 'success');
          $this->redirect('Foto:album', array($fotka->album));
         } catch (DibiException $e) {
          $this->flashMessage('Nastala chyba. Fotka nebyla upravena.', 'error');
          $form->addError($e->getMessage());
        }
    }
  }

    /********************* view delete *********************/


  public function renderDelete($id = 0)
  {
    $this->template->pageTitle = '„RB“VL - Fotogalerie - Smazání fotky';
    $this->template->pageHeading = 'Smazání fotky';
    $this->template->pageDesc = '';
    $this->template->robots = 'noindex,noarchive';

    $this->template->form = $this->getComponent('deleteForm');
    $fotky = new Fotky;

    $this->template->fotka = $fotky->find($id)->fetch();
    if (!$this->template->fotka) {
      //throw new BadRequestException('Požadovaný záznam nenalezen.');
      $this->flashMessage('Požadovaný záznam neexistuje.', 'error');
      $this->redirect('default');
    }
  }



  public function deleteFormSubmitted(AppForm $form)
  {
    $fotky = new Fotky;
    $id = $this->getParam('id');
    $fotka = $fotky->find($id)->fetch();

    if ($form['delete']->isSubmittedBy()) {
      $fotky->delete($id);
      $this->flashMessage('Fotka byla úspěšně smazána.', 'success');
      if(file_exists(WWW_DIR . "/photos/".$fotka->soubor.".jpg"))
        unlink(WWW_DIR . "/photos/".$fotka->soubor.".jpg");
      if(file_exists(WWW_DIR . "/photos/".$fotka->soubor."_thumb.jpg"))
        unlink(WWW_DIR . "/photos/".$fotka->soubor."_thumb.jpg");
    }

    $this->redirect('Foto:album', $fotka->album);
  }

   /********************* facilities *********************/


  protected function createComponent($name)
  {
    switch ($name) {
    case 'fotoForm':
      $id = $this->getParam('id');

      $form = new AppForm($this, $name);
      $form->getElementPrototype()->class('form-horizontal');

      $renderer = $form->getRenderer();
      $renderer->wrappers['pair']['container'] = Html::el('div')->class('control-group');
      $renderer->wrappers['controls']['container'] = NULL;
      $renderer->wrappers['control']['container'] = Html::el('div')->class('controls');
      $renderer->wrappers['label']['container'] = NULL;
      $renderer->wrappers['label']['requiredsuffix'] = " *";

      $form->addText('popis', 'Popis fotky:', 40)
        ->addRule(Form::MAX_LENGTH, 'Maximální délka názvu alba může být %d znaků', 200)
        ->getControlPrototype()->class('span3');
      $form['popis']->getLabelPrototype()->class('control-label');

      $form->addFile('file', 'Cesta k souboru:')
        ->addRule(Form::FILLED, "Vyberte soubor")
        ->getLabelPrototype()->class('control-label');

      $form->addHidden('album')
        ->setValue($id);

      $form->addSubmit('save', 'Uložit')->getControlPrototype()->class('btn btn-primary');
      $form->onSubmit[] = array($this, 'fotoFormSubmitted');

      $form->addProtection('Vypršel ochranný časový limit, odešlete prosím formulář ještě jednou');
      return;

    case 'fotoEditForm':
      $form = new AppForm($this, $name);

      $renderer = $form->getRenderer();
      $renderer->wrappers['label']['requiredsuffix'] = " *";

      $form->addText('popis', 'Popis fotky:', 40)
        ->addRule(Form::MAX_LENGTH, 'Maximální délka názvu alba může být %d znaků', 200);


      $form->addSubmit('save', 'Uložit')->getControlPrototype()->class('btn');
      $form->onSubmit[] = array($this, 'fotoEditFormSubmitted');

      $form->addProtection('Vypršel ochranný časový limit, odešlete prosím formulář ještě jednou');
      return;

    case 'deleteForm':
      $form = new AppForm($this, $name);
      $form->addSubmit('delete', 'Smazat')->getControlPrototype()->class('btn btn-primary');
      $form->addSubmit('cancel', 'Zrušit')->getControlPrototype()->class('btn');
      $form->onSubmit[] = array($this, 'deleteFormSubmitted');

      $form->addProtection('Vypršel ochranný časový limit, odešlete prosím formulář ještě jednou');
      return;

    default:
      parent::createComponent($name);
    }
  }
}
