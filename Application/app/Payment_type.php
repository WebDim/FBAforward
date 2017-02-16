<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment_type extends Model
{
    //
    protected $primaryKey = 'id';

    protected $table ='payment_types';
    protected $fillable = ['type_name'];

    public function delivery_booking()
    {
        return $this->hasMany(Delivery_booking::class);
    }
}