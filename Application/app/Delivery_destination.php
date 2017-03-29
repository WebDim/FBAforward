<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Delivery_destination extends Model
{
    //
    protected $primaryKey = 'id';

    protected $table ='delivery_destinations';
    protected $fillable = ['destination_name'];

    public function delivery_booking()
    {
        return $this->hasMany(Delivery_booking::class);
    }
}