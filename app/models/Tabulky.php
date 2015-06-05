<?php



/**
 * Tabulky model.
 */
class Tabulky extends Object
{
	/** @var string */
	private $table = 'tabulky';

	/** @var DibiConnection */
	private $connection;


	public function __construct()
	{
		$this->connection = dibi::getConnection();
	}


	public function getTabulky($rocnik)
	{
    return $this->connection->dataSource('
      SELECT s.popis as skupina_popis, d.nazev as druzstvo_nazev,
      (t.sety_dal/t.sety_dostal) as sety, (t.mice_dal/t.mice_dostal) as mice, t.*, r.popis as rocnik_popis
      FROM
      tabulky as t
      LEFT JOIN skupiny as s on (s.id=t.skupina)
      LEFT JOIN rocniky as r on (s.rocnik=r.id)
      LEFT JOIN druzstva as d on (t.druzstvo=d.id)
      WHERE r.id=%i
      ORDER BY
        skupina asc,
        body desc,
        sety desc,
        mice desc,
        cislo asc
    ', $rocnik);
	}

	public function findAll($order = array("id" => "asc"))
	{
    return $this->connection->dataSource("SELECT * FROM %n ORDER BY %by", $this->table, $order);
	}


	public function getDruzstvoId($skupina, $cislo)
	{
		return $this->connection->select('druzstvo')->from($this->table)->where('skupina=%i AND cislo=%i', $skupina, $cislo);
	}

	public function find($id)
	{
		return $this->connection->select('*')->from($this->table)->where('id=%i', $id);
	}



	public function update($skupina, $druzstvo, array $data)
	{
		return $this->connection->update($this->table, $data)->where('druzstvo=%i', $druzstvo)->where('skupina=%i', $skupina)->execute();
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