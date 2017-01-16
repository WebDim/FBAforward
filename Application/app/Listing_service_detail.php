<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Listing_service_detail extends Model
{
    //
    protected $primaryKey = 'listing_service_detail_id ';
    protected $table ='listing_service_details';
    protected $fillable = ['product_id', 'listing_service_ids', 'listing_service_total','grand_total','	is_activated'];

    public function listing_service()
    {
        return $this->belongsTo(Listing_service::class,'listing_service_id');
    }

}