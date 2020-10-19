<?php

namespace App\Model;

use Nette;

final class Diskuze
{
  /** @var Nette\Database\Context */
  private $db;

  /** @var Nette\Database\Connection */
  private $conn;

  private $table = "diskuze";

  public function __construct(Nette\Database\Connection $connection, Nette\Database\Context $context)
  {
    $this->conn = $connection;
    $this->db = $context;
  }


	public function findAll($order = array('id' => 'desc'), $limit = 30)
	{
    	return $this->conn->query('SELECT * FROM ?name', $this->table);
        // return $this->db->table("diskuze");
	}



	public function find($id)
	{
		return $this->db->select('*')->from($this->table)->where('id=%i', $id)->fetch();
	}



	public function update($id, $data)
	{
		return $this->db->update($this->table, $data)->where('id=%i', $id)->execute();
	}



	public function insert($data)
	{
		return $this->db->insert($this->table, $data)->execute();
	}



	public function delete($id)
	{
		return $this->db->delete($this->table)->where('id=%i', $id)->execute();
	}

}
