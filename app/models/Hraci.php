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
    return $this->connection->dataSource('SELECT * FROM %n ORDER BY %by', $this->table, $order);
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
