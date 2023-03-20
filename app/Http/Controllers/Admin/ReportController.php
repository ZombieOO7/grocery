<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Exports\JobExport;
use Illuminate\Http\Request;
use App\Helpers\ReportHelper;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests\Admin\JobReportFormRequest;
use App\Models\Job;
use App\Models\JobStatus;
use App\Models\Location;
use App\Models\Machine;
use App\Models\Problem;
use App\Models\User;

class ReportController extends BaseController
{
    private $helper;
    public $viewConstant = 'admin.reports.';
    public $job;
    public function __construct(ReportHelper $helper,Job $job, Location $location, User $user, Machine $machine)
    {
        $this->job = $job;
        $this->user = $user;
        $this->machine = $machine;
        $this->location = $location;
        $this->helper = $helper;
        $this->helper->mode = 'admin';
    }
    /**
     * -------------------------------------------------
     * | Display Comapny list                          |
     * |                                               |
     * | @return View                                  |
     * |------------------------------------------------
     */
    public function index()
    {
        try {
            $statusList = $this->statusList();
            $jobStatusList = $this->jobStatusList();
            $reportType = $this->reportType();
            return view($this->viewConstant . 'index', ['jobStatusList'=>@$jobStatusList,'statusList' => @$statusList, 'reportType' => @$reportType]);
        } catch (Exception $e) {
            abort(404);
        }
    }

    /**
     * ------------------------------------------------------
     * | Export job data to excel                           |
     * |                                                    |
     * | @return File                                       |
     * |-----------------------------------------------------
     */
    public function export(JobReportFormRequest $request){
        try {
            $exportType = $request->export_to;
            $reportData = $this->helper->getData($request);
            $headerData = $this->helper->headerData($request);
            if(count($reportData) >0)
            {
                $months = monthList();
                return Excel::download(new JobExport(@$reportData,@$headerData['title'],@$headerData['name'],@$request->report_type),$request->year.(($request->month!=null)?'_'.@$months[$request->month]:'').'_'.$headerData['title'].$exportType);
            }else{
                return redirect()->back()->with('error',__('admin/messages.not_found'));
            }
        } catch (Exception $e) {
            abort(404);
        }
    }

    /**
     * ------------------------------------------------------
     * | Display total job data by job status               |
     * |                                                    |
     * | @return Resonse                                    |
     * |-----------------------------------------------------
     */
    public function generate(Request $request){
        try {
            $exportType = $request->export_to;
            $reportData = $this->helper->getData($request);
            $headerData = $this->helper->headerData($request);
            if(count($reportData) >0)
            {
                return view('admin.exports.jobReport', ['reportData' => @$reportData, 'title'=>@$headerData['title'],'name'=>@$headerData['name'],'type'=>@$request->report_type]);
            }else{
                return response()->json(['msg' => __('admin/messages.not_found'), 'icon' => 'info']);
            }
        } catch (Exception $e) {
            return response()->json(['msg' => $e->getMessage(), 'icon' => 'info']);
        }
    }

    public function reportTypeData(Request $request){
        $list = [];
        $title = '';
        $name = '';
        switch($request->type){
            case 1:
                $location = Location::notDelete()->orderBy('title','asc')->pluck('title','id');
                $list = $this->mergeSelectOption2($location->toArray(),'location');
                $title = 'Location';
                $name = 'location_id';
            break;
            case 2:
                // $list = $this->locationList();
                // $title = 'Location';
                // $name = 'location_id';
                $machine = Machine::notDelete()->orderBy('title','asc')->pluck('title','id');
                // $list = $this->mergeSelectOption2($machine->toArray(),'Machines');
                $list = ['' => __('formname.select_type',['type'=>'Machines'])]+[0=>'Select All Machines']+ ['top20'=>'20 Machine with the highest job request']+$machine->toArray();
                $title = 'Machine';
                $name = 'machine_id';
            break;
            case 3:
                $problem = Problem::notDelete()->orderBy('title','asc')->pluck('title','id');
                $list = $this->mergeSelectOption2($problem->toArray(),' Problem');
                $title = 'Problem';
                $name = 'problem_id';
            break;
            case 4:
                $list = $this->allEngineerList();
                $title = 'Staff';
                $name = 'user_id';
            break;
            case 5:
                $list = $this->allEngineerList();
                $title = 'Staff';
                $name = 'user_id';
            break;
            case 6:
                // $list = $this->machineList();
                // $title = 'Machine';
                // $name = 'machine_id';
                $location = Location::notDelete()->orderBy('title','asc')->pluck('title','id');
                $list = $this->mergeSelectOption($location->toArray(),'location');
                $title = 'Location';
                $name = 'location_id';
            break;
            case 7:
                $list = $this->jobType();
                $title = 'Job Type';
                $name = 'job_type_id';
            break;
        }
        return view('admin.reports._add_filter',['name'=>@$name,'list'=>@$list,'title'=>@$title,'type'=>@$request->type]);
    }

    /**
     * add select option in dropdown
     * @return Array
     */
    public function mergeSelectOption2($a,$type)
    {
        return  ['' => __('formname.select_type',['type'=>@$type])]+[0=>'Select All '.@$type]+$a;
    }
}
