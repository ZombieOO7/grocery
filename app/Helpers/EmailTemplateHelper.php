<?php

namespace App\Helpers;

use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplateHelper extends BaseHelper
{

    protected $emailTemplate;
    public function __construct(EmailTemplate $emailTemplate)
    {
        $this->emailTemplate = $emailTemplate;
        parent::__construct();
    }
    /**
     * ------------------------------------------------------
     * | Get Email Template List                            |
     * |                                                    |
     * |-----------------------------------------------------
     */
    public function templateList()
    {
        return $this->emailTemplate::orderBy('id', 'desc');
    }

    /**
     * ------------------------------------------------------
     * | Email Template detail by id                        |
     * |                                                    |
     * | @param $id                                         |
     * |-----------------------------------------------------
     */
    public function detailById($id)
    {
        return $this->emailTemplate::whereId($id)->first();
    }

    /**
     * ------------------------------------------------------
     * | Email Template store                               |
     * |                                                    |
     * | @param Request $request,$uuid                      |
     * |-----------------------------------------------------
     */
    public function store(Request $request, $uuid = null)
    {
        if ($request->has('id') && $request->id != '') {
            $emailTemplate = $this->emailTemplate::findOrFail($request->id);
        } else {
            $emailTemplate = new EmailTemplate();
        }
        $emailTemplate->fill($request->all())->save();
        return $emailTemplate;
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
        $emailTemplate = $this->detail($uuid);
        $status = $emailTemplate->status == config('constant.status_active_value') ? config('constant.status_inactive_value') : config('constant.status_active_value');
        $action = ($status == 1)?__('admin/messages.active'):__('admin/messages.inactive');
        $this->emailTemplate::where('id', $emailTemplate->id)->update(['status' => $status]);
        $msg = __('admin/messages.action_msg', ['action' => $action, 'type' => 'Email Template']);
        return $msg;
    }

    /**
     * ------------------------------------------------------
     * | Email Template detail by uuid                      |
     * |                                                    |
     * | @param $uuid                                       |
     * |-----------------------------------------------------
     */
    public function detail($uuid)
    {
        return $this->emailTemplate::where('uuid', $uuid)->first();
    }

    /**
     * ------------------------------------------------------
     * | Delete Email Template                              |
     * |                                                    |
     * | @param $uuid                                       |
     * |-----------------------------------------------------
     */
    public function delete($uuid)
    {
        $emailTemplate = $this->detail($uuid);
        $emailTemplate->delete();
    }

    /**
     * ---------------------------------------------------------------
     * | Delete multiple Email Template                              |
     * |                                                             |
     * | @param Request $request                                     |
     * | @return Void                                                |
     * ---------------------------------------------------------------
     */
    public function multiDelete(Request $request)
    {
        $emailTemplate = $this->emailTemplate::whereIn('id', $request->ids);
        if ($request->action == config('constant.delete')) {
            $emailTemplate->delete();
        } else {
            $status = $request->action == config('constant.inactive') ? config('constant.status_inactive_value') : config('constant.status_active_value');
            $emailTemplate->update(['status' => $status]);
        }
    }
}
