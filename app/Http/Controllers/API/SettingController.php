<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $settings = Setting::all();
		return view('settings.index', compact('settings'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('settings.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ($request->hasFile('logo')) {
			if ($request->file('logo')->isValid()) {
				$this->validate($request, [
					'project_title' => 'required',
					'name' => 'required',
					'email' => 'required',
					'logo' => 'mimes:jpeg,png|max:3000',
				]);
				
				$extension = $request->logo->extension();
				$request->logo->storeAs('/public', $request->get('project_title').".".$extension);
				$url = Storage::url($request->get('project_title').".".$extension);
				
                $setting_details = [
					'project_title' => $request->get('project_title'),
					'name' => $request->get('name'),
					'email' => $request->get('email'),
					'logo' => $url,
				];
			}
		} else {
			$this->validate($request, [
					'project_title' => 'required',
					'name' => 'required',
					'email' => 'required',
				]);
			
			$setting_details = [
					'project_title' => $request->get('project_title'),
					'name' => $request->get('name'),
					'email' => $request->get('email'),
				];
		}
		
		$setting = Setting::create($setting_details);
		return redirect()->route('settings.index')->with('success','Settings created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $setting = Setting::find($id);
		return view('settings.show',compact('setting'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $setting = Setting::find($id);
		return view('settings.edit',compact('setting'));
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
        if ($request->hasFile('logo')) {
			if ($request->file('logo')->isValid()) {
				$this->validate($request, [
					'project_title' => 'required',
					'name' => 'required',
					'email' => 'required',
					'logo' => 'mimes:jpeg,png|max:3000',
				]);
				
				$extension = $request->logo->extension();
				$request->logo->storeAs('/public', $request->get('project_title').".".$extension);
				$url = Storage::url($request->get('project_title').".".$extension);
				
                $setting_details = [
					'project_title' => $request->get('project_title'),
					'name' => $request->get('name'),
					'email' => $request->get('email'),
					'logo' => $url,
				];
			}
		} else {
			$this->validate($request, [
					'project_title' => 'required',
					'name' => 'required',
					'email' => 'required',
				]);
			
			$setting_details = [
					'project_title' => $request->get('project_title'),
					'name' => $request->get('name'),
					'email' => $request->get('email'),
				];
		}
        
				
		$setting = Setting::find($id);
		$setting->update($setting_details);
		return redirect()->route('settings.index')->with('success','Settings updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
