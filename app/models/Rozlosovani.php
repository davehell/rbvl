<?php



/**
 * Diskuze model.
 */
class Rozlosovani extends Object
{
	/** @var string */
	private $table = 'rozlosovani';

	/** @var DibiConnection */
	private $connection;


	public function __construct()
	{
		$this->connection = dibi::getConnection();
	}



	public function findAll($rocnik, $order = array("datum" => "asc", "cas" => "asc", "id" => "asc"))
	{
    return $this->connection->dataSource("
    SELECT r.*, t.popis AS termin, roc.popis AS rocnik, dd.nazev as domaci, dh.nazev as hoste, s.popis as skupina_popis, s.id as skupina_id
    FROM %n as r
    LEFT JOIN terminy AS t on (r.datum=t.datum)
    LEFT JOIN rocniky AS roc on (t.rocnik=roc.id)
    LEFT JOIN tabulky AS td on (r.skupina=td.skupina AND r.cislo_domaci=td.cislo)
    LEFT JOIN tabulky AS th on (r.skupina=th.skupina AND r.cislo_hoste=th.cislo)
    LEFT JOIN druzstva as dd on (td.druzstvo=dd.id)
    LEFT JOIN druzstva as dh on (th.druzstvo=dh.id)
    LEFT JOIN skupiny as s on (r.skupina=s.id)
    WHERE roc.rocnik = %s
    ORDER BY %by", $this->table, $rocnik, $order);
	}
/*
    return $this->connection->dataSource("
    SELECT r.*, t.popis AS termin, roc.popis AS rocnik, dd.nazev as domaci, dh.nazev as hoste, sd.nazev as skupina_domaci_nazev, sh.nazev as skupina_hoste_nazev
    FROM %n as r
    LEFT JOIN terminy AS t on (r.datum=t.datum)
    LEFT JOIN rocniky AS roc on (t.rocnik=roc.id)
    LEFT JOIN tabulky AS td on (r.skupina_domaci=td.skupina AND r.cislo_domaci=td.cislo)
    LEFT JOIN tabulky AS th on (r.skupina_hoste=th.skupina AND r.cislo_hoste=th.cislo)
    LEFT JOIN druzstva as dd on (td.druzstvo=dd.id)
    LEFT JOIN druzstva as dh on (th.druzstvo=dh.id)
    LEFT JOIN skupiny as sd on (r.skupina_domaci=sd.id)
    LEFT JOIN skupiny as sh on (r.skupina_hoste=sh.id)
    WHERE roc.rocnik = %s
    ORDER BY %by", $this->table, $rocnik, $order);
*/
	
	public function findAllInTermin($termin, $order = array("datum" => "asc", "cas" => "asc", "id" => "asc"))
	{
    return $this->connection->dataSource("
    SELECT r.*, t.popis AS termin, roc.popis AS rocnik, dd.nazev as domaci, dh.nazev as hoste, s.popis as skupina_popis
    FROM %n as r
    LEFT JOIN terminy AS t on (r.datum=t.datum)
    LEFT JOIN rocniky AS roc on (t.rocnik=roc.id)
    LEFT JOIN tabulky AS td on (r.skupina=td.skupina AND r.cislo_domaci=td.cislo)
    LEFT JOIN tabulky AS th on (r.skupina=th.skupina AND r.cislo_hoste=th.cislo)
    LEFT JOIN druzstva as dd on (td.druzstvo=dd.id)
    LEFT JOIN druzstva as dh on (th.druzstvo=dh.id)
    LEFT JOIN skupiny as s on (r.skupina=s.id)
    WHERE t.id = %i
    ORDER BY %by", $this->table, $termin, $order);
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
