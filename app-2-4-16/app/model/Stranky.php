<?php

namespace App\Model;

use Nette;

final class Stranky
{
	private $table = null;

	public function __construct(Nette\Database\Context $context)
	{
	   $this->table = $context->table("stranky");
	}


	public function find($id)
	{
		return $this->table->get($id);
	}

	public function findByNazev($nazev)
	{
		return $this->table->where("nazev", $nazev)->fetch();
	}

	public function update($id, $data)
	{
		return $this->table->where("id", $id)->update($data);
	}
}
