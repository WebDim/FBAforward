<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Amazon_destination extends Model
{
    //
    protected $primaryKey = 'amazon_destination_id';

    protected $table ='amazon_destinations';

    public function outbound_shipping_detail()
    {
        return $this->hasMany(Outbound_Shipping_detail::class,'amazon_destination_id');
    }
}
