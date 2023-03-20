<?php

namespace App\Helpers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryHelper extends BaseHelper
{

    protected $category;
    public function __construct(Category $category)
    {
        $this->category = $category;
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
        return $this->category::orderBy('id', 'desc');
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
        return $this->category::whereId($id)->first();
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
            $category = $this->category::findOrFail($request->id);
        } else {
            $category = new Category();
        }
        $category->fill($request->all())->save();
        if($request->hasFile('image')){
            $attachment = $category->attachment;
            $category->updateAttachment($request->file('image'),$attachment);
        }
        return $category;
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
        $category = $this->detail($uuid);
        $status = $category->status == config('constant.status_active_value') ? config('constant.status_inactive_value') : config('constant.status_active_value');
        $action = ($status == 1)?__('admin/messages.active'):__('admin/messages.inactive');
        $this->category::where('id', $category->id)->update(['status' => $status]);
        $msg = __('admin/messages.action_msg', ['action' => $action, 'type' => 'Category']);
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
        return $this->category::where('uuid', $uuid)->first();
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
        $category = $this->detail($uuid);
        $category->delete();
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
        $category = $this->category::whereIn('id', $request->ids);
        if ($request->action == config('constant.delete')) {
            $category->delete();
        }else {
            $status = $request->action == config('constant.inactive') ? config('constant.status_inactive_value') : config('constant.status_active_value');
            $category->update(['status' => $status]);
        }
    }
}
