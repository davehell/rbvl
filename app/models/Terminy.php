<?php



/**
 * Terminy model.
 */
class Terminy extends Object
{
	/** @var string */
	private $table = 'terminy';

	/** @var DibiConnection */
	private $connection;


	public function __construct()
	{
		$this->connection = dibi::getConnection();
	}



	public function findAll($rocnik)
	{
    return dibi::query('
    SELECT terminy.datum, terminy.popis as termin, terminy.id as id, rocniky.popis as rocnik, rocniky.id as id_rocnik
    FROM terminy
    left join rocniky on (terminy.rocnik = rocniky.id)
    right join rozlosovani on (terminy.datum = rozlosovani.datum)
    WHERE rocniky.rocnik = %s
    AND rocniky.aktualni = 1
    GROUP BY terminy.datum
    ORDER BY terminy.rocnik ASC, datum ASC
    ', $rocnik);
	}

	public function findAllVysledky($rocnik)
	{
    return dibi::query('
    SELECT terminy.datum, terminy.popis as termin, terminy.id as id, rocniky.popis as rocnik, rocniky.id as id_rocnik
    FROM terminy
    left join rocniky on (terminy.rocnik = rocniky.id)
    right join rozlosovani on (terminy.datum = rozlosovani.datum)
    right join vysledky on (rozlosovani.id = vysledky.id_zapasu)
    WHERE rocniky.rocnik = %s
    GROUP BY terminy.datum
    ORDER BY terminy.rocnik ASC, datum ASC
    ', $rocnik);
	}

  public function aktualniKolo($rocnikID)
  {
    return dibi::query('
    SELECT terminy.datum, terminy.popis as termin, terminy.id as id, rocniky.popis as rocnik
    FROM terminy
    left join rocniky on (terminy.rocnik = rocniky.id)
    WHERE rocniky.id = %i
    AND datum <= NOW()
    ORDER BY datum DESC
    LIMIT 1
    ', $rocnikID);
  }

  public function pristiKolo($rocnikID)
  {
    return dibi::query('
    SELECT terminy.datum, terminy.popis as termin, terminy.id as id, rocniky.popis as rocnik
    FROM terminy
    left join rocniky on (terminy.rocnik = rocniky.id)
    WHERE rocniky.id = %i
    AND datum > NOW()
    ORDER BY datum ASC
    LIMIT 1
    ', $rocnikID);
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
