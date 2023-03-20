<?php

namespace App\Helpers;

use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryHelper extends BaseHelper
{

    protected $subCategory;
    public function __construct(SubCategory $subCategory)
    {
        $this->subCategory = $subCategory;
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
        return $this->subCategory::orderBy('id', 'desc');
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
        return $this->subCategory::whereId($id)->first();
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
            $subCategory = $this->subCategory::findOrFail($request->id);
        } else {
            $subCategory = new SubCategory();
        }
        $subCategory->fill($request->all())->save();
        if($request->hasFile('image')){
            $attachment = $subCategory->attachment;
            $subCategory->updateAttachment($request->file('image'),$attachment);
        }
        return $subCategory;
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
        $subCategory = $this->detail($uuid);
        $status = $subCategory->status == config('constant.status_active_value') ? config('constant.status_inactive_value') : config('constant.status_active_value');
        $action = ($status == 1)?__('admin/messages.active'):__('admin/messages.inactive');
        $this->subCategory::where('id', $subCategory->id)->update(['status' => $status]);
        $msg = __('admin/messages.action_msg', ['action' => $action, 'type' => 'Sub Category']);
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
        return $this->subCategory::where('uuid', $uuid)->first();
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
        $subCategory = $this->detail($uuid);
        $subCategory->delete();
    }

    /**
     * ---------------------------------------------------------------
     * | Delete multiple Category                                     |
     * |                                                             |
     * | @param Request $request                                     |
     * | @return Void                                                |
     * ---------------------------------------------------------------
     */
    public function multiDelete(Request $request)
    {
        $subCategory = $this->subCategory::whereIn('id', $request->ids);
        if ($request->action == config('constant.delete')) {
            $subCategory->delete();
        }else {
            $status = $request->action == config('constant.inactive') ? config('constant.status_inactive_value') : config('constant.status_active_value');
            $subCategory->update(['status' => $status]);
        }
    }
}
