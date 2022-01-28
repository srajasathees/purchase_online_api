<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;

class RoleController extends Controller
{
	
	/**
	* Display a listing of the resource.
	*
	* @return \Illuminate\Http\Response
	*/
	public function index(Request $request)
	{
		$roles = Role::where([['id', '<>', 1]])->get();
		return view('roles.index', compact('roles'));
	}
	
	/**
	* Show the form for creating a new resource.
	*
	* @return \Illuminate\Http\Response
	*/
	public function create()
	{
		$permission = Permission::get();
		return view('roles.create',compact('permission'));
	}
	
	/**
	* Store a newly created resource in storage.
	*
	* @param  \Illuminate\Http\Request  $request
	* @return \Illuminate\Http\Response
	*/
	public function store(Request $request)
	{
		$validatedData = $request->validate([
			'name' => 'required|alpha|unique:roles,name',
			'permission' => 'required',
		],
		[
			'name.required' => 'Name is required',
			'permission.required' => 'Permission is required',
        ]);
				
		$role = Role::create(['name' => $request->input('name')]);
		$role->syncPermissions($request->input('permission'));
		return redirect()->route('roles.index')->with('success','Role created successfully');
	}
	
	/**
	* Display the specified resource.
	*
	* @param  int  $id
	* @return \Illuminate\Http\Response
	*/
	public function show($id)
	{
		if($id != 1){
			$role = Role::find($id);
			$rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
								->where("role_has_permissions.role_id",$id)
								->get();
			return view('roles.show',compact('role','rolePermissions'));
		} else {
			return redirect()->route('roles.index')->with('error','You don\'t have rights to view this role.');
		}
	}
	
	/**
	* Show the form for editing the specified resource.
	*
	* @param  int  $id
	* @return \Illuminate\Http\Response
	*/
	public function edit($id)
	{
		if($id != 1){
			$role = Role::find($id);
			$permission = Permission::get();
			$rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
								->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
								->all();
			return view('roles.edit',compact('role','permission','rolePermissions'));
		} else {
			return redirect()->route('roles.index')->with('error','You don\'t have rights to edit this role.');
		}
	}
	
	/**
	* Update the specified resource in storage.
	*
	* @param  \Illuminate\Http\Request  $request
	* @param  int  $id
	* @return \Illuminate\Http\Response
	*/
	public function update(Request $request, $id)
	{
		if($id == 1){
			return redirect()->route('roles.index')->with('error','You don\'t have rights to update this role.');
		}
		
		$validatedData = $request->validate([
			'name' => 'required|alpha|unique:roles,name,'.$id,
			'permission' => 'required',
		],
		[
			'name.required' => 'Name is required',
			'permission.required' => 'Permission is required',
        ]);		
		
		$role = Role::find($id);
		$role->name = $request->input('name');
		$role->save();
		$role->syncPermissions($request->input('permission'));
		return redirect()->route('roles.index')->with('success','Role updated successfully');
	}
	
	/**
	* Remove the specified resource from storage.
	*
	* @param  int  $id
	* @return \Illuminate\Http\Response
	*/
	public function destroy($id)
	{
		if($id == 1){
			return redirect()->route('roles.index')->with('error','You don\'t have rights to delete this role.');
		}
		
		$role_users = DB::table('model_has_roles')
					->where([['role_id', '=', $id]])
					//->dd();
					->exists();
		if(!$role_users){
			DB::table("roles")->where('id',$id)->delete();
			return redirect()->route('roles.index')->with('success','Role deleted successfully');
		} else {
			return redirect()->route('roles.index')->with('error','Role is associated with users.');
		}
	}
}