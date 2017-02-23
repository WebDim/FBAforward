<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Warehouse_checkin extends Model
{
    //
    protected $primaryKey = 'id';
    protected $table ='warehouse_checkins';
    protected $fillable = ['order_id','shipment_id','cartoon_length', 'cartoon_width', 'cartoon_weight','cartoon_height','no_of_cartoon','unit_per_cartoon','cartoon_condition','location'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function Shipment()
    {
        return $this->belongsTo(Shipments::class);
    }
    public function Warehouse_checkin_image()
    {
        return $this->hasMany(Warehouse_checkin_image::class);
    }
}