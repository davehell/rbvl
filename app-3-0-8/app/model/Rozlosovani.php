<?php

namespace App\Model;


final class Rozlosovani extends BaseModel
{
    protected $table = "rozlosovani";

    public function findAllInRocnik($rocnik, $order = array("datum" => true, "cas" => true, "id" => true))
    {
        return $this->database->query('
            SELECT r.*, t.popis AS termin, roc.popis AS rocnik, dd.nazev as domaci, dh.nazev as hoste, s.popis as skupina_popis, s.id as skupina_id
            FROM rozlosovani as r
            LEFT JOIN terminy AS t on (r.datum=t.datum)
            LEFT JOIN rocniky AS roc on (t.rocnik=roc.id)
            LEFT JOIN tabulky AS td on (r.skupina=td.skupina AND r.cislo_domaci=td.cislo)
            LEFT JOIN tabulky AS th on (r.skupina=th.skupina AND r.cislo_hoste=th.cislo)
            LEFT JOIN druzstva as dd on (td.druzstvo=dd.id)
            LEFT JOIN druzstva as dh on (th.druzstvo=dh.id)
            LEFT JOIN skupiny as s on (r.skupina=s.id)
            WHERE roc.rocnik = ?
            ORDER BY ?
        ', $rocnik, $order)->fetchAll();
    }
    
    public function findAllInTermin($termin, $order = array("datum" => true, "cas" => true, "id" => true))
    {
        return $this->database->query('
            SELECT r.*, t.popis AS termin, roc.popis AS rocnik, dd.nazev as domaci, dh.nazev as hoste, s.popis as skupina_popis
            FROM rozlosovani as r
            LEFT JOIN terminy AS t on (r.datum=t.datum)
            LEFT JOIN rocniky AS roc on (t.rocnik=roc.id)
            LEFT JOIN tabulky AS td on (r.skupina=td.skupina AND r.cislo_domaci=td.cislo)
            LEFT JOIN tabulky AS th on (r.skupina=th.skupina AND r.cislo_hoste=th.cislo)
            LEFT JOIN druzstva as dd on (td.druzstvo=dd.id)
            LEFT JOIN druzstva as dh on (th.druzstvo=dh.id)
            LEFT JOIN skupiny as s on (r.skupina=s.id)
            WHERE t.id = ?
            ORDER BY ?
        ', $termin, $order)->fetchAll();
    }
}
