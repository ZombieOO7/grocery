<?php

namespace App\Helpers;

use App\Models\PermissionMaster;
use Illuminate\Http\Request;

class PermissionMasterHelper extends BaseHelper
{
    protected $permission;
    public function __construct(PermissionMaster $permission)
    {
        $this->permission = $permission;
        parent::__construct();
    }
    /**
     * ------------------------------------------------------
     * | Get permission list                                |
     * |                                                    |
     * |-----------------------------------------------------
     */
    public function list()
    {
        return $this->permission::orderBy('id', 'desc');
    }

    /**
     * ------------------------------------------------------
     * | permission detail by id                            |
     * |                                                    |
     * | @param $id                                         |
     * |-----------------------------------------------------
     */
    public function detailById($id)
    {
        return $this->permission::whereId($id)->first();
    }

    /**
     * ------------------------------------------------------
     * | permission store                                   |
     * |                                                    |
     * | @param Request $request,$uuid                      |
     * |-----------------------------------------------------
     */
    public function store(Request $request, $uuid = null)
    {
        if ($request->has('id') && $request->id != '') {
            $permission = $this->permission::findOrFail($request->id);
        } else {
            $permission = new PermissionMaster();
        }
        $permission->fill($request->all())->save();
        return $permission;
    }

    /**
     * ------------------------------------------------------
     * | Update status                                      |
     * |                                                    |
     * | @param $uuid                                       |
     * |-----------------------------------------------------
     */
    public function statusUpdate($uuid)
    {
        $permission = $this->detail($uuid);
        $status = $permission->permission_status == config('constant.status_active_value') ? config('constant.status_inactive_value') : config('constant.status_active_value');
        $action = ($status == 1)?__('admin/messages.on'):__('admin/messages.off');
        $this->permission::where('id', $permission->id)->update(['permission_status' => $status]);
        $msg = __('admin/messages.action_msg', ['action' => $action, 'type' => 'Permission']);
        return $msg;
    }

    /**
     * ------------------------------------------------------
     * | permission detail by uuid                          |
     * |                                                    |
     * | @param $uuid                                       |
     * |-----------------------------------------------------
     */
    public function detail($uuid)
    {
        return $this->permission::where('uuid', $uuid)->first();
    }

    /**
     * ------------------------------------------------------
     * | Delete permission                                  |
     * |                                                    |
     * | @param $uuid                                       |
     * |-----------------------------------------------------
     */
    public function delete($uuid)
    {
        $permission = $this->detail($uuid);
        $permission->delete();
    }

    /**
     * ---------------------------------------------------------------
     * | Delete multiple permission                                  |
     * |                                                             |
     * | @param Request $request                                     |
     * | @return Void                                                |
     * ---------------------------------------------------------------
     */
    public function multiDelete(Request $request)
    {
        $permission = $this->permission::whereIn('id', $request->ids);
        if ($request->action == config('constant.delete')) {
            $permission->delete();
        } else {
            $status = $request->action == config('constant.permission_status')[0] ? config('constant.status_inactive_value') : config('constant.status_active_value');
            $permission->update(['permission_status' => $status]);
        }
    }
}
