<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\PermissionMasterHelper;
use App\Http\Requests\Admin\PermissionMasterFormRequest;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Redirect;

class PermissionMasterController extends BaseController
{
    private $helper;
    public $viewConstant = 'admin.permission-master.';
    public function __construct(PermissionMasterHelper $helper)
    {
        $this->helper = $helper;
        $this->helper->mode = 'admin';
    }
    /**
     * -------------------------------------------------
     * | Display permission list                          |
     * |                                               |
     * | @return View                                  |
     * |------------------------------------------------
     */
    public function index()
    {
        try {
            $statusList = $this->statusList();
            $permissionStatusList = $this->permissionStatus();
            return view($this->viewConstant . 'index', ['statusList' => @$statusList, 'permissionStatusList' => @$permissionStatusList]);
        } catch (Exception $e) {
            abort(404);
        }
    }

    /**
     * -------------------------------------------------
     * | Get permission datatable data                    |
     * |                                               |
     * | @param Request $request                       |
     * | @return View                                  |
     * |------------------------------------------------
     */
    public function getdata(Request $request)
    {
        try {
            $permission = $this->helper->list();
            $permissionList = $permission->where(function ($query) use ($request) {
                if ($request->status) {
                    $query->activeSearch($request->status);
                }
                if ($request->permissionStatus) {
                    $query->statusSearch($request->permissionStatus);
                }
            })->get();
            return DataTables::of($permissionList)
                ->addColumn('action', function ($permission) {
                    return $this->getPartials($this->viewConstant . '_add_action', ['permission' => $permission]);
                })
            // ->editColumn('status', function ($permission) {
            //     return $this->getPartials($this->viewConstant . '_add_status', ['permission' => $permission]);
            // })
                ->editColumn('permission_status', function ($permission) {
                    return $this->getPartials($this->viewConstant . '_add_permission_status', ['permission' => $permission]);
                })
                ->editColumn('user_type', function ($permission) {
                    return @config('constant.user_types')[$permission->user_type];
                })
                ->editColumn('created_at', function ($permission) {
                    return $permission->proper_created_at;
                })
                ->addColumn('checkbox', function ($permission) {
                    return $this->getPartials($this->viewConstant . '_add_checkbox', ['permission' => $permission]);
                })
                ->rawColumns(['created_at', 'checkbox', 'action', 'status', 'permission_status', 'user_type'])
                ->make(true);
        } catch (Exception $e) {
            abort('404');
        }
    }

    /**
     * -------------------------------------------------
     * | Create permission page                        |
     * |                                               |
     * | @param $id                                    |
     * | @return View                                  |
     * |------------------------------------------------
     */
    public function create($uuid = null)
    {
        try {
            if (isset($uuid)) {
                $permission = $this->helper->detail($uuid);
            }
            $statusList = $this->properStatusList();
            $permissionStatusList = $this->permissionStatusList();
            $userTypeList = $this->userTypeList();
            $title = isset($uuid) ? trans('formname.permission_master.update') : trans('formname.permission_master.create');
            return view($this->viewConstant . 'create', ['permission' => @$permission, 'title' => @$title, 'statusList' => @$statusList, 'permissionStatusList' => @$permissionStatusList, 'userTypeList' => @$userTypeList]);
        } catch (Exception $e) {
            abort('404');
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    /**
     * -------------------------------------------------
     * | Store comapny details                         |
     * |                                               |
     * | @param SubjectFormRequest $request            |
     * | @return View                                  |
     * |------------------------------------------------
     */
    public function store(PermissionMasterFormRequest $request, $uuid = null)
    {
        $this->helper->dbStart();
        try {
            $this->helper->store($request, $uuid);
            if ($request->has('id') && !empty($request->id)) {
                $msg = __('admin/messages.action_msg', ['action' => __('admin/messages.updated'), 'type' => 'Permission']);
            } else {
                $msg = __('admin/messages.action_msg', ['action' => __('admin/messages.created'), 'type' => 'Permission']);
            }
            $this->helper->dbEnd();
            return redirect()->route('permission.index')->with('message', $msg);
        } catch (Exception $e) {
            $this->helper->dbRollBack();
            return Redirect::back()->with('error', $e->getMessage());
            abort('404');
        }
    }
    /**
     * -------------------------------------------------
     * | Delete permission details                     |
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
                $msg = __('admin/messages.action_msg', ['action' => __('admin/messages.deleted'), 'type' => 'Permission']);
                return response()->json(['msg' => $msg, 'icon' => 'success']);
            } else {
                return response()->json(['msg' => __('admin/messages.not_found'), 'icon' => 'info']);
            }
        } catch (Exception $e) {
            $this->helper->dbRollBack();
            return response()->json(['msg' => $e->getMessage(), 'icon' => 'info']);
            // return response()->json(['msg' => __('admin/messages.not_found'), 'icon' => 'info']);
        }
    }
    /**
     * -------------------------------------------------
     * | Delete multiple permission                    |
     * |                                               |
     * | @param Request $request                       |
     * | @return Response                              |
     * |------------------------------------------------
     */
    public function multidelete(Request $request)
    {
        $this->helper->dbStart();
        try {
            $this->helper->multiDelete($request);
            $this->helper->dbEnd();
            if ($request->action == config('constant.permission_status')[0] || $request->action == config('constant.permission_status')[1]) {
                $action = ($request->action == config('constant.permission_status')[0]) ? __('admin/messages.off') : __('admin/messages.on');
                $msg = __('admin/messages.action_msg', ['action' => $action, 'type' => 'Permission']);
                return response()->json(['msg' => $msg, 'icon' => 'success']);
            } else {
                $msg = __('admin/messages.action_msg', ['action' => __('admin/messages.deleted'), 'type' => 'Permission']);
                return response()->json(['msg' => @$msg, 'icon' => 'success']);
            }
        } catch (Exception $e) {
            $this->helper->dbRollBack();
            return response()->json(['msg' => $e->getMessage(), 'icon' => 'info']);
            // return response()->json(['msg' => __('admin/messages.not_found'), 'icon' => 'info']);
        }
    }
    /**
     * -------------------------------------------------
     * | Update permission status details              |
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
            // return response()->json(['msg' => __('admin/messages.not_found'), 'icon' => 'info']);
        }
    }

}
