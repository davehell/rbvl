<?php

require_once dirname(__FILE__) . '/BasePresenter.php';


class RozlosovaniPresenter extends BasePresenter
{

  public function actionDefault($id = 0)
  {
      $this->template->pageTitle = '„RB“VL - Rozlosování';
      $this->template->pageHeading = 'Rozlosování';
      $this->template->pageDesc = 'Rozlosování zápasů „Region Beskydy“ volejbalové ligy';
      $this->template->scripts = array('vysledky');

      $rozlosovani = new Rozlosovani;
      $terminy = new Terminy;
      $druzstva = new Druzstva;

      $this->template->druzstva = $druzstva->findAllUnique($this->R, array('nazev' => 'asc'));
      $this->template->terminy = $terminy->findAll($this->R)->fetchAll();
      if($id) {
        $this->template->rozlosovani = $rozlosovani->findAllInTermin($id)->fetchAll();
      }
      else {
        $this->template->rozlosovani = null;
      }

      $this->template->rocnikPopis = '';
      $this->template->terminPopis = '';
      $this->template->cas = '';
      $this->template->barva = 1;
  }


}
