<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobStatus;
use App\Models\Machine;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class DashboardController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $users = $this->usersList()
                ->whereNotNull('email_verified_at')
                ->take(5)->get();
        return view('admin.dashboard',['users'=>$users]);
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
        //
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
        //
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

    public function orderbylocation(Request $request){
        $type = $request->type;
        $flag = false;
        if($type ==1){
            $fromMonth = explode('-',$request->from_month);
            $fromDate = $fromMonth[1].'-'.$fromMonth[0].'-01';
            $toMonth = explode('-',$request->to_month);
            $toDate = $toMonth[1].'-'.$toMonth[0].'-01';
            $toDate = date("Y-m-t", strtotime($toDate));

            $period = CarbonPeriod::create($fromDate, '1 month', $toDate);
            foreach ($period as $key => $dt) {
                    $months[$key] =  $dt->format("Y-m");
                    $startDate = $dt->format('Y-m-d');
                    $endDate = $dt->format('Y-m-t');
                    $statusData = JobStatus::withCount(['jobs'=>function($q) use($startDate,$endDate,$request){
                                $q->where('location_id',$request->location_id);
                                $q->whereBetween('created_at',[$startDate,$endDate]);
                            }])->get();
                    foreach($statusData as $k => $data){
                        $jobStatusData[$data->title][] = $data->jobs_count;
                        if($data->jobs_count > 0){
                            $flag = true;
                        }
                    }
            }
        }
        if($type ==2){
            $machines =  Machine::whereHas('jobs',function($q) use($request){
                            $q->where('assigned_to',$request->assigned_to);
                        })->get();
            $months = $machines->pluck('title');
            foreach($machines as $machine){
                $statusData =   JobStatus::withCount(['jobs'=>function($q) use($machine,$request){
                                    $q->where('machine_id',$machine->id);
                                    $q->where('assigned_to',$request->assigned_to);
                                }])->get();
                foreach($statusData as $k => $data){
                    $jobStatusData[$data->title][] = $data->jobs_count;
                    if($data->jobs_count > 0){
                        $flag = true;
                    }
                }
            }
        }
        if($type ==3){
            $machines = Machine::where('location_id',$request->location_id)
                        ->get();
            $months = $machines->pluck('title');
            foreach($machines as $machine){
                $statusData =   JobStatus::withCount(['jobs'=>function($q) use($machine){
                                    $q->where('machine_id',$machine->id);
                                }])->get();
                foreach($statusData as $k => $data){
                    $jobStatusData[$data->title][] = $data->jobs_count;
                    if($data->jobs_count > 0){
                        $flag = true;
                    }
                }
            }
        }
        if($flag == false){
            return response()->json(['status'=>'fail','icon' => 'info','msg'=>__('formname.not_found')]);
        }
        return response()->json(['status'=>'success','months'=>@$months,'chartData'=>@$jobStatusData]);
    }
}