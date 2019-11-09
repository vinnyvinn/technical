<?php

namespace App\Http\Controllers;

use App\Http\Resources\JobcardResource;
use App\Jobcard;
use App\Machine;
use Carbon\Carbon;
use Illuminate\Http\Request;


class JobcardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(JobcardResource::collection(Jobcard::all()));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request['time_in'] = Carbon::parse($request->time_in)->format('H:i');
        $request['time_out'] = Carbon::parse($request->time_out)->format('H:i');
        $jobcard = Jobcard::create($request->all());
        $jobcard->update(['card_no' => substr('ESL-' . $jobcard->id . '-' . Machine::find($jobcard->machine_id)->code, 0, 20)]);
        Machine::find($jobcard->machine_id)->update([
            'current_readings' => $request->get('current_readings'),
            'next_readings' => $request->get('next_readings'),
            'next_service_date' => $request->get('next_service_date'),
            'fuel_balance_id' => $request->get('fuel_balance_id'),
        ]);
        if ($request->get('checklist_file')) {
            $image = $request->get('checklist_file');
            $name = time() . '.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
            \Image::make($request->get('checklist_file'))->save(public_path('uploads/') . $name);
            $jobcard->checklist_file = $name;
            $jobcard->save();
        }
        return response()->json(new JobcardResource($jobcard));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(new JobcardResource(Jobcard::find($id)));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request['time_in'] = Carbon::parse($request->time_in)->format('H:i');
        $request['time_out'] = Carbon::parse($request->time_out)->format('H:i');
        $request['card_no'] = substr('ESL-' . $id . '-' . Machine::find($request->machine_id)->code, 0, 20);
        $jobcard = Jobcard::find($id);
        $jobcard->update($request->except('service_types'));
        Machine::find($request->machine_id)->update([
            'current_readings' => $request->get('current_readings'),
            'next_readings' => $request->get('next_readings'),
            'next_service_date' => $request->get('next_service_date'),
            'fuel_balance_id' => $request->get('fuel_balance_id'),
        ]);
        if ($request->get('checklist_file') && $request->get('checklist_file') != 'undefined' && strlen($request->get('checklist_file')) > 50) {
            $image = $request->get('checklist_file');
            $name = time() . '.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
            \Image::make($request->get('checklist_file'))->save(public_path('uploads/') . $name);
            $jobcard->checklist_file = $name;
            $jobcard->save();
        }
        return response()->json(new JobcardResource(Jobcard::find($id)));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Jobcard::destroy($id);
        return response()->json($id);
    }
}
