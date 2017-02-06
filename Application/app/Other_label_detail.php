<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Other_label_detail extends Model
{
    //
    protected $primaryKey = 'other_label_detail_id';
    protected $table ='other_label_details';
    protected $fillable=['label_id','prep_detail_id'];
    public function prep_detail()
    {
        return $this->belongsTo(Prep_detail::class,'prep_detail_id');
    }

}