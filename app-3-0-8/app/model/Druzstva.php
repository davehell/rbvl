<?php

namespace App\Model;


final class Druzstva extends BaseModel
{
    protected $table = "druzstva";

    //pouziti pro seznam druzstev v Druzstva:default
    public function findAllInRocnik($rocnik)
    {
      return $this->database->query('
        SELECT d.*, s.popis as skupina_popis
        FROM rocniky as r
        LEFT JOIN skupiny as s on (s.rocnik=r.id)
        LEFT JOIN tabulky as t on (t.skupina=s.id)
        RIGHT JOIN druzstva as d on (t.druzstvo=d.id)
        WHERE r.rocnik=?
        AND r.aktualni=1
        AND d.nazev <> "foo"
        ORDER BY t.skupina ASC, t.cislo ASC
      ', $rocnik)->fetchAll();
    }


    //pouziti pro seznam druzstev v selectboxu u rozlosovani a vysledku
    //kazde druzstvo se zobrazi pouze jednou - i kdyz bude zarazeno do vice skupin
    public function findAllUnique($rocnik, $order = array("t.skupina" => true, "t.cislo" => true))
    {
        return $this->database->query('
            SELECT d.*
            FROM rocniky as r
            LEFT JOIN skupiny as s on (s.rocnik=r.id)
            LEFT JOIN tabulky as t on (t.skupina=s.id)
            RIGHT JOIN druzstva as d on (t.druzstvo=d.id)
            WHERE r.rocnik=? AND r.aktualni=1 AND d.nazev <> "foo"
            GROUP BY d.nazev
            ORDER BY ?
        ', $rocnik, $order)->fetchAll();
    }

    public function soupiskaDruzstva($id)
    {
      return $this->database->query('
        SELECT h.id as idHrac, h.jmeno, h.prijmeni, h.narozen, s.id as idSoupiska
        FROM hraci as h, soupisky as s
        WHERE s.druzstvo = ?
        AND h.id = s.hrac
        ORDER BY s.id ASC
      ', $id)->fetchAll();
    }
}
