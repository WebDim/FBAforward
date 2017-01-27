<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supplier_detail extends Model
{
    //
    protected $primaryKey = 'supplier_detail_id';
    protected $table ='supplier_details';
    protected $fillable = ['shipment_detail_id','order_id','supplier_id','user_id', 'product_id', 'total_unit'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class,'supplier_id');
    }
    public function supplier_inspection()
    {
        return $this->hasMany(Supplier_inspection::class,'supplier_detail_id');
    }
    public function shipment_detail()
    {
        return $this->belongsTo(Shipment_detail::class);
    }
    public function order()
    {
        return $this->belongsTo(Order::class,'supplier_id');
    }
    public function users()
    {
        return $this->belongsTo(User::class);
    }
    public function amazon_inventory()
    {
        return $this->belongsTo(Amazon_inventory::class);
    }
}
