<?php



/**
 * Diskuze model.
 */
class Uzivatele extends Object
{
  /** @var string */
  private $table = 'uzivatele';

  /** @var DibiConnection */
  private $connection;


  public function __construct()
  {
    $this->connection = dibi::getConnection();
  }



  public function findAll($orderBy = "id")
  {
    return $this->connection->select('*')->from($this->table)->orderBy($orderBy);
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
