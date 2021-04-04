<?php

namespace App\Model;


final class Akce extends BaseModel
{
    protected $table = "akce";

    public function findAllDateSorted($date)
    {
        return $this->database->query('
            SELECT *, UNIX_TIMESTAMP(datum_od) as int_datum_od, UNIX_TIMESTAMP(datum_do) as int_datum_do
            FROM akce
            WHERE datum_od >= ?
            ORDER BY datum_od
        ', $date)->fetchAll();
    }
}
