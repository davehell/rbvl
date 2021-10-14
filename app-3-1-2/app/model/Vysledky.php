<?php

namespace App\Model;


final class Vysledky extends BaseModel
{
    protected $table = "vysledky";

    public function findAllInRocnik($rocnik)
    {
        return $this->database->query('
          SELECT
                  v.id, v.id_zapasu,v.domaci AS domaci_id, v.hoste AS hoste_id, sety_domaci, sety_hoste, mice1_domaci, mice1_hoste, mice2_domaci, mice2_hoste, mice3_domaci, mice3_hoste, kontumace_domaci, kontumace_hoste,
                  td.nazev AS domaci_nazev,
                  th.nazev AS hoste_nazev,
                  r.datum AS datum, r.cas,
                  t.popis AS termin,
                  s.popis AS skupina
          FROM
                  vysledky AS v
          LEFT JOIN
                  druzstva AS td ON (v.domaci=td.id)
          LEFT JOIN
                  druzstva AS th ON (v.hoste=th.id)
          LEFT JOIN
                  rozlosovani AS r ON (r.id=v.id_zapasu)
          LEFT JOIN
                  terminy AS t ON (r.datum=t.datum)
          LEFT JOIN
                  skupiny AS s ON (r.skupina=s.id)
          LEFT JOIN
                  rocniky AS roc ON (roc.id=t.rocnik)
          WHERE
                  roc.rocnik = ?
          ORDER BY
                  r.datum ASC,
                  s.id ASC,
                  v.id_zapasu ASC
        ', $rocnik)->fetchAll();
    }

    public function findAllInTermin($termin)
    {
        return $this->database->query('
          SELECT v.id, v.id_zapasu,v.domaci AS domaci_id, v.hoste AS hoste_id, v.poznamka, sety_domaci, sety_hoste, mice1_domaci, mice1_hoste, mice2_domaci, mice2_hoste, mice3_domaci, mice3_hoste, kontumace_domaci, kontumace_hoste,
                  td.nazev AS domaci_nazev,
                  th.nazev AS hoste_nazev,
                  r.datum AS datum, r.cas,
                  t.popis AS termin,
                  s.popis AS skupina
          FROM
                  vysledky AS v
          LEFT JOIN
                  druzstva AS td ON (v.domaci=td.id)
          LEFT JOIN
                  druzstva AS th ON (v.hoste=th.id)
          LEFT JOIN
                  rozlosovani AS r ON (r.id=v.id_zapasu)
          LEFT JOIN
                  terminy AS t ON (r.datum=t.datum)
          LEFT JOIN
                  skupiny AS s ON (r.skupina=s.id)
          LEFT JOIN
                  rocniky AS roc ON (roc.id=t.rocnik)
          WHERE
                  t.id = ?
          ORDER BY
                  r.datum ASC,
                  s.id ASC,
                  v.id_zapasu ASC
        ', $termin)->fetchAll();
    }

    public function findDruzstvo($skupina, $druzstvo)
    {
        return $this->database->query('
          SELECT
                  v.*
          FROM
                  rozlosovani AS r
          LEFT JOIN
                  vysledky AS v ON (r.id=v.id_zapasu)
          WHERE
                  r.skupina = ?
          AND
                  (v.domaci=? OR v.hoste=?)
          AND
                  (v.sety_domaci is not null AND v.sety_hoste is not null)
          ORDER BY
                  v.id_zapasu ASC
        ', $skupina, $druzstvo, $druzstvo)->fetchAll();
    }

    public function getVysledek($id)
    {
        return $this->database->query('
          SELECT
                  v.id, v.id_zapasu, v.domaci AS id_domaci, v.hoste AS id_hoste, sety_domaci, sety_hoste, mice1_domaci, mice1_hoste, mice2_domaci, mice2_hoste, mice3_domaci, mice3_hoste, kontumace_domaci, kontumace_hoste,
                  td.nazev AS domaci_nazev,
                  th.nazev AS hoste_nazev,
                  r.datum AS datum, r.cas, r.skupina as id_skupina,
                  t.id AS termin, t.popis AS termin_popis,
                  s.popis AS skupina
          FROM
                  vysledky AS v
          LEFT JOIN
                  druzstva AS td ON (v.domaci=td.id)
          LEFT JOIN
                  druzstva AS th ON (v.hoste=th.id)
          LEFT JOIN
                  rozlosovani AS r ON (r.id=v.id_zapasu)
          LEFT JOIN
                  terminy AS t ON (r.datum=t.datum)
          LEFT JOIN
                  skupiny AS s ON (r.skupina=s.id)
          WHERE
                  v.id = ?
        ', $id)->fetch();
    }

  public function updateVysledek($id, $data)
  {
    return $this->database->table($this->table)->where("id", $id)->update($data);
  }

  public function spocitejTabulku($skupina, $R, $tymy, $vysledky, $tabulky)
  {
    $druzstva = $tymy->findAllInSkupina($skupina, $R);

    foreach($druzstva as $druzstvo) {
      $values['sety_dal'] = 0;
      $values['sety_dostal'] = 0;
      $values['mice_dal'] = 0;
      $values['mice_dostal'] = 0;
      $values['body'] = 0;
      $values['c'] = 0; 
      $values['v'] = 0; 
      $values['vt'] = 0; 
      $values['r'] = 0;
      $values['pt'] = 0; 
      $values['p'] = 0; 
      $values['pk'] = 0;

      $zapasy = $vysledky->findDruzstvo($skupina, $druzstvo->id);

      foreach($zapasy as $zapas) {
        $values['c']++;
        if($zapas->domaci == $druzstvo->id) { //pocitane druzstvo hralo jako domaci
          $values['sety_dal'] += $zapas->sety_domaci;
          $values['sety_dostal'] += $zapas->sety_hoste;
          $values['mice_dal'] += $zapas->mice1_domaci;
          $values['mice_dal'] += $zapas->mice2_domaci;
          $values['mice_dal'] += $zapas->mice3_domaci;
          $values['mice_dostal'] += $zapas->mice1_hoste;
          $values['mice_dostal'] += $zapas->mice2_hoste;
          $values['mice_dostal'] += $zapas->mice3_hoste;
/*
na dva vítězné sety
          if($zapas->sety_domaci == 2 && $zapas->sety_hoste == 0) { //vyhra 2:0
            $values['v']++;
            $values['body'] += 3;
          }
          else if($zapas->sety_domaci == 2 && $zapas->sety_hoste == 1) { //vyhra 2:1
            $values['vt']++;
            $values['body'] += 2;
          }
          else if($zapas->sety_domaci == 1 && $zapas->sety_hoste == 2) { //prohra 1:2
            $values['pt']++;
            $values['body'] += 1;
          }
          else if($zapas->sety_domaci == 0 && $zapas->sety_hoste == 2) { //prohra 0:2
            $values['p']++;
            $values['body'] += 0;
          }
 */
          if($zapas->sety_domaci == 2 && $zapas->sety_hoste == 0) { //vyhra 2:0
            $values['v']++;
            $values['body'] += 2;
          }
          else if($zapas->sety_domaci == 1 && $zapas->sety_hoste == 1) { //remiza 1:1
            $values['r']++;
            $values['body'] += 1;
          }
          else if($zapas->sety_domaci == 0 && $zapas->sety_hoste == 2) { //prohra 0:2
            $values['p']++;
            $values['body'] += 0;
          }
          if($zapas->kontumace_domaci) { //kontumacni prohra
            $values['pk']++;
            $values['body']--;
          }
        }//end druzstvo jako domaci
        else if($zapas->hoste == $druzstvo->id) { //pocitane druzstvo hralo jako hostujici
          $values['sety_dal'] += $zapas->sety_hoste;
          $values['sety_dostal'] += $zapas->sety_domaci;
          $values['mice_dal'] += ($zapas->mice1_hoste + $zapas->mice2_hoste + $zapas->mice3_hoste);
          $values['mice_dostal'] += ($zapas->mice1_domaci + $zapas->mice2_domaci + $zapas->mice3_domaci);
/*
na dva vítězné sety
          if($zapas->sety_domaci == 0 && $zapas->sety_hoste == 2) { //vyhra 2:0
            $values['v']++;
            $values['body'] += 3;
          }
          else if($zapas->sety_domaci == 1 && $zapas->sety_hoste == 2) { //vyhra 2:1
            $values['vt']++;
            $values['body'] += 2;
          }
          else if($zapas->sety_domaci == 2 && $zapas->sety_hoste == 1) { //prohra 1:2
            $values['pt']++;
            $values['body'] += 1;
          }
          else if($zapas->sety_domaci == 2 && $zapas->sety_hoste == 0) { //prohra 0:2
            $values['p']++;
            $values['body'] += 0;
          }
*/
          if($zapas->sety_domaci == 0 && $zapas->sety_hoste == 2) { //vyhra 2:0
            $values['v']++;
            $values['body'] += 2;
          }
          else if($zapas->sety_domaci == 1 && $zapas->sety_hoste == 1) { //remiza 1:1
            $values['r']++;
            $values['body'] += 1;
          }
          else if($zapas->sety_domaci == 2 && $zapas->sety_hoste == 0) { //prohra 0:2
            $values['p']++;
            $values['body'] += 0;
          }
          if($zapas->kontumace_hoste) { //kontumacni prohra
            $values['pk']++;
            $values['body']--;
          }
        }//end druzstvo jako HOSTE
      }//foreach zapasy
      $tabulky->updateTabulka($skupina, $druzstvo->id, $values);
    }// foreach druzstva

  }

}
