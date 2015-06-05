<?php



/**
 * Hraci model.
 */
class Hraci extends Object
{
	/** @var string */
	private $table = 'hraci';

	/** @var DibiConnection */
	private $connection;


	public function __construct()
	{
		$this->connection = dibi::getConnection();
	}

	public function findAll($order = array('prijmeni' => 'asc', 'jmeno' => 'asc'))
	{
    //return $this->connection->dataSource('select h.id, h.jmeno, h.prijmeni, h.narozen, d.nazev from hraci as h, soupisky as s, druzstva as d where s.hrac = h.id and s.druzstvo = d.id ORDER BY %by', $order);
    return $this->connection->dataSource('SELECT h.id, h.jmeno, h.prijmeni, h.narozen, d.nazev FROM hraci as h LEFT JOIN soupisky AS s ON h.id = s.hrac LEFT JOIN druzstva AS d ON s.druzstvo = d.id ORDER BY %by', $order);
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
