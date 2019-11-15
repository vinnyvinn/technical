<?php

namespace App\Http\Controllers;

use App\CheckList;
use App\Http\Resources\ChecklistResource;
use Illuminate\Http\Request;

class ChecklistsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(ChecklistResource::collection(CheckList::all()));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $name = '';
        if($request->get('checklist_file'))
        {
            $image = $request->get('checklist_file');
            $name = time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
            \Image::make($request->get('checklist_file'))->save(public_path('uploads/').$name);

        }

        $request['checklist_file'] = $name;
        $checklist = CheckList::create($request->except('check_list'));
        return response()->json(new ChecklistResource($checklist));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
         $name = '';
          if($request->get('checklist_file') && $request->get('checklist_file')!='null' && strlen($request->get('checklist_file'))>50)
        {
            $image = $request->get('checklist_file');
             $name = time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
            \Image::make($request->get('checklist_file'))->save(public_path('uploads/').$name);
        }
        $name ? $request['checklist_file'] = $name : '';
        CheckList::find($id)->update($request->except(['expiry_type','check_list','duration_type']));
        return response()->json(new ChecklistResource(CheckList::find($id)));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        CheckList::destroy($id);
        return response()->json($id);
    }
}
