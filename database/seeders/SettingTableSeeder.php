<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Setting;

class SettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $setting = Setting::create([
			'project_title' => 'Purchase Online',
			'name' => 'Adminstrator',
			'email' => 'school@gmail.com',
			'logo' => '',
			'currency' => 'AED'
		]);
    }
}
