<?php

use Illuminate\Database\Seeder;

class FeaturesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('features')->delete();
        DB::table('features')->insert([
            ['id' => 1, 'name' => 'Support', 'status' => 1, 'created_at' => \Carbon::now()],
            ['id' => 2, 'name' => 'Disk Space', 'status' => 1, 'created_at' => \Carbon::now()],
            ['id' => 3, 'name' => 'Dedicated RAM', 'status' => 1, 'created_at' => \Carbon::now()],
            ['id' => 4, 'name' => 'Processors', 'status' => 1, 'created_at' => \Carbon::now()],
            ['id' => 5, 'name' => 'Add-on Domain', 'status' => 1, 'created_at' => \Carbon::now()],
            ['id' => 6, 'name' => 'Email Accounts', 'status' => 1, 'created_at' => \Carbon::now()],
        ]);
    }
}
