<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Charges extends Model
{
    //
    protected $primaryKey = 'id';

    protected $table ='charges';
    protected $fillable = ['name', 'price'];

    public function shipping_charge()
    {
        return $this->hasMany(Shipping_charge::class);
    }
}
