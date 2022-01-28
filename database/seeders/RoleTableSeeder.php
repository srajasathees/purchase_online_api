<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$ArrRoles = array("Admin","Staff");
		
		$ArrPermissions['Admin'] = array(
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
										);
		
		$ArrPermissions['Staff'] = array(
											'purchase-list',
											'purchase-create',
											'purchase-edit',
											'purchase-delete'
										);
		
		foreach($ArrRoles as $key => $Value){
			$role = Role::create(['name' => $Value]);
			$permissions = Permission::pluck('name','id')->all();
			$ArrPer = [];
			foreach($permissions as $id => $name){
				if(in_array($name, $ArrPermissions[$Value])){
					$ArrPer[$id] = $id;
				}
			}
			
			$role->syncPermissions($ArrPer);
		}
		
    }
}
