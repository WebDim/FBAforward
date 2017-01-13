<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    //
    protected $primaryKey = 'supplier_id';

    protected $table ='suppliers';

    public function supplier_detail()
    {
        return $this->hasMany(Supplier_detail::class,'supplier_id');
    }
}
