<?php

namespace App\Model;

use Nette;

final class Akce extends BaseModel
{
    protected $table = "akce";

    public function findActualActions(int $limit, int $offset): Nette\Database\ResultSet
    {
        return $this->database->query('
            SELECT *, UNIX_TIMESTAMP(datum_od) as int_datum_od, UNIX_TIMESTAMP(datum_do) as int_datum_do
            FROM akce
            WHERE datum_od >= ?
            ORDER BY datum_od
            LIMIT ?
            OFFSET ?
        ', new \DateTime, $limit, $offset);
    }

    /**
     * Vrací celkový počet aktuálních akci
     */
    public function getActualActionsCount(): int
    {
        return $this->database->fetchField('SELECT COUNT(*) FROM akce WHERE datum_od > ?', new \DateTime);
    }
}
