<?php
namespace App\Helpers;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\UserRoleMaster;

class RoleHelper extends BaseHelper
{

    protected $uerRole;
    public function __construct(UserRoleMaster $uerRole)
    {
        $this->userRole = $uerRole;
        parent::__construct();
    }
    /**
     * ------------------------------------------------------
     * | Get user role list                                 |
     * |                                                    |
     * |-----------------------------------------------------
     */
    public function list()
    {
        return $this->userRole::orderBy('id', 'desc');
    }

    /**
     * ------------------------------------------------------
     * | user role detail by id                             |
     * |                                                    |
     * | @param $id                                         |
     * |-----------------------------------------------------
     */
    public function detailById($id)
    {
        return $this->userRole::whereId($id)->first();
    }

    /**
     * ------------------------------------------------------
     * | user role store                                    |
     * |                                                    |
     * | @param Request $request,$uuid                      |
     * |-----------------------------------------------------
     */
    public function store(Request $request, $uuid = null)
    {
        if ($request->has('id') && $request->id != '') {
            $uerRole = $this->userRole::findOrFail($request->id);
        } else {
            $uerRole = new UserRoleMaster();
        }
        $uerRole->fill($request->all())->save();
        return $uerRole;
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
        $uerRole = $this->detailById($uuid);
        $status = $uerRole->status == config('constant.status_active_value') ? config('constant.status_inactive_value') : config('constant.status_active_value');
        $action = ($status == 1)?__('admin/messages.active'):__('admin/messages.inactive');
        $this->userRole::where('id', $uerRole->id)->update(['status' => $status]);
        $msg = __('admin/messages.action_msg', ['action' => $action, 'type' => 'Role']);
        return $msg;
    }

    /**
     * ------------------------------------------------------
     * | user role detail by uuid                           |
     * |                                                    |
     * | @param $uuid                                       |
     * |-----------------------------------------------------
     */
    public function detail($uuid)
    {
        return $this->userRole::where('uuid', $uuid)->first();
    }

    /**
     * ------------------------------------------------------
     * | Delete user role                                   |
     * |                                                    |
     * | @param $uuid                                       |
     * |-----------------------------------------------------
     */
    public function delete($uuid)
    {
        $uerRole = $this->detailById($uuid);
        $uerRole->delete();
    }

    /**
     * ---------------------------------------------------------------
     * | Delete multiple user role                                   |
     * |                                                             |
     * | @param Request $request                                     |
     * | @return Void                                                |
     * ---------------------------------------------------------------
     */
    public function multiDelete(Request $request)
    {
        $uerRole = $this->userRole::whereIn('id', $request->ids);
        if ($request->action == config('constant.delete')) {
            $uerRole->delete();
        } else {
            $status = $request->action == config('constant.inactive') ? config('constant.status_inactive_value') : config('constant.status_active_value');
            $uerRole->update(['status' => $status]);
        }
    }
}
