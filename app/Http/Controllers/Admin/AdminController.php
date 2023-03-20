<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\AdminFormRequest;
use App\Models\Admin;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Redirect;
use Yajra\Datatables\Datatables;

class AdminController extends BaseController
{
    protected $admin, $role;

    public function __construct(Admin $admin)
    {
        $this->admin = $admin;
    }
    /**
     * -----------------------------------------------------
     * | Admin  list                                       |
     * |                                                   |
     * | @return View                                      |
     * -----------------------------------------------------
     */
    public function index()
    {
        $statusList = $this->statusList();
        return view('admin.admin.index', ['statusList' => $statusList]);
    }

    /**
     * -----------------------------------------------------
     * | Admin  datatables data                            |
     * |                                                   |
     * | @param Request $request                           |
     * | @return Response                                  |
     * -----------------------------------------------------
     */
    public function getdata(Request $request)
    {
        $adminDataTable = $this->admin->orderBy('created_at', 'desc')
            ->where(function ($query) use ($request) {
                if ($request->status) {
                    $query->activeSearch($request->status);
                }
            });
        return Datatables::of($adminDataTable)
            ->addColumn('action', function ($admin) {
                return View::make('admin.admin._add_action', ['admin' => $admin])->render();
            })
            ->editColumn('status', function ($admin) {
                return $admin->active_tag;
            })
            ->addColumn('checkbox', function ($admin) {
                return View::make('admin.admin._add_checkbox', ['admin' => $admin])->render();
            })
            ->rawColumns(['checkbox', 'action', 'status'])
            ->make(true);
    }

    /**
     * -----------------------------------------------------
     * | Create/Update Admin user form                     |
     * |                                                   |
     * | @param $id                                        |
     * | @return View                                      |
     * -----------------------------------------------------
     */
    public function create($id = null)
    {
        if (isset($id)) {
            $admin = Admin::find($id);
        }
        $statusList = $this->properStatusList();
        $role = Role::where('guard_name', 'admin')->pluck('name', 'name');
        return view('admin.admin.create_admin', ['admin' => @$admin, 'role' => @$role,'statusList' => @$statusList]);
    }

    /**
     * -----------------------------------------------------
     * | Store/Edit Admin form                             |
     * |                                                   |
     * | @param AdminFormRequest $request                  |
     * | @return Redirect                                  |
     * -----------------------------------------------------
     */
    public function store(AdminFormRequest $request)
    {
        $request['email'] = strtolower($request['email']);
        if ($request->has('id') && !empty($request->id)) {
            $msg = __('admin/messages.admin_update');
        } else {
            $msg = __('admin/messages.admin_create');
        }
        $admin = $this->admin->updateOrCreate(
            ['id' => @$request->id],
            $request->all()
        );
        $admin->syncRoles($request->role);
        return Redirect::route('admin_index')->with('message', $msg);
    }

    /**
     * -----------------------------------------------------
     * | Delete Admin record                               |
     * |                                                   |
     * | @param Request $request                           |
     * | @return Response                                  |
     * -----------------------------------------------------
     */
    public function destroyAdmin(Request $request)
    {
        if (isset($request->id)) {
            $admin = Admin::find($request->id)->delete();
            return response()->json(['msg' => __('admin/messages.admin_delete'), 'icon' => 'success']);
        } else {
            return response()->json(['msg' => __('admin/messages.not_found'), 'icon' => 'info']);
        }
    }

    /**
     * -----------------------------------------------------
     * | Multiple Delete Admin record                      |
     * |                                                   |
     * | @param Request $request                           |
     * | @return Response                                  |
     * -----------------------------------------------------
     */
    public function multideleteAdmin(Request $request)
    {
        $admin_id_array = $request->input('ids');
        $admins = $this->admin->whereIn('id', $admin_id_array);
        if ($request->action == 'inactive') {
            $admins->update(['status' => '0']);
            return response()->json(['msg' => __('admin/messages.admin_inactive'), 'icon' => 'success']);
        } else if ($request->action == 'active') {
            $admins->update(['status' => '1']);
            return response()->json(['msg' => __('admin/messages.admin_active'), 'icon' => 'success']);
        } else {
            $admins->delete();
            return response()->json(['msg' => __('admin/messages.admin_delete'), 'icon' => 'success']);
        }
    }

    /**
     * -----------------------------------------------------
     * | Update status                                     |
     * |                                                   |
     * | @param Request $request                           |
     * | @return Response                                  |
     * -----------------------------------------------------
     */
    public function updateStatus(Request $request)
    {
        if (isset($request->id)) {
            $admin = $this->admin->find($request->id);
            if ($admin->status == '0') {
                $admin->update(['status' => '1']);
                return response()->json(['msg' => __('admin/messages.admin_active'), 'icon' => 'success']);
            } else {
                $admin->update(['status' => '0']);
                return response()->json(['msg' => __('admin/messages.admin_inactive'), 'icon' => 'success']);
            }
        } else {
            return response()->json(['msg' => __('admin/messages.not_Found'), 'icon' => 'info']);
        }
    }

    /**
     * -----------------------------------------------------
     * | Display Profile                                      |
     * |                                                      |
     * | @return View                                         |
     * -----------------------------------------------------
     */
    public function profile()
    {
        $id = Auth::id();
        $admin = Admin::find($id);
        return view('admin.admin.profile', ['admin' => @$admin]);
    }
        /**
     * -----------------------------------------------------
     * | Display Profile                                      |
     * |                                                      |
     * | @return View                                         |
     * -----------------------------------------------------
     */
    public function updateProfile(AdminFormRequest $request)
    {
        $request['email'] = strtolower($request['email']);
        $admin = Admin::find(Auth::id());
        $admin->fill($request->all())->save();
        $msg = __('admin/messages.action_msg', ['action' => __('admin/messages.updated'), 'type' => 'Profile']);
        return redirect()->route('profile')->with('message', $msg);

    }
}
