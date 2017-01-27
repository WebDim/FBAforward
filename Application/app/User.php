<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    use Billable;
    
    protected $dates = ['trial_ends_at', 'subscription_ends_at', 'deleted_at'];
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

    /**
     * Get the role record associated with the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function getAvatarAttribute()
    {
        return 'uploads/avatars/' . $this->attributes['avatar'];
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
    public function user_info()
    {
        return $this->hasMany(User_info::class);
    }
    public function customer_amazon_detail()
    {
        return $this->hasMany(Customer_amazon_detail::class);
    }
    public function amazon_inventory()
    {
        return $this->hasMany(Amazon_inventory::class);
    }
    public function shipment_detail()
    {
        return $this->hasMany(Shipment_detail::class);
    }
    public function supplier_detail()
    {
        return $this->hasMany(Supplier_detail::class);
    }
}
