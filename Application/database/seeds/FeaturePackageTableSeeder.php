<?php

use Illuminate\Database\Seeder;

class FeaturePackageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('feature_package')->delete();
        DB::table('feature_package')->insert([
            ['package_id' => 1, 'feature_id' => 1, 'spec' => '24/7 Support', 'created_at' => \Carbon::now()],
            ['package_id' => 1, 'feature_id' => 2, 'spec' => '5 GB Space', 'created_at' => \Carbon::now()],
            ['package_id' => 1, 'feature_id' => 3, 'spec' => '4 GB RAM', 'created_at' => \Carbon::now()],
            ['package_id' => 1, 'feature_id' => 4, 'spec' => '1 Core CPU', 'created_at' => \Carbon::now()],
            ['package_id' => 2, 'feature_id' => 1, 'spec' => '24/7 Support', 'created_at' => \Carbon::now()],
            ['package_id' => 2, 'feature_id' => 2, 'spec' => '10 GB Space', 'created_at' => \Carbon::now()],
            ['package_id' => 2, 'feature_id' => 3, 'spec' => '4 GB RAM', 'created_at' => \Carbon::now()],
            ['package_id' => 2, 'feature_id' => 4, 'spec' => '2 Core CPU', 'created_at' => \Carbon::now()],
            ['package_id' => 2, 'feature_id' => 5, 'spec' => '10 Add-on Domain', 'created_at' => \Carbon::now()],
            ['package_id' => 3, 'feature_id' => 1, 'spec' => '24/7 Support', 'created_at' => \Carbon::now()],
            ['package_id' => 3, 'feature_id' => 2, 'spec' => '20 GB Space', 'created_at' => \Carbon::now()],
            ['package_id' => 3, 'feature_id' => 3, 'spec' => '8 GB RAM', 'created_at' => \Carbon::now()],
            ['package_id' => 3, 'feature_id' => 4, 'spec' => '4 Core CPU', 'created_at' => \Carbon::now()],
            ['package_id' => 3, 'feature_id' => 5, 'spec' => '10 Add-on Domain', 'created_at' => \Carbon::now()],
            ['package_id' => 3, 'feature_id' => 6, 'spec' => '100 Email Address', 'created_at' => \Carbon::now()],
            ['package_id' => 4, 'feature_id' => 1, 'spec' => '24/7 Support', 'created_at' => \Carbon::now()],
            ['package_id' => 4, 'feature_id' => 2, 'spec' => '50 GB Space', 'created_at' => \Carbon::now()],
            ['package_id' => 4, 'feature_id' => 3, 'spec' => '16 GB RAM', 'created_at' => \Carbon::now()],
            ['package_id' => 4, 'feature_id' => 4, 'spec' => '8 Core CPU', 'created_at' => \Carbon::now()],
            ['package_id' => 4, 'feature_id' => 5, 'spec' => '10 Add-on Domain', 'created_at' => \Carbon::now()],
            ['package_id' => 4, 'feature_id' => 6, 'spec' => '500 Email Address', 'created_at' => \Carbon::now()]
        ]);
    }
}
