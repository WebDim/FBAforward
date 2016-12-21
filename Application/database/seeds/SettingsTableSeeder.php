<?php

use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('settings')->delete();

        \DB::table('settings')->insert(array(
            0 =>
                array(
                    'id' => 1,
                    'key_cd' => 'SITE_TITLE',
                    'type' => 'TEXT',
                    'display_value' => 'Site Title',
                    'value' => 'Laraship Pro',
                    'created_at' => '2016-03-31 07:04:15',
                    'updated_at' => '2016-03-31 12:18:21',
                ),
            1 =>
                array(
                    'id' => 2,
                    'key_cd' => 'SHORT_SITE_TITLE',
                    'type' => 'TEXT',
                    'display_value' => 'Short Site Title',
                    'value' => 'M.',
                    'created_at' => '2016-03-31 07:04:15',
                    'updated_at' => NULL,
                ),
            2 =>
                array(
                    'id' => 3,
                    'key_cd' => 'SITE_LOGO',
                    'type' => 'FILE',
                    'display_value' => 'Site Logo',
                    'value' => 'SITE_LOGO.png',
                    'created_at' => '2016-03-31 07:04:15',
                    'updated_at' => '2016-03-31 07:39:53',
                ),
            3 =>
                array(
                    'id' => 4,
                    'key_cd' => 'FOOTER',
                    'type' => 'TEXT',
                    'display_value' => 'Footer',
                    'value' => '<div class="pull-right hidden-xs"> <b>Laravel LarashipPro</b></div> 	<strong>Copyright © 2016</strong> All rights reserved.',
                    'created_at' => '2016-03-31 07:04:15',
                    'updated_at' => '2016-03-31 12:09:55',
                ),
            4 =>
                array(
                    'id' => 5,
                    'key_cd' => 'DEFAUTL_USER_ROLE',
                    'type' => 'TEXT',
                    'display_value' => 'Default User Role',
                    'value' => '2',
                    'created_at' => '2016-03-31 07:04:15',
                    'updated_at' => NULL,
                ),
            5 =>
                array(
                    'id' => 6,
                    'key_cd' => 'DEFAULT_PACKAGE_ID',
                    'type' => 'TEXT',
                    'display_value' => 'Default User Package',
                    'value' => '1',
                    'created_at' => '2016-03-31 07:04:15',
                    'updated_at' => NULL,
                ),
            6 =>
                array(
                    'id' => 7,
                    'key_cd' => 'JOB_TITLES',
                    'type' => 'SELECT',
                    'display_value' => 'Job Titles',
                    'value' => '["Engineer","Developer","Web designer", "Admin", "Member"]',
                    'created_at' => '2016-03-31 07:04:15',
                    'updated_at' => NULL,
                ),
            7 =>
                array(
                    'id' => 8,
                    'key_cd' => 'DEFAULT_CURRENCY',
                    'type' => 'TEXT',
                    'display_value' => 'Default Currency',
                    'value' => 'usd',
                    'created_at' => '2016-03-31 07:04:15',
                    'updated_at' => NULL,
                ),
            8 =>
                array(
                    'id' => 9,
                    'key_cd' => 'INFO_EMAIL',
                    'type' => 'TEXT',
                    'display_value' => 'Info Email',
                    'value' => 'info@laraship.com',
                    'created_at' => '2016-03-31 07:04:15',
                    'updated_at' => NULL,
                ),
            9 =>
                array(
                    'id' => 10,
                    'key_cd' => 'SUPPORT_PHONE',
                    'type' => 'TEXT',
                    'display_value' => 'Support Phone',
                    'value' => '+90-897-678-44',
                    'created_at' => '2016-03-31 07:04:15',
                    'updated_at' => NULL,
                ),
            10 =>
                array(
                    'id' => 11,
                    'key_cd' => 'FRONTEND_FOOTER',
                    'type' => 'TEXT',
                    'display_value' => 'Front End Footer',
                    'value' => '© 2016 Laraship | By : <a href="http://laraship.com" target="_blank">Laraship</a>',
                    'created_at' => '2016-03-31 07:04:15',
                    'updated_at' => '2016-03-31 12:11:26',
                ),
            11 =>
                array(
                    'id' => 12,
                    'key_cd' => 'LOGO_WIDTH',
                    'type' => 'TEXT',
                    'display_value' => 'Logo Width',
                    'value' => '150px',
                    'created_at' => '2016-03-31 07:43:36',
                    'updated_at' => '2016-03-31 07:43:36',
                ),
            12 =>
                array(
                    'id' => 13,
                    'key_cd' => 'POSTS_PER_PAGE',
                    'type' => 'TEXT',
                    'display_value' => 'Posts Per Page',
                    'value' => '3',
                    'created_at' => '2016-03-31 11:17:23',
                    'updated_at' => '2016-03-31 11:17:23',
                ),
            13 =>
                array(
                    'id' => 14,
                    'key_cd' => 'ADDRESS',
                    'type' => 'TEXT',
                    'display_value' => 'Address',
                    'value' => 'JOHN SMITH, <br/>100 MAIN ST, PO BOX 1022<br/>SEATTLE WA 98104, USA',
                    'created_at' => '2016-03-31 11:17:23',
                    'updated_at' => '2016-03-31 11:17:23',
                ),
            14 =>
                array(
                    'id' => 15,
                    'key_cd' => 'MAP_LONGITUDE',
                    'type' => 'TEXT',
                    'display_value' => 'Map Longitude',
                    'value' => '-122.335167',
                    'created_at' => '2016-03-31 11:17:23',
                    'updated_at' => '2016-03-31 11:17:23',
                ),
            15 =>
                array(
                    'id' => 16,
                    'key_cd' => 'MAP_LATITUDE',
                    'type' => 'TEXT',
                    'display_value' => 'Map Latitude',
                    'value' => '47.608013',
                    'created_at' => '2016-03-31 11:17:23',
                    'updated_at' => '2016-03-31 11:17:23',
                ),
            16 =>
                array(
                    'id' => 17,
                    'key_cd' => 'MENUS_LOCATION',
                    'type' => 'SELECT',
                    'display_value' => 'Menus Location',
                    'value' => '["HEADER","FOOTER","LEFT_SIDE", "RIGHT_SIDE"]',
                    'created_at' => \Carbon\Carbon::now(),
                    'updated_at' => \Carbon\Carbon::now(),
                ),
        ));


    }
}
