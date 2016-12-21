<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('users')->delete();
        
        \DB::table('users')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Admin',
                'email' => 'admin@laraship.com',
                'password' => '$2y$10$OK5VFN1FkFSn6WFhE9U0J.CIoZEQ5JM86wsrAWTOjjyJUsxWpBAey',
                'role_id' => 1,
                'package_id' => 0,
                'job_title' => 'Admin',
                'address' => '225 Ocean Park',
                'mobile' => '19208432222',
                'avatar' => 'avatar.png',
                'remember_token' => NULL,
                'created_at' => '2016-04-04 09:26:29',
                'updated_at' => '2016-04-04 09:27:31',
                'stripe_id' => NULL,
                'card_brand' => NULL,
                'card_last_four' => NULL,
                'trial_ends_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Member',
                'email' => 'member@laraship.com',
                'password' => '$2y$10$kP6Xd7DXACYVFRrBXXNgN.eRTO2jRCEL45jcIw0FQLICqNI6kTG1K',
                'role_id' => 2,
                'package_id' => 0,
                'job_title' => 'Member',
                'address' => '225 Ocean Park',
                'mobile' => '19208432222',
                'avatar' => 'avatar.png',
                'remember_token' => NULL,
                'created_at' => '2016-04-04 09:26:29',
                'updated_at' => '2016-04-04 09:27:51',
                'stripe_id' => NULL,
                'card_brand' => NULL,
                'card_last_four' => NULL,
                'trial_ends_at' => NULL,
            ),
        ));
        
        
    }
}
