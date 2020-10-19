<?php

namespace App\Model;

// use Dibi;

/**
 * Aktuality model.
 */
final class Akce
{
/*
    private $connection;
    private $table = "akce";

    public function __construct(Dibi\Connection $connection)
    {
        $this->db = $connection;
    }



	public function findAll($col = 'id', $dir = 'desc', $now)
	{
		return $this->dibi->fetchAll("SELECT *, UNIX_TIMESTAMP(datum_od) as int_datum_od, UNIX_TIMESTAMP(datum_do) as int_datum_do FROM %n WHERE datum_od >= %d ORDER BY %n", $this->table, $now, $col);
	}



	public function find($id)
	{
		return $this->dibi->select('*')->from($this->table)->where('id=%i', $id);
	}



	public function update($id, $data)
	{
		return $this->dibi->update($this->table, $data)->where('id=%i', $id)->execute();
	}



	public function insert($data)
	{
		return $this->dibi->insert($this->table, $data)->execute(dibi::IDENTIFIER);
	}



	public function delete($id)
	{
		return $this->dibi->delete($this->table)->where('id=%i', $id)->execute();
	}
*/
}
