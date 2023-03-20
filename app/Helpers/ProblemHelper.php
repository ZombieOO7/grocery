<?php

namespace App\Helpers;

use App\Models\Problem;
use Illuminate\Http\Request;

class ProblemHelper extends BaseHelper
{
    protected $problem;
    public function __construct(Problem $problem)
    {
        $this->problem = $problem;
        parent::__construct();
    }
    /**
     * ------------------------------------------------------
     * | Get problem list                                   |
     * |                                                    |
     * |-----------------------------------------------------
     */
    public function list()
    {
        return $this->problem::orderBy('id', 'desc');
    }

    /**
     * ------------------------------------------------------
     * | problem detail by id                               |
     * |                                                    |
     * | @param $id                                         |
     * |-----------------------------------------------------
     */
    public function detailById($id)
    {
        return $this->problem::whereId($id)->first();
    }

    /**
     * ------------------------------------------------------
     * | problem store                                      |
     * |                                                    |
     * | @param Request $request,$uuid                      |
     * |-----------------------------------------------------
     */
    public function store(Request $request, $uuid = null)
    {
        if ($request->has('id') && $request->id != '') {
            $request['status'] = 1;
            $problem = $this->problem::findOrFail($request->id);
        } else {
            $problem = new Problem();
        }
        $problem->fill($request->all())->save();
        return $problem;
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
        $problem = $this->detail($uuid);
        $status = $problem->status == config('constant.status_active_value') ? config('constant.status_inactive_value') : config('constant.status_active_value');
        $action = ($status == 1)?__('admin/messages.active'):__('admin/messages.inactive');
        $this->problem::where('id', $problem->id)->update(['status' => $status]);
        $msg = __('admin/messages.action_msg', ['action' => $action, 'type' => 'Problem']);
        return $msg;
    }

    /**
     * ------------------------------------------------------
     * | problem detail by uuid                             |
     * |                                                    |
     * | @param $uuid                                       |
     * |-----------------------------------------------------
     */
    public function detail($uuid)
    {
        return $this->problem::where('uuid', $uuid)->first();
    }

    /**
     * ------------------------------------------------------
     * | Delete problem                                     |
     * |                                                    |
     * | @param $uuid                                       |
     * |-----------------------------------------------------
     */
    public function delete($uuid)
    {
        $problem = $this->detail($uuid);
        $problem->delete();
    }

    /**
     * ---------------------------------------------------------------
     * | Delete multiple problem                                     |
     * |                                                             |
     * | @param Request $request                                     |
     * | @return Void                                                |
     * ---------------------------------------------------------------
     */
    public function multiDelete(Request $request)
    {
        $problem = $this->problem::whereIn('id', $request->ids);
        if ($request->action == config('constant.delete')) {
            $problem->delete();
        } else {
            $status = $request->action == config('constant.inactive') ? config('constant.status_inactive_value') : config('constant.status_active_value');
            $problem->update(['status' => $status]);
        }
    }
}
