<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Additional_service extends Model
{
    //
    protected $primaryKey = 'id';

    protected $table ='additional_services';
    protected $fillable = ['custom_clearance_id', 'service_id'];

    public function custom_clearance()
    {
        return $this->belongsTo(Custom_clearance::class);
    }

}