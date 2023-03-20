<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\MachineHelper;
use App\Http\Requests\Admin\MachineFormRequest;
use App\Models\Job;
use Exception;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Redirect;

class MachineController extends BaseController
{
    private $helper;
    public $viewConstant = 'admin.machine.';
    public function __construct(MachineHelper $helper, Job $job)
    {
        $this->helper = $helper;
        $this->job = $job;
        $this->helper->mode = 'admin';
    }
    /**
     * -------------------------------------------------
     * | Display Machine list                          |
     * |                                               |
     * | @return View                                  |
     * |------------------------------------------------
     */
    public function index()
    {
        try {
            $statusList = $this->statusList();
            return view($this->viewConstant . 'index', ['statusList' => @$statusList]);
        } catch (Exception $e) {
            abort(404);
        }
    }

    /**
     * -------------------------------------------------
     * | Get machine datatable data                    |
     * |                                               |
     * | @param Request $request                       |
     * | @return View                                  |
     * |------------------------------------------------
     */
    public function getdata(Request $request)
    {
        try {
            $machine = $this->helper->list();
            $machineList = $machine->where(function ($query) use ($request) {
                if ($request->status) {
                    $query->activeSearch($request->status);
                }
                if($request->location){
                    $query->whereLocationId($request->location);
                }
            })->get();
            return Datatables::of($machineList)
                ->addColumn('action', function ($machine) {
                    return $this->getPartials($this->viewConstant . '_add_action', ['machine' => @$machine]);
                })
                ->editColumn('status', function ($machine) {
                    return $this->getPartials($this->viewConstant . '_add_status', ['machine' => @$machine]);
                })
                ->editColumn('created_at', function ($machine) {
                    return @$machine->proper_created_at;
                })
                ->editColumn('location_id', function ($machine) {
                    return $this->getPartials($this->viewConstant .'_add_message', ['machine' => @$machine->location,'title'=>__('formname.location.title')]);
                })
                ->editColumn('title', function ($machine) {
                    return $this->getPartials($this->viewConstant .'_add_message', ['machine' => $machine, 'title'=>__('formname.machine.title')]);
                })
                ->addColumn('checkbox', function ($machine) {
                    return $this->getPartials($this->viewConstant . '_add_checkbox', ['machine' => @$machine]);
                })
                ->addColumn('print', function ($machine) {
                    return $this->getPartials($this->viewConstant . '_add_print', ['machine' => @$machine]);
                })
                ->rawColumns(['title','created_at', 'checkbox', 'action', 'status','location_id','print'])
                ->make(true);
        } catch (Exception $e) {
            abort('404');
        }
    }

    /**
     * -------------------------------------------------
     * | Create machine page                           |
     * |                                               |
     * | @param $id                                    |
     * | @return View                                  |
     * |------------------------------------------------
     */
    public function create($uuid = null)
    {
        try {
            if (isset($uuid)) {
                $machine = $this->helper->detail($uuid);
            }
            $statusList = $this->statusList();
            $locationList = $this->locationList();
            $title = isset($uuid) ? trans('formname.machine.update') : trans('formname.machine.create');
            return view($this->viewConstant . 'create', ['machine' => @$machine, 'title' => @$title, 'statusList' => @$statusList,'locationList'=>@$locationList]);
        } catch (Exception $e) {
            abort(404);
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    /**
     * -------------------------------------------------
     * | Store machinne details                        |
     * |                                               |
     * | @param SubjectFormRequest $request            |
     * | @return View                                  |
     * |------------------------------------------------
     */
    public function store(MachineFormRequest $request, $uuid = null)
    {
        $this->helper->dbStart();
        try {
            array_set($request,'qr_code',$this->generateRandomString(8));
            $this->helper->store($request, $uuid);
            $this->helper->dbEnd();
            if ($request->has('id') && !empty($request->id)) {
                $msg = __('admin/messages.action_msg', ['action' => __('admin/messages.updated'), 'type' => 'Machine']);
            } else {
                $msg = __('admin/messages.action_msg', ['action' => __('admin/messages.created'), 'type' => 'Machine']);
            }
            return redirect()->back()->with('message', $msg);
        } catch (Exception $e) {
            $this->helper->dbRollBack();
            return Redirect::back()->with('error', $e->getMessage());
            abort('404');
        }
    }
    /**
     * -------------------------------------------------
     * | Delete machine details                        |
     * |                                               |
     * | @param Request $request                       |
     * | @return Response                              |
     * |------------------------------------------------
     */
    public function destroy(Request $request)
    {
        $this->helper->dbStart();
        try {
            if (isset($request->id)) {
                $this->helper->delete($request->id);
                $this->helper->dbEnd();
                $msg = __('admin/messages.action_msg', ['action' => __('admin/messages.deleted'), 'type' => 'Machine']);
                return response()->json(['msg' => $msg, 'icon' => 'success']);
            } else {
                return response()->json(['msg' => __('admin/messages.not_found'), 'icon' => 'info']);
            }
        } catch (Exception $e) {
            $this->helper->dbRollBack();
            return response()->json(['msg' => $e->getMessage(), 'icon' => 'info']);
        }
    }
    /**
     * -------------------------------------------------
     * | Delete multiple machine                       |
     * |                                               |
     * | @param Request $request                       |
     * | @return Response                              |
     * |------------------------------------------------
     */
    public function multidelete(Request $request)
    {
        // dd($request->all());
        $this->helper->dbStart();
        try {
            $this->helper->dbEnd();
            if ($request->action == config('constant.inactive') || $request->action == config('constant.active')) {
                $this->helper->multiDelete($request);
                $action = ($request->action == config('constant.active')) ? __('admin/messages.active') : __('admin/messages.inactive');
                $msg = __('admin/messages.action_msg', ['action' => $action, 'type' => 'Machine']);
                return response()->json(['msg' => $msg, 'icon' => 'success']);
            }else if($request->action == config('constant.print')){
                $path = $this->helper->multiDelete($request);
                return response()->json(['path' => @$path, 'icon' => 'success']);
            }else {
                $this->helper->multiDelete($request);
                $msg = __('admin/messages.action_msg', ['action' => __('admin/messages.deleted'), 'type' => 'Machine']);
                return response()->json(['msg' => @$msg, 'icon' => 'success']);
            }
        } catch (Exception $e) {
            $this->helper->dbRollBack();
            return response()->json(['msg' => $e->getMessage(), 'icon' => 'info']);
        }
    }
    /**
     * -------------------------------------------------
     * | Update machine status details                 |
     * |                                               |
     * | @param Request $request                       |
     * | @return View                                  |
     * |------------------------------------------------
     */
    public function updateStatus(Request $request)
    {
        $this->helper->dbStart();
        try {
            if (isset($request->id)) {
                $msg = $this->helper->statusUpdate($request->id);
                $this->helper->dbEnd();
                return response()->json(['msg' => $msg, 'icon' => 'success']);
            } else {
                return response()->json(['msg' => __('admin/messages.not_found'), 'icon' => 'info']);
            }
        } catch (Exception $e) {
            $this->helper->dbRollBack();
            return response()->json(['msg' => $e->getMessage(), 'icon' => 'info']);
        }
    }
    /**
     * -------------------------------------------------
     * | Before delete check machine is used in jobs   |
     * |                                               |
     * | @param Request $request                       |
     * | @return Response                              |
     * |------------------------------------------------
     */
    public function jobStatus(Request $request){
        $machine = $this->helper->detail($request->id);
        $counts = $this->job::whereMachineId($machine->id)->count();
        $msg = __('admin/messages.delete_data',['title'=>'Machine', 'type'=>'job']);
        return response()->json(['msg' => $msg, 'counts' => @$counts]);
    }
    /**
     * -------------------------------------------------
     * | Before delete check machine is used in jobs   |
     * |                                               |
     * | @param Request $request                       |
     * | @return Response                              |
     * |------------------------------------------------
     */
    public function  multipleJobStatus(Request $request){
        $counts = $this->job::whereIn('machine_id',$request->id)->count();
        $msg = __('admin/messages.multi_delete_data',['title'=>'Machines','type'=>'job(s)']);
        return response()->json(['msg' => $msg, 'counts' => @$counts]);
    }

    /**
     * -------------------------------------------------
     * | Get machine datatable data                    |
     * |                                               |
     * | @param Request $request                       |
     * | @return View                                  |
     * |------------------------------------------------
     */
    public function list(Request $request)
    {
        $name = $request->name;
        $machine = $this->helper->list();
        $machineList =  $machine->where('title','ilike', '%' .$name . '%')
                        ->orderBy('title','asc')
                        ->pluck('title');
        return response()->json(['names' => $machineList]);
    }
    /**
     * -------------------------------------------------
     * | Get machine Barcode                           |
     * |                                               |
     * | @param Request $request                       |
     * | @return File                                  |
     * |------------------------------------------------
     */
    public function print($uuid){
        $this->helper->print($uuid,true);
    }

    function generateRandomString($length = 8) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
