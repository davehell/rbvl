<?php



/**
 * Alba model.
 */
class Alba extends Object
{
	/** @var string */
	private $table = 'alba';

	/** @var DibiConnection */
	private $connection;


	public function __construct()
	{
		$this->connection = dibi::getConnection();
	}



	public function findAll($order = array('id' => 'asc'))
	{
    return $this->connection->dataSource('
      select alba.id, alba.popis, fotky.soubor, fotky.vyska, fotky.sirka
      from alba left join fotky
      on alba.id=fotky.album
      where alba.aktualni = 1
      group by alba.id
      ORDER BY %by', $order);
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
