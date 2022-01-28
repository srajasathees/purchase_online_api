<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\Department;
use DB;
use App\Http\Resources\Department as DepartmentResource;
use Validator;

class DepartmentController extends BaseController
{
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {		
		$departments = Department::where([['status', '<>', 2]])->get();
		return $this->sendResponse(DepartmentResource::collection($departments), 'Departments fetched.');
		
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		$input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required',
			'status' => 'required'
        ]);
		
        if($validator->fails()){
            return $this->sendError($validator->errors());       
        }
		
		
		$department_details = [
			'name' => $request->get('name'),
			'status' => $request->get('status')
		];
		
		$department = Department::create($department_details);
		return $this->sendResponse(new DepartmentResource($department), 'Department created.');
		
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $department = Department::find($id);
		if (is_null($department)) {
            return $this->sendError('Department does not exist.');
        }
        return $this->sendResponse(new DepartmentResource($department), 'Department fetched.');
		
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
		$input = $request->all();

        $validator = Validator::make($input, [
            'name' => 'required',
			'status' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors());       
        }
		
		$department_details = [
			'name' => $request->get('name'),
			'status' => $request->get('status')
		];
		
		$department = Department::find($id);
		$department->update($department_details);
		
		return $this->sendResponse(new DepartmentResource($department), 'Department updated.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {		
		$dept = Department::find($id);
		$dept->update(array('status' => 2));
		return $this->sendResponse([], 'Department deleted.');
    }
}
