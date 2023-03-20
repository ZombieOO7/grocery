<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\PermissionFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Redirect;
use Spatie\Permission\Models\Permission;
use Yajra\Datatables\Datatables;

class PermissionController extends BaseController
{
    protected $permission;

    /**
     * Create a new controller instance.
     */
    public function __construct(Permission $permission)
    {
        $this->permission = $permission;
    }
    /**
     * -----------------------------------------------------
     * | Permission list                                   |
     * |                                                   |
     * | @return View                                      |
     * -----------------------------------------------------
     */
    public function index()
    {
        return view('admin.permission.index');
    }

    /**
     * -----------------------------------------------------
     * | Permission datatables data                        |
     * |                                                   |
     * | @param Request $request                           |
     * | @return Response                                  |
     * -----------------------------------------------------
     */
    public function getdata(Request $request)
    {
        $permissions = $this->permission->orderBy('created_at', 'desc')->get();
        return Datatables::of($permissions)
            ->addColumn('name', function ($permission) {
                return ucwords(str_replace('_', ' ', $permission->name));
            })
            ->addColumn('action', function ($permission) {
                return View::make('admin.permission._add_action', ['permission' => $permission])->render();
            })
            ->addColumn('checkbox', function ($permission) {
                return View::make('admin.permission._add_checkbox', ['permission' => $permission])->render();
            })
            ->rawColumns(['checkbox', 'action', 'status'])
            ->make(true);
    }

    /**
     * -----------------------------------------------------
     * | Create/Update Permission form                     |
     * |                                                   |
     * | @param $id                                        |
     * | @return View                                      |
     * -----------------------------------------------------
     */
    public function create($id = null)
    {
        $data = [];
        if (isset($id)) {
            $permission = $this->permission->find($id);
        }
        return view('admin.permission.create_permission',['permission'=>@$permission]);
    }

    /**
     * -----------------------------------------------------
     * | Store/Edit Permission form                        |
     * |                                                   |
     * | @param PermissionFormRequest $request             |
     * | @return Redirect                                  |
     * -----------------------------------------------------
     */
    public function store(PermissionFormRequest $request)
    {
        if ($request->has('id') && !empty($request->id)) {
            $permission = $this->permission->find($request->id);
            $permission->update(['name' => $request->name]);
            $msg = __('admin/messages.action_msg', ['action' => __('admin/messages.updated'), 'type' => 'Permission']);
        } else {
            $permission = $this->permission->create(['guard_name' => 'admin', 'name' => $request->name]);
            $msg = __('admin/messages.action_msg', ['action' => __('admin/messages.created'), 'type' => 'Permission']);
        }
        return Redirect::route('permission_index')->with('message', $msg);
    }

    /**
     * -----------------------------------------------------
     * | Delete Permission record                          |
     * |                                                   |
     * | @param Request $request                           |
     * | @return Response                                  |
     * -----------------------------------------------------
     */
    public function destroyPermission(Request $request)
    {
        if (isset($request->id)) {
            $permission = $this->permission->find($request->id);
            $permission->delete();
            $msg = __('admin/messages.action_msg', ['action' => __('admin/messages.deleted'), 'type' => 'Permission']);
            return response()->json(['msg' => $msg, 'icon' => 'success']);
        } else {
            return response()->json(['msg' => __('admin/messages.not_found'), 'icon' => 'info']);
        }
    }

    /**
     * -----------------------------------------------------
     * | Multiple Delete Permission record                 |
     * |                                                   |
     * | @param Request $request                           |
     * | @return Response                                  |
     * -----------------------------------------------------
     */
    public function multideletePermission(Request $request)
    {
        $permission_id_array = $request->input('ids');
        $permissions = $this->permission->whereIn('id', $permission_id_array)->delete();
        $msg = __('admin/messages.action_msg', ['action' => __('admin/messages.deleted'), 'type' => 'Permission']);
        return response()->json(['msg' => $msg, 'icon' => 'success']);
    }
}
