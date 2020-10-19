<?php

namespace App\Presenters;

use Dibi;
use App;

final class AktualityPresenter extends BasePresenter
{
/*

  private $db;


  private $aktuality;

  public function __construct(Dibi\Connection $db, App\Model\Aktuality $aktuality)
  {
    $this->db = $db;
    $this->aktuality = $aktuality;
  }
*/
  public function renderDefault($id)
  {
      $this->template->pageTitle = '„RB“VL - Novinky';
      $this->template->pageHeading = 'Novinky';
      $this->template->pageDesc = 'Novinky z „RB“VL';
/*
    $jedna = $this->aktuality->find(232);
    \Tracy\Debugger::dump($jedna->text);
    $aktuality = $this->aktuality->findAll();
    $pok = $this->db->query('SELECT * FROM aktuality');

    $aktuality->dump();
    foreach ($aktuality as $row) {
      dump($row->id);
    }

    foreach ($pok as $row) {
      dump($row->id);
    }
*/
  }
}
