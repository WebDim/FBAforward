<?php

use Illuminate\Database\Seeder;

class PackagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('packages')->delete();
        DB::table('packages')->insert([
            ['id' => 1, 'name' => 'Free', 'cost' => 0.00, 'cost_per' => 'Month', 'plan' => 'Free', 'status' => 1, 'featured' => 0, 'pricing_order' => 1, 'created_at' => \Carbon::now()],
            ['id' => 2, 'name' => 'Basic', 'cost' => 4.99, 'cost_per' => 'Month', 'plan' => 'Basic', 'status' => 1, 'featured' => 0, 'pricing_order' => 2, 'created_at' => \Carbon::now()],
            ['id' => 3, 'name' => 'Premium', 'cost' => 9.99, 'cost_per' => 'Month', 'plan' => 'Premium', 'status' => 1, 'featured' => 1, 'pricing_order' => 3, 'created_at' => \Carbon::now()],
            ['id' => 4, 'name' => 'Enterprise', 'cost' => 19.99, 'cost_per' => 'Month', 'plan' => 'Enterprise', 'status' => 1, 'featured' => 0, 'pricing_order' => 4, 'created_at' => \Carbon::now()],
        ]);
    }
}
