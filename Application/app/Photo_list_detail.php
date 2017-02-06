<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Photo_list_detail extends Model
{
    //
    protected $primaryKey = 'photo_list_detail_id';
    protected $table ='photo_list_details';
    protected $fillable=['photo_list_detail_id','listing_service_detail_id','standard_photo','prop_photo'];
    public function listing_service_detail()
    {
        return $this->belongsTo(Listing_service_detail::class,'listing_service_detail_id');
    }

}