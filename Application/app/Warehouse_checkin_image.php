<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Warehouse_checkin_image extends Model
{
    //
    protected $primaryKey = 'id';
    protected $table ='warehouse_checkin_images';
    protected $fillable = ['warehouse_checkin_id','images'];

    public function warehouse_checkin()
    {
        return $this->belongsTo(Warehouse_checkin::class);
    }
}