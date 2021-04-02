<?php

namespace App\Model;


final class Hraci extends BaseModel
{
    protected $table = "hraci";

	public function findAllNameSorted()
	{
    	return $this->database->query('
    		SELECT h.id, h.jmeno, h.prijmeni, h.narozen, d.nazev
    		FROM hraci as h
    		LEFT JOIN soupisky AS s ON h.id = s.hrac
    		LEFT JOIN druzstva AS d ON s.druzstvo = d.id
    		ORDER BY prijmeni ASC, jmeno ASC');
	}

}
