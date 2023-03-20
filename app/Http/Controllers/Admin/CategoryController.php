<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CategoryHelper;
use App\Http\Requests\Admin\CategoryFormRequest;
use Exception;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Redirect;

class CategoryController extends BaseController
{
    private $helper;
    public $viewConstant = 'admin.category.';
    public function __construct(CategoryHelper $helper)
    {
        $this->helper = $helper;
        $this->helper->mode = 'admin';
    }
    /**
     * -------------------------------------------------
     * | Display Machine list                          |
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
     * | Get machine datatable data                    |
     * |                                               |
     * | @param Request $request                       |
     * | @return View                                  |
     * |------------------------------------------------
     */
    public function getdata(Request $request)
    {
        try {
            $draw = intval($request->draw) + 1 ;
            $limit = @$request->length?? 10;
            $start = @$request->start ?? 0;
            $itemQuery = $this->helper->list();
            $itemQuery = $itemQuery->where(function ($query) use ($request) {
                if ($request->status) {
                    $query->activeSearch($request->status);
                }
            });
            $count_total = $itemQuery->count();
            $categoryList = $itemQuery->skip($start)->take($limit);
            $count_filter = 0;
            if ($count_filter == 0) {
                $count_filter = $count_total;
            }
            return Datatables::of($categoryList)
                ->addColumn('action', function ($category) {
                    return $this->getPartials($this->viewConstant . '_add_action', ['category' => @$category]);
                })
                ->editColumn('status', function ($category) {
                    return $this->getPartials($this->viewConstant . '_add_status', ['category' => @$category]);
                })
                ->editColumn('created_at', function ($category) {
                    return @$category->proper_created_at;
                })
                ->editColumn('title', function ($category) {
                    return $this->getPartials($this->viewConstant .'_add_message', ['category' => $category, 'title'=>__('formname.category.title')]);
                })
                ->addColumn('checkbox', function ($category) {
                    return $this->getPartials($this->viewConstant . '_add_checkbox', ['category' => @$category]);
                })
                ->with([
                    "draw" => $draw, 
                    "Total" => $count_total,
                    "recordsTotal" => $count_total,
                    "recordsFiltered" => $count_filter,
                ])
                ->rawColumns(['title','created_at', 'checkbox', 'action', 'status'])
                ->skipPaging()
                ->make(true);
        } catch (Exception $e) {
            abort('404');
        }
    }

    /**
     * -------------------------------------------------
     * | Create machine page                           |
     * |                                               |
     * | @param $id                                    |
     * | @return View                                  |
     * |------------------------------------------------
     */
    public function create($uuid = null)
    {
        try {
            if (isset($uuid)) {
                $category = $this->helper->detail($uuid);
            }
            $statusList = $this->properStatusList();
            $title = isset($uuid) ? trans('formname.category.update') : trans('formname.category.create');
            return view($this->viewConstant . 'create', ['category' => @$category, 'title' => @$title, 'statusList' => @$statusList]);
        } catch (Exception $e) {
            abort(404);
            return Redirect::back()->with('error', $e->getMessage());
        }
    }

    /**
     * -------------------------------------------------
     * | Store machinne details                        |
     * |                                               |
     * | @param SubjectFormRequest $request            |
     * | @return View                                  |
     * |------------------------------------------------
     */
    public function store(CategoryFormRequest $request, $uuid = null)
    {
        $this->helper->dbStart();
        try {
            $this->helper->store($request, $uuid);
            $this->helper->dbEnd();
            if ($request->has('id') && !empty($request->id)) {
                $msg = __('admin/messages.action_msg', ['action' => __('admin/messages.updated'), 'type' => 'Category']);
            } else {
                $msg = __('admin/messages.action_msg', ['action' => __('admin/messages.created'), 'type' => 'Category']);
            }
            return redirect()->route('category.index')->with('message', $msg);
        } catch (Exception $e) {
            $this->helper->dbRollBack();
            return Redirect::back()->with('error', $e->getMessage());
            abort('404');
        }
    }
    /**
     * -------------------------------------------------
     * | Delete machine details                        |
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
                $msg = __('admin/messages.action_msg', ['action' => __('admin/messages.deleted'), 'type' => 'Category']);
                return response()->json(['msg' => $msg, 'icon' => 'success']);
            } else {
                return response()->json(['msg' => __('admin/messages.not_found'), 'icon' => 'info']);
            }
        } catch (Exception $e) {
            $this->helper->dbRollBack();
            return response()->json(['msg' => $e->getMessage(), 'icon' => 'info']);
        }
    }
    /**
     * -------------------------------------------------
     * | Delete multiple machine                       |
     * |                                               |
     * | @param Request $request                       |
     * | @return Response                              |
     * |------------------------------------------------
     */
    public function multidelete(Request $request)
    {
        // dd($request->all());
        $this->helper->dbStart();
        try {
            $this->helper->dbEnd();
            if ($request->action == config('constant.inactive') || $request->action == config('constant.active')) {
                $this->helper->multiDelete($request);
                $action = ($request->action == config('constant.active')) ? __('admin/messages.active') : __('admin/messages.inactive');
                $msg = __('admin/messages.action_msg', ['action' => $action, 'type' => 'Category']);
                return response()->json(['msg' => $msg, 'icon' => 'success']);
            }else if($request->action == config('constant.print')){
                $path = $this->helper->multiDelete($request);
                return response()->json(['path' => @$path, 'icon' => 'success']);
            }else {
                $this->helper->multiDelete($request);
                $msg = __('admin/messages.action_msg', ['action' => __('admin/messages.deleted'), 'type' => 'Category']);
                return response()->json(['msg' => @$msg, 'icon' => 'success']);
            }
        } catch (Exception $e) {
            $this->helper->dbRollBack();
            return response()->json(['msg' => $e->getMessage(), 'icon' => 'info']);
        }
    }
    /**
     * -------------------------------------------------
     * | Update machine status details                 |
     * |                                               |
     * | @param Request $request                       |
     * | @return View                                  |
     * |------------------------------------------------
     */
    public function updateStatus(Request $request)
    {
        $this->helper->dbStart();
        try {
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

    /**
     * -------------------------------------------------
     * | Get machine datatable data                    |
     * |                                               |
     * | @param Request $request                       |
     * | @return View                                  |
     * |------------------------------------------------
     */
    public function list(Request $request)
    {
        $name = $request->name;
        $category = $this->helper->list();
        $categoryList =  $category->where('title','ilike', '%' .$name . '%')
                        ->orderBy('title','asc')
                        ->pluck('title');
        return response()->json(['names' => $categoryList]);
    }
}
