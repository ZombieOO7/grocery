<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\EmailTemplateHelper;
use App\Http\Requests\Admin\EmailTemplateRequest;
use Exception;
use Illuminate\Http\Request;
use Lang;
use Illuminate\Support\Facades\Redirect;
use Yajra\Datatables\Datatables;

class EmailTemplateController extends BaseController
{
    private $helper;
    public $viewConstant = 'admin.email-template.';
    public function __construct(EmailTemplateHelper $helper)
    {
        $this->helper = $helper;
        $this->helper->mode = 'admin';
    }
    /**
     * -------------------------------------------------
     * | Display Email Template list                   |
     * |                                               |
     * | @return View                                  |
     * |------------------------------------------------
     */
    public function index()
    {
        try {
            $statusList = $this->statusList();
            return view($this->viewConstant . 'index', ['statusList' => @$statusList]);
        } catch (Exception $e) {
            abort(404);
        }
    }

    /**
     * -------------------------------------------------
     * | Get Email Template datatable date             |
     * |                                               |
     * | @param Request $request                       |
     * | @return View                                  |
     * |------------------------------------------------
     */
    public function getdata(Request $request)
    {
        try {
            $emailTemplates = $this->helper->templateList();
            $emailTemplateList = $emailTemplates->where(function ($query) use ($request) {
                if ($request->status) {
                    $query->activeSearch($request->status);
                }
            })->get();
            // dd($emailTemplateList);
            return Datatables::of($emailTemplateList)
                ->addColumn('action', function ($emailTemplate) {
                    return $this->getPartials($this->viewConstant .'_add_action',['emailTemplate'=>@$emailTemplate]);

                })
                ->editColumn('status', function ($emailTemplate) {
                    return $this->getPartials($this->viewConstant .'_add_status',['emailTemplate'=>@$emailTemplate]);
                })
                ->editColumn('created_at', function ($emailTemplate) {
                    return $emailTemplate->proper_created_at;
                })
                ->addColumn('checkbox', function ($emailTemplate) {
                    return $this->getPartials($this->viewConstant .'_add_checkbox',['emailTemplate'=>@$emailTemplate]);
                })
                ->editColumn('title', function ($emailTemplate) {
                    return $emailTemplate->title_text;
                })
                ->editColumn('subject', function ($emailTemplate) {
                    return $emailTemplate->subject_text;
                })
                ->rawColumns(['title', 'subject', 'created_at', 'checkbox', 'action', 'status'])
                ->make(true);
        } catch (Exception $e) {
            abort('404');
        }
    }

    /**
     * -------------------------------------------------
     * | Create Email Template page                    |
     * |                                               |
     * | @param $id                                    |
     * | @return View                                  |
     * |------------------------------------------------
     */
    public function create($uuid = null)
    {
        try {
            if (isset($uuid)) {
                $emailTemplate = $this->helper->detail($uuid);
            }
            $title = isset($uuid) ? trans('formname.emailTemplate.update') : trans('formname.emailTemplate.create');
            return view($this->viewConstant . 'create', ['emailTemplate' => @$emailTemplate, 'title' => @$title]);
        } catch (Exception $e) {
            abort(404);
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    /**
     * -------------------------------------------------
     * | Store Email Template details                  |
     * |                                               |
     * | @param SubjectFormRequest $request            |
     * | @return View                                  |
     * |------------------------------------------------
     */
    public function store(EmailTemplateRequest $request, $uuid = null)
    {
        $this->helper->dbStart();
        try {
            $this->helper->store($request, $uuid);
            $this->helper->dbEnd();
            if ($request->has('id') && $request->id != '') {
                $msg = __('admin/messages.action_msg', ['action' => __('admin/messages.updated'), 'type' => 'Email template']);
            } else {
                $msg = __('admin/messages.action_msg', ['action' => __('admin/messages.created'), 'type' => 'Email template']);
            }
            return Redirect::route('emailTemplate.index')->with('message', $msg);
        } catch (Exception $e) {
            $this->helper->dbRollBack();
            return Redirect::back()->with('error', $e->getMessage());
            abort('404');
        }
    }
    /**
     * -------------------------------------------------
     * | Delete Email Template details                 |
     * |                                               |
     * | @param Request $request                       |
     * | @return Response                              |
     * |------------------------------------------------
     */
    public function destroy(Request $request)
    {
        $this->helper->dbStart();
        try {
            if (isset($request->id)) {
                $this->helper->delete($request->id);
                $this->helper->dbEnd();
                $msg = __('admin/messages.action_msg', ['action' => __('admin/messages.deleted'), 'type' => 'Email Template']);
                return response()->json(['msg' => $msg, 'icon' => 'success']);
            } else {
                return response()->json(['msg' => Lang::get('formname.not_found'), 'icon' => 'info']);
            }
        } catch (Exception $e) {
            $this->helper->dbRollBack();
            return response()->json(['msg' => $e->getMessage(), 'icon' => 'info']);
        }
    }
    /**
     * -------------------------------------------------
     * | Delete multiple Email Template                |
     * |                                               |
     * | @param Request $request                       |
     * | @return Response                              |
     * |------------------------------------------------
     */
    public function multidelete(Request $request)
    {
        $this->helper->dbStart();
        try {
            $this->helper->multiDelete($request);
            $this->helper->dbEnd();
            if ($request->action == config('constant.inactive') || $request->action == config('constant.active')) {
                $action = ($request->action == config('constant.active'))?__('admin/messages.active'):__('admin/messages.inactive');
                $msg = __('admin/messages.action_msg', ['action' => $action, 'type' => 'Email Template']);
                return response()->json(['msg' => $msg, 'icon' => 'success']);
            } else {
                $msg = __('admin/messages.action_msg', ['action' => __('admin/messages.deleted'), 'type' => 'Email Template']);
                return response()->json(['msg' => @$msg, 'icon' => 'success']);
            }
        } catch (Exception $e) {
            $this->helper->dbRollBack();
            return response()->json(['msg' => $e->getMessage(), 'icon' => 'info']);
        }
    }
    /**
     * -------------------------------------------------
     * | Update Email Template status details          |
     * |                                               |
     * | @param Request $request                       |
     * | @return View                                  |
     * |------------------------------------------------
     */
    public function updateStatus(Request $request)
    {
        $this->helper->dbStart();
        try{
            if (isset($request->id)) {
                $msg = $this->helper->statusUpdate($request->id);
                $this->helper->dbEnd();
                return response()->json(['msg' => $msg, 'icon' => 'success']);
            } else {
                return response()->json(['msg' => __('admin/messages.not_found'), 'icon' => 'info']);
            }
        } catch (Exception $e) {
            $this->helper->dbRollBack();
            return response()->json(['msg' => $e->getMessage(), 'icon' => 'info']);
        }
    }

}
