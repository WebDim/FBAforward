<?php

namespace App;

use App\BaseModel;

class Customer_amazon_detail extends BaseModel
{



    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */

    public function users()
    {
        return $this->belongsTo(Users::class);
    }
    public function amazon_marketplace()
    {
        return $this->belongsTo(Amazon_marketplace::class);
    }
}
