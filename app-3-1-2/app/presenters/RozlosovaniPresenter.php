<?php

namespace App\Presenters;

use App,
    Nette\Application\UI\Form,
    Nette\Utils\Html;


final class RozlosovaniPresenter extends BasePresenter
{
  /** @var App\Model\Rozlosovani */
  private $rozlosovani;

  /** @var App\Model\Terminy */
  private $terminy;

  /** @var App\Model\Druzstva */
  private $druzstva;

  public function __construct(App\Model\Rozlosovani $rozlosovani, App\Model\Terminy $terminy, App\Model\Druzstva $druzstva)
  {
    parent::__construct();
    $this->rozlosovani = $rozlosovani;
    $this->terminy = $terminy;
    $this->druzstva = $druzstva;
  }

  public function actionDefault($id = 0)
  {
      $this->template->pageTitle = '„RB“VL - Rozlosování';
      $this->template->pageHeading = 'Rozlosování';
      $this->template->pageDesc = 'Rozlosování zápasů „Region Beskydy“ volejbalové ligy';
      $this->template->scripts = array('vysledky');

      $this->template->rozlosovani = [];

      $this->template->druzstva = $this->druzstva->findAllUnique($this->R, array("nazev" => true));
      $this->template->terminy = $this->terminy->findAllInRocnik($this->R);

      $this->template->rozlosovani = $this->rozlosovani->findAllInTermin($id);
      if (!$this->template->rozlosovani) {
        $this->error();
      }



      $this->template->rocnikPopis = '';
      $this->template->terminPopis = '';
      $this->template->cas = '';
      $this->template->barva = 1;
  }
}
