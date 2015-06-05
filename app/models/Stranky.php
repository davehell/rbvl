<?php



/**
 * Stranky model.
 */
class Stranky extends Object
{
	/** @var string */
	private $table = 'stranky';

	/** @var DibiConnection */
	private $connection;


	public function __construct()
	{
		$this->connection = dibi::getConnection();
	}


	public function find($id)
	{
		return $this->connection->select('*')->from($this->table)->where('id=%i', $id);
	}

	public function findByNazev($nazev)
	{
		return $this->connection->select('*')->from($this->table)->where('nazev=%s', $nazev);
	}

	public function update($id, array $data)
	{
		return $this->connection->update($this->table, $data)->where('id=%i', $id)->execute();
	}
}
