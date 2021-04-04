<?php

namespace App\Model;


final class Terminy extends BaseModel
{
    protected $table = "terminy";

    public function findAllInRocnik($rocnik)
    {
        return $this->database->query('
            SELECT terminy.datum, terminy.popis as termin, terminy.id as id, rocniky.popis as rocnik, rocniky.id as id_rocnik
            FROM terminy
            LEFT JOIN rocniky on (terminy.rocnik = rocniky.id)
            RIGHT JOIN rozlosovani on (terminy.datum = rozlosovani.datum)
            WHERE rocniky.rocnik = ?
            AND rocniky.aktualni = 1
            GROUP BY terminy.datum
            ORDER BY terminy.rocnik ASC, datum ASC
        ', $rocnik)->fetchAll();
    }

    public function findAllVysledky($rocnik)
    {
        return $this->database->query('
            SELECT terminy.datum, terminy.popis as termin, terminy.id as id, rocniky.popis as rocnik, rocniky.id as id_rocnik
            FROM terminy
            LEFT JOIN rocniky on (terminy.rocnik = rocniky.id)
            RIGHT JOIN rozlosovani on (terminy.datum = rozlosovani.datum)
            RIGHT JOIN vysledky on (rozlosovani.id = vysledky.id_zapasu)
            WHERE rocniky.rocnik = ?
            GROUP BY terminy.datum
            ORDER BY terminy.rocnik ASC, datum ASC
        ', $rocnik)->fetchAll();
    }

  public function aktualniKolo($rocnikID)
  {
    return $this->database->query('
        SELECT terminy.datum, terminy.popis as termin, terminy.id as id, rocniky.popis as rocnik
        FROM terminy
        LEFT JOIN rocniky on (terminy.rocnik = rocniky.id)
        WHERE rocniky.id = ?
        AND datum <= NOW()
        ORDER BY datum DESC
        LIMIT 1
    ', $rocnikID)->fetch();
  }

  public function pristiKolo($rocnikID)
  {
    return $this->database->query('
        SELECT terminy.datum, terminy.popis as termin, terminy.id as id, rocniky.popis as rocnik
        FROM terminy
        LEFT JOIN rocniky on (terminy.rocnik = rocniky.id)
        WHERE rocniky.id = ?
        AND datum > NOW()
        ORDER BY datum ASC
        LIMIT 1
    ', $rocnikID)->fetch();
  }
}
