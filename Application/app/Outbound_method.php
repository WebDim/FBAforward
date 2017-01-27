<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Outbound_method extends Model
{
    protected $primaryKey = 'outbound_method_id';
    protected $table ='outbound_methods';

    public function outbound_shipping_detail()
    {
        return $this->hasMany(Outbound_Shipping_detail::class,'outbound_method_id');
    }
}
