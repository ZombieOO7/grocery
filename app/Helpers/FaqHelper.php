<?php

namespace App\Helpers;

use App\Models\Faq;
use Illuminate\Http\Request;

class FaqHelper extends BaseHelper
{

    protected $faq;
    public function __construct(Faq $faq)
    {
        $this->faq = $faq;
        parent::__construct();
    }
    /**
     * ------------------------------------------------------
     * | Get faq list                                       |
     * |                                                    |
     * |-----------------------------------------------------
     */
    public function list()
    {
        return $this->faq::orderBy('id', 'desc');
    }

    /**
     * ------------------------------------------------------
     * | faq detail by id                                   |
     * |                                                    |
     * | @param $id                                         |
     * |-----------------------------------------------------
     */
    public function detailById($id)
    {
        return $this->faq::whereId($id)->first();
    }

    /**
     * ------------------------------------------------------
     * | faq store                                          |
     * |                                                    |
     * | @param Request $request,$uuid                      |
     * |-----------------------------------------------------
     */
    public function store(Request $request, $uuid = null)
    {
        if ($request->has('id') && $request->id != '') {
            $faq = $this->faq::findOrFail($request->id);
        } else {
            $faq = new Faq();
        }
        $faq->fill($request->all())->save();
        return $faq;
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
        $faq = $this->detail($uuid);
        $status = $faq->status == config('constant.status_active_value') ? config('constant.status_inactive_value') : config('constant.status_active_value');
        $action = ($status == 1)?__('admin/messages.active'):__('admin/messages.inactive');
        $this->faq::where('id', $faq->id)->update(['status' => $status]);
        $msg = __('admin/messages.action_msg', ['action' => $action, 'type' => 'FAQ']);
        return $msg;
    }

    /**
     * ------------------------------------------------------
     * | faq detail by uuid                                 |
     * |                                                    |
     * | @param $uuid                                       |
     * |-----------------------------------------------------
     */
    public function detail($uuid)
    {
        return $this->faq::where('uuid', $uuid)->first();
    }

    /**
     * ------------------------------------------------------
     * | Delete faq                                         |
     * |                                                    |
     * | @param $uuid                                       |
     * |-----------------------------------------------------
     */
    public function delete($uuid)
    {
        $faq = $this->detail($uuid);
        $faq->delete();
    }

    /**
     * ---------------------------------------------------------------
     * | Delete multiple faq                                         |
     * |                                                             |
     * | @param Request $request                                     |
     * | @return Void                                                |
     * ---------------------------------------------------------------
     */
    public function multiDelete(Request $request)
    {
        $faq = $this->faq::whereIn('id', $request->ids);
        if ($request->action == config('constant.delete')) {
            $faq->delete();
        } else {
            $status = $request->action == config('constant.inactive') ? config('constant.status_inactive_value') : config('constant.status_active_value');
            $faq->update(['status' => $status]);
        }
    }
}
