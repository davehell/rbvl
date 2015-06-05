<?php



/**
 * Soupisky model.
 */
class Soupisky extends Object
{
	/** @var string */
	private $table = 'soupisky';

	/** @var DibiConnection */
	private $connection;


	public function __construct()
	{
		$this->connection = dibi::getConnection();
	}


	public function findPlayer($hrac)
	{
    return $this->connection->dataSource('
      SELECT h.jmeno, h.prijmeni, h.narozen, d.nazev as druzstvo
      FROM hraci as h, soupisky as s, druzstva as d
      WHERE s.hrac = %i
      AND s.druzstvo = d.id
      AND s.hrac = h.id
    ', $hrac);
	}

	public function findAllPlayersInTeam($druzstvo)
	{
    return $this->connection->dataSource('
      SELECT h.id, h.jmeno, h.prijmeni, h.narozen
      FROM hraci as h, soupisky as s
      WHERE s.druzstvo = %i
      AND s.hrac = h.id
      ORDER BY h.prijmeni ASC, h.jmeno ASC
    ', $druzstvo);
	}


	public function insert(array $data)
	{
		return $this->connection->insert($this->table, $data)->execute(dibi::IDENTIFIER);
	}



	public function deleteOnePlayerInTeam($hrac, $druzstvo)
	{
		return $this->connection->delete($this->table)->where('hrac=%i', $hrac)->where('druzstvo=%i', $druzstvo)->execute();
	}

	public function deleteAllPlayersInTeam($druzstvo)
	{
		return $this->connection->delete($this->table)->where('druzstvo=%i', $druzstvo)->execute();
	}

	public function deleteAll()
	{
		return $this->connection->delete($this->table)->execute();
	}

}
