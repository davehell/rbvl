<?php

namespace App\Model;


final class Aktuality extends BaseModel
{
    protected $table = "aktuality";

    public function findAllDateSorted()
    {
        return $this->findAll()->order("vlozeno DESC");
    }
}
