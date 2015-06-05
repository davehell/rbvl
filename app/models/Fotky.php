<?php



/**
 * Fotky model.
 */
class Fotky extends Object
{
	/** @var string */
	private $table = 'fotky';

	/** @var DibiConnection */
	private $connection;


	public function __construct()
	{
		$this->connection = dibi::getConnection();
	}



	public function findAllInAlbum($album, $order = array('id' => 'asc'))
	{
    return $this->connection->dataSource('SELECT * FROM %n WHERE album=%i ORDER BY %by', $this->table, $album, $order);
	}
	
	
	public function findAll($order = array('id' => 'asc'))
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
