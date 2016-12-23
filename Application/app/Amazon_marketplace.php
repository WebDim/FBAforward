<?php

namespace App;

use App\BaseModel;

class Amazon_marketplace extends BaseModel
{
    use Billable;


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
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function customer_amazon_detail()
    {
        return $this->hasMany(Customer_amazon_detail::class);
    }

}
