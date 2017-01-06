<?php

namespace App;

use App\BaseModel;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Dev_account extends Eloquent
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

     public function amazon_marketplace()
    {
        return $this->belongsTo(Amazon_marketplace::class);
    }
}
