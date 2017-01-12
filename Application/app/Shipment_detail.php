<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Shipment_detail extends Model
{
    //
    protected $primaryKey = 'shipment_detail_id';
    protected $fillable = ['user_id','product_id','shipping_method_id','split_shipment','goods_ready_date', 'fnsku','qty_per_box', 'qty_per_box ', 'no_boxs', 'total'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function shipping_method()
    {
        return $this->belongsTo(Shipping_method::class);
    }
    public function amazon_inventory()
    {
        return $this->belongsTo(Amazon_inventory::class);
    }

}
