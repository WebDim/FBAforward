<?php

namespace App;

use App\BaseModel;
use Illuminate\Notifications\Notifiable;
class Role extends BaseModel {

    use Notifiable;
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function Users() {
        return $this->hasMany(User::class);
    }
    public function notification()
    {
        return $this->hasMany(Notification::class);
    }
    public function newNotification()
    {
        $notification = new Notification;
        $notification->role()->associate($this);

        return $notification;
    }

}
