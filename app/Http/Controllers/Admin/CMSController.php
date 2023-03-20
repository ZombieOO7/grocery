<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\CMSFormRequest;
use App\Models\CMS;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Redirect;
use Yajra\Datatables\Datatables;

class CMSController extends BaseController
{

    protected $cms;

    /**
     * Create a new controller instance.
     */
    public function __construct(CMS $cms)
    {
        $this->cms = $cms;
    }
    /**
     * -----------------------------------------------------
     * | CMS list                                          |
     * |                                                   |
     * | @return View                                      |
     * -----------------------------------------------------
     */
    public function index()
    {
        try {
            $statusList = $this->statusList();
            return view('admin.cms.index', ['statusList' => $statusList]);
        } catch (Exception $e) {
            abort('404');
        }
    }

    /**
     * -----------------------------------------------------
     * | CMS datatables data                               |
     * |                                                   |
     * | @param Request $request                           |
     * | @return Response                                  |
     * -----------------------------------------------------
     */
    public function getdata(Request $request)
    {
        $cmsDataTable = $this->cms::orderBy('created_at', 'desc')
            ->where(function ($query) use ($request) {
                if ($request->status) {
                    $query->activeSearch($request->status);
                }
            });
        return Datatables::of($cmsDataTable)
            ->addColumn('action', function ($cms) {
                return View::make('admin.cms._add_action', ['cms' => $cms])->render();
            })
            ->editColumn('status', function ($cms) {
                return $cms->active_tag;
            })
            ->addColumn('checkbox', function ($cms) {
                return View::make('admin.cms._add_checkbox', ['cms' => $cms])->render();
            })
            ->editColumn('created_at', function ($cms) {
                return $cms->proper_created_at;
            })
            ->rawColumns(['checkbox', 'action', 'status', 'created_at'])
            ->make(true);
    }

    /**
     * -----------------------------------------------------
     * | Create/Update CMS form                            |
     * |                                                   |
     * | @param $id                                        |
     * | @return View                                      |
     * -----------------------------------------------------
     */
    public function create($uuid = null)
    {
        try {
            if (isset($uuid)) {
                $cms = $this->cms->whereUuid($uuid)->firstOrFail();
            }
            $statusList = $this->properStatusList();
            return view('admin.cms.create_cms', ['cms' => @$cms, 'statusList' => @$statusList]);
        } catch (Exception $e) {
            abort('404');
            // return Redirect::back()->with('error', $e->getMessage());
        }
    }

    /**
     * -----------------------------------------------------
     * | Store/Edit CMS form                               |
     * |                                                   |
     * | @param CMSFormRequest $request                    |
     * | @return Redirect                                  |
     * -----------------------------------------------------
     */
    public function store(CMSFormRequest $request)
    {
        // $this->helper->dbStart();
        try {
            $cms = ($request->has('id') && !empty($request->id)) ? $this->cms->find($request->id) : new CMS();
            $action = ($request->has('id') && !empty($request->id)) ? 'updated' : 'created';
            $request['updated_by'] = Auth::guard('admin')->user()->id;
            $cms->fill($request->all())->save();
            // $this->helper->dbEnd();
            $msg = __('admin/messages.action_msg', ['action' => __('admin/messages.' . $action), 'type' => 'Page']);
            return Redirect::route('cms_index')->with('message', $msg);
        } catch (Exception $e) {
            // $this->helper->dbRollBack();
            abort('404');
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    /**
     * -----------------------------------------------------
     * | Delete CMS record                                 |
     * |                                                   |
     * | @param Request $request                           |
     * | @return Response                                  |
     * -----------------------------------------------------
     */
    public function destroyCms(Request $request)
    {
        // $this->helper->dbStart();
        try {
            if (isset($request->id)) {
                $cms = $this->cms->find($request->id);
                $cms->delete();
                // $this->helper->dbEnd();
                $msg = __('admin/messages.action_msg', ['action' => __('admin/messages.deleted'), 'type' => 'Page']);
                return response()->json(['msg' => $msg, 'icon' => 'success']);
            } else {
                return response()->json(['msg' => Lang::get('formname.not_found'), 'icon' => 'info']);
            }
        } catch (Exception $e) {
            // $this->helper->dbRollBack();
            return response()->json(['msg' => $e->getMessage(), 'icon' => 'info']);
        }
    }

    /**
     * -----------------------------------------------------
     * | Multiple Delete CMS record                        |
     * |                                                   |
     * | @param Request $request                           |
     * | @return Response                                  |
     * -----------------------------------------------------
     */
    public function multideleteCMS(Request $request)
    {
        // $this->helper->dbStart();
        try {
            $cms = $this->cms::whereIn('id', $request->ids);
            if ($request->action == config('constant.delete')) {
                $cms->delete();
                // $this->helper->dbEnd();
                $msg = __('admin/messages.action_msg', ['action' => __('admin/messages.deleted'), 'type' => 'Page']);
            } else {
                $status = $request->action == config('constant.inactive') ? config('constant.status_inactive_value') : config('constant.status_active_value');
                $cms->update(['status' => $status]);
                // $this->helper->dbEnd();
                $action = ($request->action == config('constant.active')) ? __('admin/messages.active') : __('admin/messages.inactive');
                $msg = __('admin/messages.action_msg', ['action' => $action, 'type' => 'Page']);
            }
            return response()->json(['msg' => $msg, 'icon' => 'success']);
        } catch (Exception $e) {
            // $this->helper->dbRollBack();
            return response()->json(['msg' => $e->getMessage(), 'icon' => 'info']);
        }
    }

    /**
     * -------------------------------------------------
     * | Update company status details                 |
     * |                                               |
     * | @param Request $request                       |
     * | @return View                                  |
     * |------------------------------------------------
     */
    public function updateStatus(Request $request)
    {
        // $this->helper->dbStart();
        try {
            if (isset($request->id)) {
                $cms = $this->cms::whereUuid($request->id)->first();
                $status = $cms->status == config('constant.status_active_value') ? config('constant.status_inactive_value') : config('constant.status_active_value');
                $action = ($status == 1) ? __('admin/messages.active') : __('admin/messages.inactive');
                $cms->update(['status' => $status]);
                // $this->helper->dbEnd();
                $msg = __('admin/messages.action_msg', ['action' => $action, 'type' => 'Page']);
                return response()->json(['msg' => $msg, 'icon' => 'success']);
            } else {
                return response()->json(['msg' => Lang::get('formname.not_found'), 'icon' => 'info']);
            }
        } catch (Exception $e) {
            // $this->helper->dbRollBack();
            return response()->json(['msg' => $e->getMessage(), 'icon' => 'info']);
        }
    }
}
