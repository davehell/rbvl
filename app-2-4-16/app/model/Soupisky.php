<?php

namespace App\Model;


final class Soupisky extends BaseModel
{
    protected $table = "soupisky";


	public function findPlayer($hrac)
	{
        return $this->database->query('
          SELECT h.jmeno, h.prijmeni, h.narozen, d.nazev as druzstvo
          FROM hraci as h, soupisky as s, druzstva as d
          WHERE s.hrac = ?
          AND s.druzstvo = d.id
          AND s.hrac = h.id
        ', $hrac)->fetch();
	}

	public function findAllPlayersInTeam($druzstvo)
	{
        return $this->database->query('
          SELECT h.id, h.jmeno, h.prijmeni, h.narozen
          FROM hraci as h, soupisky as s
          WHERE s.druzstvo = ?
          AND s.hrac = h.id
          ORDER BY h.prijmeni ASC, h.jmeno ASC
        ', $druzstvo);
	}

	public function deleteOnePlayerInTeam($hrac, $druzstvo)
	{
		return $this->database->table($this->table)->where('hrac', $hrac)->where('druzstvo', $druzstvo)->delete();
	}

	// public function deleteAllPlayersInTeam($druzstvo)
	// {
	// 	return $this->connection->delete($this->table)->where('druzstvo=%i', $druzstvo)->execute();
	// }

	// public function deleteAll()
	// {
	// 	return $this->connection->delete($this->table)->execute();
	// }

}
