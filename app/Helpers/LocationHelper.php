<?php

namespace App\Helpers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationHelper extends BaseHelper
{
    protected $location;
    public function __construct(Location $location)
    {
        $this->location = $location;
        parent::__construct();
    }
    /**
     * ------------------------------------------------------
     * | Get location list                                  |
     * |                                                    |
     * |-----------------------------------------------------
     */
    public function list()
    {
        return $this->location::orderBy('id', 'desc');
    }

    /**
     * ------------------------------------------------------
     * | location detail by id                              |
     * |                                                    |
     * | @param $id                                         |
     * |-----------------------------------------------------
     */
    public function detailById($id)
    {
        return $this->location::whereId($id)->first();
    }

    /**
     * ------------------------------------------------------
     * | location store                                     |
     * |                                                    |
     * | @param Request $request,$uuid                      |
     * |-----------------------------------------------------
     */
    public function store(Request $request, $uuid = null)
    {
        if ($request->has('id') && $request->id != '') {
            $location = $this->location::findOrFail($request->id);
        } else {
            $location = new Location();
        }
        $location->fill($request->all())->save();
        return $location;
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
        $location = $this->detail($uuid);
        $status = $location->status == config('constant.status_active_value') ? config('constant.status_inactive_value') : config('constant.status_active_value');
        $action = ($status == 1)?__('admin/messages.active'):__('admin/messages.inactive');
        $this->location::where('id', $location->id)->update(['status' => $status]);
        $msg = __('admin/messages.action_msg', ['action' => $action, 'type' => 'Location']);
        return $msg;
    }

    /**
     * ------------------------------------------------------
     * | location detail by uuid                             |
     * |                                                    |
     * | @param $uuid                                       |
     * |-----------------------------------------------------
     */
    public function detail($uuid)
    {
        return $this->location::where('uuid', $uuid)->first();
    }

    /**
     * ------------------------------------------------------
     * | Delete location                                    |
     * |                                                    |
     * | @param $uuid                                       |
     * |-----------------------------------------------------
     */
    public function delete($uuid)
    {
        $location = $this->detail($uuid);
        $location->delete();
    }

    /**
     * ---------------------------------------------------------------
     * | Delete multiple location                                    |
     * |                                                             |
     * | @param Request $request                                     |
     * | @return Void                                                |
     * ---------------------------------------------------------------
     */
    public function multiDelete(Request $request)
    {
        $location = $this->location::whereIn('id', $request->ids);
        if ($request->action == config('constant.delete')) {
            $location->delete();
        } else {
            $status = $request->action == config('constant.inactive') ? config('constant.status_inactive_value') : config('constant.status_active_value');
            $location->update(['status' => $status]);
        }
    }
}
