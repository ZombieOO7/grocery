<?php
namespace App\Helpers;
use App\Models\Job;
use App\Models\JobStatus;
use App\Models\Location;
use App\Models\Machine;
use App\Models\Problem;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class ReportHelper extends BaseHelper
{
    protected $jobStatus, $location;
    public function __construct(JobStatus $jobStatus, Job $job, Location $location, Machine $machine, Problem $problem, User $user)
    {
        $this->jobStatus = $jobStatus;
        $this->job = $job;
        $this->location = $location;
        $this->machine = $machine;
        $this->problem = $problem;
        $this->user = $user;
        parent::__construct();
    }
    /**
     * ------------------------------------------------------
     * | Get job list                                       |
     * |                                                    |
     * |-----------------------------------------------------
     */
    public function list()
    {
        return $this->jobStatus::orderBy('id', 'desc');
    }
    /**
     * ------------------------------------------------------
     * | Get report data list                               |
     * |                                                    |
     * |-----------------------------------------------------
     */
    public function getData($request){
        $reportData = [];
        switch($request->report_type){
            case 1: //By Location
                $query =$this->location::where(function($query) use($request){
                            if($request->location_id != null && $request->location_id !='' && $request->location_id !=0){
                                $query->whereId($request->location_id);
                            }
                        })->orderBy('title','asc');
                $reportData =   $this->commonQuery($query,$request);
                $location = $this->location::find($request->location_id);
                $locationName = @$location->title; 
            break;
            case 2: //By Machine
                $query =$this->machine::where(function($query) use($request){
                            if($request->location_id != null && $request->location_id !=''){
                                $query->whereLocationId($request->location_id);
                            }
                            if($request->machine_id != null && $request->machine_id !='' && $request->machine_id !=0 && $request->machine_id !='top20'){
                                $query->whereId($request->machine_id);
                            }
                        })
                        ->orderBy('id','desc')
                        ->withCount(['jobs']);
                $reportData = $this->commonQuery($query,$request);
                if($request->machine_id =='top20'){
                    $reportData = $reportData->sortByDesc('jobs_count')->take(20);
                }
            break;
            case 3: //By Problem
                $query =$this->problem::where(function($query) use($request){
                            if($request->problem_id != null && $request->problem_id !='' && $request->problem_id !=0){
                                $query->whereId($request->problem_id);
                            }
                        })->orderBy('title','asc');
                $reportData = $this->commonQuery($query,$request);
            break;
            case 4: //By fitter engineer
                $query =$this->user::where(function($query) use($request){
                            if($request->user_id != null && $request->user_id !=''){
                                $query->whereId($request->user_id);
                            }
                        })->whereNull('deleted_at')->whereNotNull('email_verified_at')
                        ->whereUserType(2)
                        ->orderBy('first_name','asc');
                $reportData = $this->commonQuery($query,$request);
            break;
            case 5: //By individual fitter 
                $reportData = $this->job::where(function($query) use($request){
                                $query->whereYear('created_at',$request->year);
                                if($request->month != null)
                                    $query->whereMonth('created_at',$request->month);
                                if($request->date != null)
                                    $query->whereDay('created_at',$request->date);
                                if($request->user_id != null && $request->user_id !='')
                                    $query->whereAssignedTo($request->user_id);
                                })->orderBy('created_at','desc')
                                ->get();
            break;
            case 6: //By individual machine
                $reportData =   $this->job::where(function($query) use($request){
                                    $query->whereYear('created_at',$request->year);
                                    if($request->month != null)
                                        $query->whereMonth('created_at',$request->month);
                                    if($request->date != null)
                                        $query->whereDay('created_at',$request->date);
                                    if($request->location_id != null && $request->location_id !='')
                                        $query->whereLocationId($request->location_id);
                                    if($request->machine_id != null && $request->machine_id !='')
                                        $query->whereMachineId($request->machine_id);
                                    })
                                ->whereJobStatusId(4)
                                ->orderBy('created_at','desc')
                                ->get();
            break;
            case 7: //By summury of simmilar job
                $reportData =   $this->job::where(function($query) use($request){
                                    if($request->job_type_id != null){
                                        if($request->job_type_id == 5 || $request->job_type_id == 6)
                                            $query->whereJobStatusId($request->job_type_id);
                                        else
                                            $query->wherePriority($request->job_type_id);
                                    }
                                    $query->whereYear('created_at',$request->year);
                                    if($request->month != null)
                                        $query->whereMonth('created_at',$request->month);
                                    if($request->date != null)
                                        $query->whereDay('created_at',$request->date);
                                    })
                                ->orderBy('created_at','desc')
                                ->get();
            break;
            case 8: //By Decline Work Order
                $reportData =   $this->job::where(function($query) use($request){
                                    $query->whereYear('created_at',$request->year);
                                    if($request->month != null)
                                        $query->whereMonth('created_at',$request->month);
                                    if($request->date != null)
                                        $query->whereDay('created_at',$request->date);
                                    })
                                ->orderBy('created_at','desc')
                                ->whereJobStatusId(5)
                                ->get();
            break;
            case 9: //By KIV Work Order
                $reportData =   $this->job::where(function($query) use($request){
                                    $query->whereYear('created_at',$request->year);
                                    if($request->month != null)
                                        $query->whereMonth('created_at',$request->month);
                                    if($request->date != null)
                                        $query->whereDay('created_at',$request->date);
                                    
                                    })
                                ->orderBy('created_at','desc')
                                ->whereJobStatusId(6)
                                ->get();
            break;
        }
        return $reportData;
    }

    /**
     * ------------------------------------------------------
     * | Get common code for report data                    |
     * |                                                    |
     * | @return Array                                      |
     * |-----------------------------------------------------
     */
    public function commonQuery($q,$request){
        $reportData = $q->with(['jobs'=>function ($query) use($request) {
                            $query->whereYear('created_at',$request->year);
                            if($request->month != null)
                                $query->whereMonth('created_at',$request->month);
                            if($request->date != null)
                                $query->whereDay('created_at',$request->date);
                        }])
                        ->get();
        foreach($reportData as $key => $data){
            // $reportData[$key]['job_request'] = $data->jobs->where('job_status_id',1)->count();
            $reportData[$key]['total_job_request'] = $data->jobs->count();
            $reportData[$key]['job_request'] = $data->jobs->where('job_status_id',1)->count();
            $reportData[$key]['assigned'] = $data->jobs->where('job_status_id',2)->count();
            $reportData[$key]['work_order'] = $data->jobs->where('job_status_id',3)->count();
            $reportData[$key]['complete'] = $data->jobs->where('job_status_id',4)->count();
            $reportData[$key]['declined'] = $data->jobs->where('job_status_id',5)->count();
            $reportData[$key]['kiv'] = $data->jobs->where('job_status_id',6)->count();
            $reportData[$key]['unable_to_complete'] = $data->jobs->where('job_status_id',7)->count();
            if($request->report_type != 4){
                $reportData[$key]['pending'] = $data->jobs//->where('job_status_id',4)
                ->where('priority',0)->count();
                $reportData[$key]['low'] = $data->jobs//->where('job_status_id',4)
                                            ->where('priority',2)->count();
                $reportData[$key]['medium'] = $data->jobs//->where('job_status_id',4)
                                            ->where('priority',3)->count();
                $reportData[$key]['high'] = $data->jobs//->where('job_status_id',4)
                                            ->where('priority',4)->count();
            }else{
                $reportData[$key]['pending'] = $data->jobs//->where('job_status_id',4)
                ->where('priority',0)->count();
                $reportData[$key]['low'] = $data->jobs->where('priority',2)->count();
                $reportData[$key]['medium'] = $data->jobs->where('priority',3)->count();
                $reportData[$key]['high'] = $data->jobs->where('priority',4)->count();
            }
            $reportData[$key]['incomplete'] = $data->jobs->where('job_status_id','!=',4)->count();
        }
        return $reportData;
    }

    public function headerData($request){
        switch($request->report_type){
            case 1:
                $title = 'Location';
            break;
            case 2:
                $location = $this->location::find($request->location_id);
                $name = @$location->title; 
                $title = 'Machine';
            break;
            case 3:
                $title = 'Problem';
            break;
            case 4:
                $title = 'Staff';
            break;
            case 5:
                $title = 'Staff';
                $user = $this->user::whereId($request->user_id)->first();
                $name = $user->full_name_text;
            break;
            case 6:
                $location = $this->location::find($request->location_id);
                $locationName = @$location->title; 
                $title = 'Machine';
                if($request->machine_id != null){
                    $machine = $this->machine::whereId($request->machine_id)->first();
                    $machineName = isset($machine->title)?' ('.$machine->title.')':'';
                }
                $name = @$locationName.@$machineName;
            break;
            case 7:
                $title = 'summary_of_similar_job';
            break;
            case 8:
                $title = 'decline_work_order';
            break;
            case 9:
                $title = 'kiv_work_order';
            break;
        }
        return ['title'=>@$title,'name'=>@$name];
    }
}