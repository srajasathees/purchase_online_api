<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Models\Department;

class UserController extends Controller
{
	
	/**
	* Display a listing of the resource.
	*
	* @return \Illuminate\Http\Response
	*/
	public function index(Request $request)
	{
		$data = User::where([['status', '<>', '2'],['id', '<>', 1]])->get();		
		return view('users.index', compact('data'));
		
	}
	
	/**
	* Show the form for creating a new resource.
	*
	* @return \Illuminate\Http\Response
	*/
	public function create()
	{
		$roles = Role::where([['id', '<>', 1]])->pluck('name','name')->all();
		$departments = Department::where([['status', '=', '1']])->pluck('name','id')->all();
		
		return view('users.create',compact('roles','departments'));
	}
	
	/**
	* Store a newly created resource in storage.
	*
	* @param  \Illuminate\Http\Request  $request
	* @return \Illuminate\Http\Response
	*/
	public function store(Request $request)
	{
		$department_validation = [];
		$department_validation_msg = [];
		
		$roles = $request->get('roles');
		if($roles == 'Staff'){
			$department_validation = array("department_id" => "required");
			$department_validation_msg = array('department_id.required' => 'Department is required');
		}
		
		$arr_profile_photo_validation = [];
		if ($request->hasFile('profile_photo')) {
			if ($request->file('profile_photo')->isValid()) {
				$arr_profile_photo_validation = array('profile_photo' => 'mimes:jpeg,png,jpg|max:3000');
			}
		}
		
		$arr_contact_number_validation = [];
		if($request->get('contact_number') != ''){
			$arr_contact_number_validation = array('contact_number' => 'integer|max:10');
		}
		
		$validatedData = $request->validate(array_merge([
			'name' => 'required|regex:/^[a-zA-Z0-9\s]+$/',
			'email' => [
						'required',
						'email:rfc,dns',
						Rule::unique('users')->where(function ($query) {
							return $query->where([['status', '<>', '2']]);
						})
			],
			'password' => 'required|same:confirm-password',
			'roles' => 'required',
			'status' => 'required',
			'mobile_number' => 'max:10'
		], $arr_profile_photo_validation, $department_validation),
		array_merge([
			'name.required' => 'Name is required',
			'email.required' => 'Email is required',
			'password.required' => 'Password is required',
			'roles.required' => 'Role is required',
			'status.required' => 'Status is required'
        ], $department_validation_msg));
		
	
		$department_input = [];
		$budget_holder_input = [];
		if($roles == 'Staff'){
			$department_input = array("department_id" => $request->get('department_id'), "is_budget_holder" => $request->get('budget_holder'));
		}
		
		$country = 0;
		if($request->get('country') != ''){
			$country = $request->get('country');
		}
		
		$nationality = 0;
		if($request->get('nationality') != ''){
			$nationality = $request->get('nationality');
		}
		
		$user_details = [
			'name' => $request->get('name'),
			'email' => $request->get('email'),
			'password' => Hash::make($request->get('password')),
			'status' => $request->get('status'),
			'contact_number' => $request->get('contact_number'),
			'gender' => $request->get('gender'),
			'address' => $request->get('address'),
			'city' => $request->get('city'),
			'country' => $nationality,
			'nationality' => $nationality
		];
		
		//echo '<pre>';print_r(array_merge($user_details, $department_input));exit();
		
		$user = User::create(array_merge($user_details, $department_input));
		
		if($user){
			if ($request->hasFile('profile_photo')) {
				if ($request->file('profile_photo')->isValid()) {
					
					$extension = $request->profile_photo->extension();
					$request->profile_photo->storeAs('/public/users', $user->id.".".$extension);
					$url = Storage::url('users/'.$user->id.".".$extension);
					
					$user_details = User::find($user->id);
					$input['profile_photo'] = $url;
					$user_details->update($input);
				}
			}
			
			$user->assignRole($request->input('roles'));
		}
		
		return redirect()->route('users.index')->with('success','User created successfully');
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
			$user = User::find($id);
			return view('users.show',compact('user'));
		} else {
			return redirect()->route('users.index')->with('error','You don\'t have rights to view this user.');
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
			
			$user = User::find($id);
			$roles = Role::where([['id', '<>', 1]])->pluck('name','name')->all();			
			$userRole = $user->roles->pluck('name','name')->all();
			
			$departments = Department::where([['status', '=', '1']])->pluck('name','id')->all();
			
			return view('users.edit',compact('user','roles','userRole','departments'));
			
		} else {
			return redirect()->route('users.index')->with('error','You don\'t have rights to edit this user.');
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
			return redirect()->route('users.index')->with('error','You don\'t have rights to update this user.');
		}
		
		$arr_contact_number_validation = [];
		if($request->get('contact_number') != ''){
			$arr_contact_number_validation = array('contact_number' => 'integer|digits_between:8,10');
		}
		
		if ($request->hasFile('profile_photo')) { 
			if ($request->file('profile_photo')->isValid()) {
				
				$validatedData = $request->validate(array_merge([
					'name' => 'required',
					'email' => [
						'required',
						'email:rfc,dns',
						Rule::unique('users')->ignore($id)->where(function ($query) {
							return $query->where([['status', '<>', '2']]);
						})
					],
					'password' => 'same:confirm-password',
					'profile_photo' => 'mimes:jpeg,png|max:3000',
					'roles' => 'required',
					'status' => 'required'
				], $arr_contact_number_validation),
				[
					'name.required' => 'Name is required',
					'email.required' => 'Email is required',
					'password.required' => 'Password is required',
					'roles.required' => 'Role is required',
					'status.required' => 'Status is required',
					'profile_photo.min' => 'Your photo is too less, must be greater than 1 MB.',
					'profile_photo.max' => 'Your photo is too large, must be less than 3 MB.'
				]);
				
				$extension = $request->profile_photo->extension();
				$request->profile_photo->storeAs('/public/users', $id.".".$extension);
				$url = Storage::url('users/'.$id.".".$extension);
								
				$input = $request->all();				
				if(!empty($input['password'])){
					$input['password'] = Hash::make($input['password']);
				}else{
					$input = Arr::except($input,array('password'));
				}
				
				$user = User::find($id);
				
				$input['country'] = 0;
				if($request->get('country') != ''){
					$input['country'] = $request->get('country');
				}
				
				$input['nationality'] = 0;
				if($request->get('nationality') != ''){
					$input['nationality'] = $request->get('nationality');
				}
				
				$input['profile_photo'] = $url;
				
				$user->update($input);
				
				DB::table('model_has_roles')->where('model_id',$id)->delete();
				$user->assignRole($request->input('roles'));
								
				return redirect()->route('users.index')->with('success','User updated successfully');
			}
		} else {
						
			$validatedData = $request->validate(array_merge([
				'name' => 'required',
				'email' => [
					'required',
					'email:rfc,dns',
					Rule::unique('users')->ignore($id)->where(function ($query) {
						return $query->where([['status', '<>', '2']]);
					})
				],
				'password' => 'same:confirm-password',
				'roles' => 'required',
				'status' => 'required'
			], $arr_contact_number_validation),
			[
				'name.required' => 'Name is required',
				'email.required' => 'Email is required',
				'password.required' => 'Password is required',
				'roles.required' => 'Role is required',
				'status.required' => 'Status is required'
			]);
			
			$url = '';
			
			$input = $request->all();
			if(!empty($input['password'])){
				$input['password'] = Hash::make($input['password']);
			}else{
				$input = Arr::except($input,array('password'));
			}
			$user = User::find($id);
			
			$input['country'] = 0;
			if($request->get('country') != ''){
				$input['country'] = $request->get('country');
			}
			
			$input['nationality'] = 0;
			if($request->get('nationality') != ''){
				$input['nationality'] = $request->get('nationality');
			}
			
			$input['profile_photo'] = $url;
			
			$user->update($input);
			
			DB::table('model_has_roles')->where('model_id',$id)->delete();
			$user->assignRole($request->input('roles'));
							
			return redirect()->route('users.index')->with('success','User updated successfully');
		}
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
			return redirect()->route('users.index')->with('error','You don\'t have rights to delete this user.');
		}
		
		$user = User::find($id);
		$user->delete();
		return redirect()->route('users.index')->with('success','User deleted successfully');
	}
}