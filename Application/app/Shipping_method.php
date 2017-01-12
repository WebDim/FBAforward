<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shipping_method extends Model
{
    //
    protected $primaryKey = 'shipping_method_id';

protected $table ='shipping_methods';
    public function shipment_detail()
    {
        return $this->hasMany(Shipment_detail::class);
    }
}
