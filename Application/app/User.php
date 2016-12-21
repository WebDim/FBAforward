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
}
