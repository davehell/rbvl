<?php

namespace App\Model;


final class Stranky extends BaseModel
{
	protected $table = "stranky";

	/**
	 * @param string
	 * @return \Nette\Database\Table\Selection
	 */
	public function getByNazev($text)
	{
		return $this->getBy(["nazev" => $text]);
	}
}
