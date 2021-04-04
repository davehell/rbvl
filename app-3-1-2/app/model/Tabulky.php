<?php

namespace App\Model;


final class Tabulky extends BaseModel
{
    protected $table = "tabulky";

    public function getTabulky($rocnik)
    {
        return $this->database->query('
            SELECT s.popis as skupina_popis, d.nazev as druzstvo_nazev,
            (t.sety_dal/t.sety_dostal) as sety, (t.mice_dal/t.mice_dostal) as mice, t.*, r.popis as rocnik_popis
            FROM tabulky as t
            LEFT JOIN skupiny as s on (s.id=t.skupina)
            LEFT JOIN rocniky as r on (s.rocnik=r.id)
            LEFT JOIN druzstva as d on (t.druzstvo=d.id)
            WHERE r.id=?
            ORDER BY skupina asc, body desc, sety desc, mice desc, cislo asc
        ', $rocnik)->fetchAll();
    }

    public function getDruzstvoId($skupina, $cislo)
    {
        return $this->database->table($this->table)->where("cislo", $cislo)->where("skupina", $skupina)->fetch();
    }

    public function updateTabulka($skupina, $druzstvo, $data)
    {
        return $this->database->table($this->table)->where("druzstvo", $druzstvo)->where("skupina", $skupina)->update($data);
    }
}