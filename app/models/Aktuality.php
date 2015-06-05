<?php



/**
 * Aktuality model.
 */
class Aktuality extends Object
{
	/** @var string */
	private $table = 'aktuality';

	/** @var DibiConnection */
	private $connection;


	public function __construct()
	{
		$this->connection = dibi::getConnection();
	}



	public function findAll($order = array('id' => 'desc'), $limit = 30)
	{
    return $this->connection->dataSource('SELECT * FROM %n ORDER BY %by LIMIT %i', $this->table, $order, $limit);
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
