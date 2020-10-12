<?php

namespace App\Presenters;

use App;
use Nette;

final class DiskuzePresenter extends BasePresenter
{

  /** @var Nette\Database\Connection */
  private $db;

  /** @var App\Model\Diskuze */
  private $diskuze;


  public function __construct(\Nette\Database\Connection $database, App\Model\Diskuze $diskuze)
  {
    $this->db = $database;
    $this->diskuze = $diskuze;
  }

  public function renderDefault($id)
  {
      $this->template->pageTitle = '„RB“VL - Diskuze';
      $this->template->pageHeading = 'Diskuze';
      $this->template->pageDesc = '„RB“VL - Diskuze';

    $diskuze = $this->diskuze->findAll();
    $pok = $this->db->query('SELECT * FROM diskuze');

    foreach ($diskuze as $row) {
      dump($row->id);
    }

    // foreach ($pok as $row) {
    //   dump($row->id);
    // }
  }
}
