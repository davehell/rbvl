<?php



/**
 * Aktuality model.
 */
class Akce extends Object
{
	/** @var string */
	private $table = 'akce';

	/** @var DibiConnection */
	private $connection;


	public function __construct()
	{
		$this->connection = dibi::getConnection();
	}



	public function findAll($col = 'id', $dir = 'desc', $now)
	{
        return $this->connection->dataSource("SELECT *, UNIX_TIMESTAMP(datum_od) as int_datum_od, UNIX_TIMESTAMP(datum_do) as int_datum_do FROM %n WHERE datum_od >= %d ORDER BY %n $dir", $this->table, $now, $col);
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
