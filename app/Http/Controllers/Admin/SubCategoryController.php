<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SubCategoryHelper;
use App\Http\Requests\Admin\SubCategoryFormRequest;
use Exception;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Redirect;

class SubCategoryController extends BaseController
{
    private $helper;
    public $viewConstant = 'admin.sub-category.';
    public function __construct(SubCategoryHelper $helper)
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
            $itemQuery = $itemQuery
                ->with('category')
                ->where(function ($query) use ($request) {
                if ($request->status) {
                    $query->activeSearch($request->status);
                }
            });
            $count_total = $itemQuery->count();
            $itemQuery = $itemQuery->skip($start)->take($limit);
            $subCategoryList = $itemQuery->orderBy('created_at', 'desc')->get();
            $count_filter = 0;
            if ($count_filter == 0) {
                $count_filter = $count_total;
            }
            return Datatables::of($subCategoryList)
                ->addColumn('action', function ($subCategory) {
                    return $this->getPartials($this->viewConstant . '_add_action', ['subCategory' => @$subCategory]);
                })
                ->addColumn('category', function ($banner) {
                    return @$banner->category->title;
                })
                ->editColumn('status', function ($subCategory) {
                    return $this->getPartials($this->viewConstant . '_add_status', ['subCategory' => @$subCategory]);
                })
                ->editColumn('created_at', function ($subCategory) {
                    return @$subCategory->proper_created_at;
                })
                ->editColumn('title', function ($subCategory) {
                    return $this->getPartials($this->viewConstant .'_add_message', ['subCategory' => $subCategory, 'title'=>__('formname.subcategory.title')]);
                })
                ->addColumn('checkbox', function ($subCategory) {
                    return $this->getPartials($this->viewConstant . '_add_checkbox', ['subCategory' => @$subCategory]);
                })
                ->with([
                    "draw" => $draw, 
                    "Total" => $count_total,
                    "recordsTotal" => $count_total,
                    "recordsFiltered" => $count_filter,
                ])
                ->rawColumns(['title','created_at', 'checkbox', 'action', 'status','category'])
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
                $subCategory = $this->helper->detail($uuid);
            }
            $statusList = $this->properStatusList();
            $categoryList = $this->categoryList();
            $title = isset($uuid) ? trans('formname.subcategory.update') : trans('formname.subcategory.create');
            return view($this->viewConstant . 'create', ['subCategory' => @$subCategory, 'title' => @$title, 'statusList' => @$statusList,'categoryList'=>@$categoryList]);
        } catch (Exception $e) {
            // abort(404);
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
    public function store(SubCategoryFormRequest $request, $uuid = null)
    {
        $this->helper->dbStart();
        try {
            $this->helper->store($request, $uuid);
            $this->helper->dbEnd();
            if ($request->has('id') && !empty($request->id)) {
                $msg = __('admin/messages.action_msg', ['action' => __('admin/messages.updated'), 'type' => 'Sub Category']);
            } else {
                $msg = __('admin/messages.action_msg', ['action' => __('admin/messages.created'), 'type' => 'Sub Category']);
            }
            return redirect()->route('subcategory.index')->with('message', $msg);
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
                $msg = __('admin/messages.action_msg', ['action' => __('admin/messages.deleted'), 'type' => 'subCategory']);
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
                $msg = __('admin/messages.action_msg', ['action' => $action, 'type' => 'subCategory']);
                return response()->json(['msg' => $msg, 'icon' => 'success']);
            }else if($request->action == config('constant.print')){
                $path = $this->helper->multiDelete($request);
                return response()->json(['path' => @$path, 'icon' => 'success']);
            }else {
                $this->helper->multiDelete($request);
                $msg = __('admin/messages.action_msg', ['action' => __('admin/messages.deleted'), 'type' => 'subCategory']);
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
        $subCategory = $this->helper->list();
        $subCategoryList =  $subCategory->where('title','ilike', '%' .$name . '%')
                        ->orderBy('title','asc')
                        ->pluck('title');
        return response()->json(['names' => $subCategoryList]);
    }
}
