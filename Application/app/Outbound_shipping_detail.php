<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Outbound_shipping_detail extends Model
{
    //
    protected $primaryKey = 'outbound_shipping_detail_id';
    protected $table ='outbound_shipping_details';
    protected $fillable = ['shipment_detail_id', 'order_id','product_ids','qty', 'amazon_destination_id', 'outbound_method_id','is_activated'];

    public function amazon_destination()
    {
        return $this->belongsTo(Amazon_destination::class,'amazon_destination_id');
    }
    public function outbound_method()
    {
        return $this->belongsTo(Outbound_method::class,'outbound_method_id');
    }
    public function shipment_deatil()
    {
        return $this->belongsTo(Shipment_detail::class);
    }
}