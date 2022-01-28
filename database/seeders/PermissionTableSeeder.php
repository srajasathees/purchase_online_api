<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
			'role-list',
			'role-create',
			'role-edit',
			'role-delete',
			'user-list',
			'user-create',
			'user-edit',
			'user-delete',
			'setting-list',
			'setting-create',
			'setting-edit',
			'setting-delete',
			'department-list',
			'department-create',
			'department-edit',
			'department-delete',
			'purchase-list',
			'purchase-create',
			'purchase-edit',
			'purchase-delete'
		];
		
		foreach ($permissions as $permission) {
			Permission::create(['name' => $permission]);
		}
    }
}
