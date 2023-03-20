<?php

namespace App\Helpers;

use App\Models\ChatGroup;
use Illuminate\Http\Request;

class ChatGroupHelper extends BaseHelper
{
    protected $chatGroup;
    public function __construct(ChatGroup $chatGroup)
    {
        $this->chatGroup = $chatGroup;
        parent::__construct();
    }
    /**
     * ------------------------------------------------------
     * | Get chat group list                                  |
     * |                                                    |
     * |-----------------------------------------------------
     */
    public function list()
    {
        return $this->chatGroup::orderBy('id', 'desc');
    }

    /**
     * ------------------------------------------------------
     * | chat group detail by id                              |
     * |                                                    |
     * | @param $id                                         |
     * |-----------------------------------------------------
     */
    public function detailById($id)
    {
        return $this->chatGroup::whereId($id)->first();
    }

    /**
     * ------------------------------------------------------
     * | chat group store                                     |
     * |                                                    |
     * | @param Request $request,$uuid                      |
     * |-----------------------------------------------------
     */
    public function store(Request $request, $uuid = null)
    {
        if ($request->has('id') && $request->id != '') {
            $chatGroup = $this->chatGroup::findOrFail($request->id);
        } else {
            $chatGroup = new ChatGroup();
        }
        $chatGroup->fill($request->all())->save();
        $chatGroup->groupUsers()->sync($request->user_ids);
        return $chatGroup;
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
        $chatGroup = $this->detail($uuid);
        $status = $chatGroup->status == config('constant.status_active_value') ? config('constant.status_inactive_value') : config('constant.status_active_value');
        $action = ($status == 1)?__('admin/messages.active'):__('admin/messages.inactive');
        $this->chatGroup::where('id', $chatGroup->id)->update(['status' => $status]);
        $msg = __('admin/messages.action_msg', ['action' => $action, 'type' => 'Group']);
        return $msg;
    }

    /**
     * ------------------------------------------------------
     * | chat group detail by uuid                             |
     * |                                                    |
     * | @param $uuid                                       |
     * |-----------------------------------------------------
     */
    public function detail($uuid)
    {
        return $this->chatGroup::where('uuid', $uuid)->first();
    }

    /**
     * ------------------------------------------------------
     * | Delete chat group                                    |
     * |                                                    |
     * | @param $uuid                                       |
     * |-----------------------------------------------------
     */
    public function delete($uuid)
    {
        $chatGroup = $this->detail($uuid);
        $chatGroup->delete();
    }

    /**
     * ---------------------------------------------------------------
     * | Delete multiple chat group                                    |
     * |                                                             |
     * | @param Request $request                                     |
     * | @return Void                                                |
     * ---------------------------------------------------------------
     */
    public function multiDelete(Request $request)
    {
        $chatGroup = $this->chatGroup::whereIn('id', $request->ids);
        if ($request->action == config('constant.delete')) {
            $chatGroup->delete();
        } else {
            $status = $request->action == config('constant.inactive') ? config('constant.status_inactive_value') : config('constant.status_active_value');
            $chatGroup->update(['status' => $status]);
        }
    }
}
