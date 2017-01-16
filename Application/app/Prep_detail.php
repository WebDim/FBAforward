<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Prep_detail extends Model
{
    //
    protected $primaryKey = 'prep_detail_id';
    protected $table ='prep_details';
    protected $fillable = ['user_id','product_id','total_qty', 'prep_service_ids', 'prep_service_total','grand_total','	is_activated'];

    public function prep_service()
    {
        return $this->belongsTo(Prep_service::class,'prep_service_id');
    }

}