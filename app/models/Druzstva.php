<?php



/**
 * Druzstva model.
 */
class Druzstva extends Object
{
	/** @var string */
	private $table = 'druzstva';

	/** @var DibiConnection */
	private $connection;


	public function __construct()
	{
		$this->connection = dibi::getConnection();
	}


	public function findAll($rocnik, $order = array('t.skupina' => 'asc', 't.cislo' => 'asc'))
	{
        return $this->connection->dataSource('
          SELECT d.*, s.popis as skupina_popis
          FROM
          rocniky as r
          LEFT JOIN skupiny as s on (s.rocnik=r.id)
          LEFT JOIN tabulky as t on (t.skupina=s.id)
          RIGHT JOIN druzstva as d on (t.druzstvo=d.id)
          WHERE r.rocnik=%s AND r.aktualni=1 AND d.nazev <> "foo"
          ORDER BY %by
        ', $rocnik, $order);
	}

	public function findAllInSkupina($skupina, $rocnik)
	{
        return $this->connection->dataSource('
          SELECT d.id, d.nazev
          FROM tabulky as t
          LEFT JOIN druzstva AS d ON (t.druzstvo=d.id)
          LEFT JOIN skupiny AS s ON (s.id=t.skupina)
          LEFT JOIN rocniky AS r ON (s.rocnik=r.id)
          WHERE s.id = %i
          AND r.rocnik = %s
          ORDER BY t.id ASC
        ', $skupina, $rocnik);
	}

	public function soupiska($id)
	{
        return $this->connection->dataSource('
          SELECT h.id as idHrac, h.jmeno, h.prijmeni, h.narozen, s.id as idSoupiska
          FROM hraci as h, soupisky as s
          WHERE s.druzstvo = %i
          AND h.id = s.hrac
          ORDER BY h.prijmeni ASC, h.jmeno ASC
        ', $id);
	}

	public function find($id)
	{
		return $this->connection->select('*')->from($this->table)->where('id=%i', $id);
	}



	public function update($id, array $data)
	{
		return $this->connection->update($this->table, $data)->where('id=%i', $id)->execute();
	}



	public function insert(array $data)
	{
		return $this->connection->insert($this->table, $data)->execute(dibi::IDENTIFIER);
	}



	public function delete($id)
	{
		return $this->connection->delete($this->table)->where('id=%i', $id)->execute();
	}

}
