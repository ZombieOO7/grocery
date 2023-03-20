<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\RoleFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Redirect;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\Datatables\Datatables;

class RoleController extends BaseController
{
    protected $role;

    /**
     * Create a new controller instance.
     */
    public function __construct(Role $role)
    {
        $this->role = $role;
    }
    /**
     * -----------------------------------------------------
     * | Role list                                         |
     * |                                                   |
     * | @return View                                      |
     * -----------------------------------------------------
     */
    public function index(Request $request)
    {
        return view('admin.role.index');
    }

    /**
     * -----------------------------------------------------
     * | Role datatables data                              |
     * |                                                   |
     * | @param Request $request                           |
     * | @return Response                                  |
     * -----------------------------------------------------
     */
    public function getdata(Request $request)
    {
        $roleDataTable = $this->role->orderBy('created_at', 'desc');
        return Datatables::of($roleDataTable)
            ->addColumn('action', function ($role) {
                return View::make('admin.role._add_action', ['role' => $role])->render();
            })
            ->addColumn('checkbox', function ($role) {
                return View::make('admin.role._add_checkbox', ['role' => $role])->render();
            })
            ->rawColumns(['checkbox', 'action', 'status'])
            ->make(true);
    }

    /**
     * -----------------------------------------------------
     * | Create/Update Role form                           |
     * |                                                   |
     * | @param $id                                        |
     * | @return View                                      |
     * -----------------------------------------------------
     */
    public function create($id = null)
    {
        if (isset($id)) {
            $role = $this->role->find($id);
        }
        $permission = Permission::pluck('name', 'id');
        return view('admin.role.create_role',['permission'=>@$permission,'role'=>@$role]);
    }

    /**
     * -----------------------------------------------------
     * | Store/Edit Role form                              |
     * |                                                   |
     * | @param RoleFormRequest $request                   |
     * | @return Redirect                                  |
     * -----------------------------------------------------
     */
    public function store(RoleFormRequest $request)
    {
        if ($request->has('id') && !empty($request->id)) {
            $role = $this->role->find($request->id);
            $role->update(['name' => $request->name]);
            $msg = __('admin/messages.action_msg', ['action' => __('admin/messages.updated'), 'type' => 'Role']);
        } else {
            $role = $this->role->create(['guard_name' => 'admin', 'name' => $request->name]);
            $msg = __('admin/messages.action_msg', ['action' => __('admin/messages.created'), 'type' => 'Role']);
        }
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $role->syncPermissions($request->permission);
        return Redirect::route('role_index')->with('message', $msg);
    }

    /**
     * -----------------------------------------------------
     * | Delete Role record                                |
     * |                                                   |
     * | @param Request $request                           |
     * | @return Response                                  |
     * -----------------------------------------------------
     */
    public function destroyRole(Request $request)
    {
        if (isset($request->id)) {
            $role = $this->role->find($request->id);
            $role->delete();
            $msg = __('admin/messages.action_msg', ['action' => __('admin/messages.deleted'), 'type' => 'Role']);
            return response()->json(['msg' => $msg, 'icon' => 'success']);
        } else {
            return response()->json(['msg' => __('admin/messages.not_found'), 'icon' => 'info']);
        }
    }

    /**
     * -----------------------------------------------------
     * | Multiple Delete Role record                       |
     * |                                                   |
     * | @param Request $request                           |
     * | @return Response                                  |
     * -----------------------------------------------------
     */
    public function multideleteRole(Request $request)
    {
        $role_id_array = $request->input('ids');
        $roles = $this->role->whereIn('id', $role_id_array)->delete();
        $msg = __('admin/messages.action_msg', ['action' => __('admin/messages.deleted'), 'type' => 'Role']);
        return response()->json(['msg' => $msg, 'icon' => 'success']);
    }
}
