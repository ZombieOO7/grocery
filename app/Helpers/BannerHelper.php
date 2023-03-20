<?php

namespace App\Helpers;

use App\Models\Banner;
use Illuminate\Http\Request;

class BannerHelper extends BaseHelper
{

    protected $banner;
    public function __construct(Banner $banner)
    {
        $this->banner = $banner;
        parent::__construct();
    }
    /**
     * ------------------------------------------------------
     * | Get Category list                                   |
     * |                                                    |
     * |-----------------------------------------------------
     */
    public function list()
    {
        return $this->banner::orderBy('id', 'desc');
    }

    /**
     * ------------------------------------------------------
     * | comapny detail by id                               |
     * |                                                    |
     * | @param $id                                         |
     * |-----------------------------------------------------
     */
    public function detailById($id)
    {
        return $this->banner::whereId($id)->first();
    }

    /**
     * ------------------------------------------------------
     * | Category store                                      |
     * |                                                    |
     * | @param Request $request,$uuid                      |
     * |-----------------------------------------------------
     */
    public function store(Request $request, $uuid = null)
    {
        if ($request->has('id') && $request->id != '') {
            $request['status']= 1;
            $banner = $this->banner::findOrFail($request->id);
        } else {
            $banner = new Banner();
        }
        $banner->fill($request->all())->save();
        if($request->hasFile('image')){
            $attachment = $banner->attachment;
            $banner->updateAttachment($request->file('image'),$attachment);
        }
        return $banner;
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
        $banner = $this->detail($uuid);
        $status = $banner->status == config('constant.status_active_value') ? config('constant.status_inactive_value') : config('constant.status_active_value');
        $action = ($status == 1)?__('admin/messages.active'):__('admin/messages.inactive');
        $this->banner::where('id', $banner->id)->update(['status' => $status]);
        $msg = __('admin/messages.action_msg', ['action' => $action, 'type' => 'Banner']);
        return $msg;
    }

    /**
     * ------------------------------------------------------
     * | Category detail by uuid                             |
     * |                                                    |
     * | @param $uuid                                       |
     * |-----------------------------------------------------
     */
    public function detail($uuid)
    {
        return $this->banner::where('uuid', $uuid)->first();
    }

    /**
     * ------------------------------------------------------
     * | Delete Category                                     |
     * |                                                    |
     * | @param $uuid                                       |
     * |-----------------------------------------------------
     */
    public function delete($uuid)
    {
        $banner = $this->detail($uuid);
        $banner->delete();
    }

    /**
     * ---------------------------------------------------------------
     * | Delete multiple Category                                    |
     * |                                                             |
     * | @param Request $request                                     |
     * | @return Void                                                |
     * ---------------------------------------------------------------
     */
    public function multiDelete(Request $request)
    {
        $banner = $this->banner::whereIn('id', $request->ids);
        if ($request->action == config('constant.delete')) {
            $banner->delete();
        }else {
            $status = $request->action == config('constant.inactive') ? config('constant.status_inactive_value') : config('constant.status_active_value');
            $banner->update(['status' => $status]);
        }
    }
}
