<?php

namespace App\Model;


final class Diskuze extends BaseModel
{
    protected $table = "diskuze";

    public function findAllDateSorted()
    {
        return $this->findAll()->order("vlozeno DESC");
    }
}
