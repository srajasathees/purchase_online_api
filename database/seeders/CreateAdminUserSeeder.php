<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
			'name' => 'Super Admin',
			'email' => 'purchase@gmail.com',
			'password' => bcrypt('12345'),
			'status' => '1',
			'contact_number' => 552406260,
			'gender' => 1,
			'address' => "Dubai",
			'city' => "Dubai",
			'country' => 182,
			"nationality" => 182
		]);
		
		$role = Role::create(['name' => 'Super Admin']);
		$permissions = Permission::pluck('id','id')->all();
		$role->syncPermissions($permissions);
		$user->assignRole([$role->id]);
		
    }
}
