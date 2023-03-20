<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Http\Resources\BannerResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\SubCategoryResource;
use App\Models\Banner;
use App\Models\Category;
use Validator;
use Illuminate\Pagination\Paginator;
use App\Models\User;
use App\Models\Notification;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\UserRoleMaster;
use Exception;

class MasterController extends BaseController
{
    public $successStatus = 200;
    protected $category;
    protected $user;
    protected $notification;
    protected $userRoleMaster;
    protected $product;
    protected $banner, $subCategory;

    /**
     * -------------------------------------------------------
     * | Create a new controller instance.                   |
     * |                                                     |
     * -------------------------------------------------------
     */
    public function __construct(Category $category, SubCategory $subCategory, User $user, Notification $notification, UserRoleMaster $userRoleMaster, Product $product, Banner $banner)
    {
        parent::__construct();
        $this->category = $category;
        $this->subCategory = $subCategory;
        $this->user = $user;
        $this->notification = $notification;
        $this->userRoleMaster = $userRoleMaster;
        $this->product = $product;
        $this->banner = $banner;
    }

    /**
     * -------------------------------------------------------
     * | Category List.                                      |
     * |                                                     |
     * -------------------------------------------------------
     */
    public function categoryList()
    {
        $categories = $this->category::active()->get();
        $list = CategoryResource::collection($categories);
        return $this->getResponse($list, true, 200, __('api_messages.list', ['type' => 'Categpry']));
    }

    /**
     * -------------------------------------------------------
     * | Sub Category List.                                  |
     * |                                                     |
     * -------------------------------------------------------
     */
    public function subCategoryList(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), ['category_id' => 'required|integer|min:1']);
            /** Validation code */
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first(), 400);
            }
            $categories = $this->subCategory::active()->get();
            $list = SubCategoryResource::collection($categories);
            return $this->getResponse($list, true, 200, __('api_messages.list', ['type' => 'Categpry']));
        } catch (Exception $error) {
            return $this->getResponse($this->blankObject, false, $error->getCode() ?? 501, $error->getMessage());
        }
    }


    /**
     * -------------------------------------------------------
     * | Engineers List.                                     |
     * |                                                     |
     * -------------------------------------------------------
     */
    public function engineersList()
    {
        $listArr = $this->user::whereUserType(config('constant.engineer'))->whereStatus(1)->whereNull('deleted_at')->whereNotNull('email_verified_at')->get();
        $engFinalArr = [];
        foreach ($listArr as $key => $value) {
            /** Used to display all engineer */
            $arr['engineer_id'] = $value->id;
            $arr['engineer_name'] = $value->full_name;
            $arr['email'] = $value->email;
            $arr['phone'] = $value->phone;
            $arr['profile_pic'] = $value->profile_image;
            $role = $this->userRoleMaster::whereId($value->role_id)->first();
            $arr['position']['position_id'] = $value->role_id;
            $arr['position']['position_name'] = @$role->name;
            $arr['company']['company_id'] = $value->company->id;
            $arr['company']['company_name'] = $value->company->title;
            $removedNullArr = removeNullFromArray($arr);
            /** To remove null value from array */
            array_push($engFinalArr, $removedNullArr);
        }
        $list['engineers_list'] = $engFinalArr;
        return count($listArr) ? $this->getResponse($list, true, 200, __('api_messages.list', ['type' => __('api_messages.engineers')])) : $this->getResponse($this->blankObject, false, 405, __('api_messages.master.no_engineers_found'));
    }

    /**
     * -------------------------------------------------------
     * | Notifications List.                                 |
     * |                                                     |
     * -------------------------------------------------------
     */
    public function notificationList(Request $request)
    {
        $validator = Validator::make($request->all(), ['page_number' => 'required|integer|min:1']);
        /** Validation code */
        if ($validator->fails()) {
            return $this->getResponse($this->blankObject, false, 400, $validator->errors()->first());
        } else {
            $currentPage = trim($request->page_number); // You can set this to any page you want to paginate to

            // Make sure that you call the static method currentPageResolver() before querying users
            Paginator::currentPageResolver(function () use ($currentPage) {
                return $currentPage;
            });

            $tokenUserId = $request->user()->token()->user_id;
            $notificationObjData = $this->userNotification::whereUserId($tokenUserId)->orderBy('created_at', 'DESC');
            $notificationObj = $notificationObjData->paginate(20);
            /** Pagination */
            $jobResults = $notificationObj->toArray();
            $notificationFinalArr = [];
            $unreadCount = 0;
            foreach ($notificationObj as $notificationObjKey => $notificationObjVal) {
                /** Used to display all faq */
                $faqArr['job_id'] = $notificationObjVal->model_id;
                $faqArr['content'] = $notificationObjVal->description;
                $faqArr['date_time'] = timeStampConverter($notificationObjVal->created_at);
                $removedNullArr = removeNullFromArray($faqArr);
                /** To remove null value from array */
                array_push($notificationFinalArr, $removedNullArr);
                if ($notificationObjVal->is_read == 0) {
                    $unreadCount += 1;
                }
            }
            $notificationArre['notification_list'] = $notificationFinalArr;
            $notificationArre['unread_notification_count'] = $unreadCount;
            $loggedInUser = $this->user::whereId($tokenUserId)->first();
            $unreadJobRequestCount = 0;
            if ($loggedInUser->user_type == config('constant.engineer')) {
                $unreadJobRequestCount = Job::whereUserId($loggedInUser->id)->where('status', '<>', 3)->whereIsRead(0)->count();
                $unreadWorkOrderCount = Job::whereUserId($loggedInUser->id)->where('status', 3)->whereIsRead(0)->count();
            } else if ($loggedInUser->user_type == config('constant.operator')) {
                $unreadJobRequestCount = Job::whereUserId($loggedInUser->id)->whereIsRead(0)->count();
                $unreadWorkOrderCount = 0;
            } else {
                $unreadJobRequestCount = Job::whereStatus(1)->whereIsRead(0)->count();
                $unreadWorkOrderCount = 0;
            }
            $notificationArre['unread_job_request_count'] = $unreadJobRequestCount;
            $notificationArre['unread_workorder_count'] = $unreadWorkOrderCount;
            $emptyArr['unread_notification_count'] = 0;
            $emptyArr['unread_job_request_count'] = $unreadJobRequestCount;
            $emptyArr['unread_workorder_count'] = $unreadWorkOrderCount;
            $lastPage = $notificationObj->lastPage();
            /** Last page of record */
            if ($currentPage > $lastPage) {
                /** Check if this is last page or not */
                return $this->getResponse($emptyArr, false, 405, __('api_messages.no_records_found'), '', $notificationObj, $lastPage);
            } else {
                return count($notificationFinalArr) > 0 ? $this->getResponse($notificationArre, true, 200, __('api_messages.list', ['type' => __('api_messages.notifications')]), '', $notificationObj, $lastPage) : $this->getResponse($emptyArr, false, 405, __('api_messages.no_records_found'), '', $notificationObj, $lastPage);
            }
        }
    }

    public function productList(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), ['page_number' => 'required|integer|min:1']);
            /** Validation code */
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first(), 400);
            } else {
                $currentPage = trim($request->page_number); // You can set this to any page you want to paginate to

                // Make sure that you call the static method currentPageResolver() before querying users
                Paginator::currentPageResolver(function () use ($currentPage) {
                    return $currentPage;
                });

                $query = $this->product::with('category', 'subCategory', 'productMedia')->whereStatus(config('constant.status_active_value'))->whereNull('deleted_at');
                if ($request->has('sub_category_id')) {
                    $productObjData = $query->where('sub_category_id', $request->sub_category_id);
                }
                $productObjData = $query->orderBy('created_at', 'DESC');
                $productObj = $productObjData->paginate(config('constant.job_page_limit'));
                /** Pagination */
                $products = $productObj;
                $productFinalArr = ProductResource::collection($products);
                $productArre['products'] = $productFinalArr;
                $lastPage = $productObj->lastPage();
                /** Last page of record */
                if ($currentPage > $lastPage) {
                    /** Check if this is last page or not */
                    return $this->getResponse($this->blankObject, false, 405, __('api_messages.no_records_found'), '', $productObj, $lastPage);
                } else {
                    return count($productFinalArr) > 0 ? $this->getResponse($productArre, true, 200, __('api_messages.product_list'), '', $productObj, $lastPage) : $this->getResponse($this->blankObject, false, 405, __('api_messages.no_records_found'), '', $productObj, $lastPage);
                }
            }
        } catch (Exception $error) {
            return $this->getResponse($this->blankObject, false, $error->getCode() ?? 501, $error->getMessage());
        }
    }

    /**
     * -------------------------------------------------------
     * | Category List.                                      |
     * |                                                     |
     * -------------------------------------------------------
     */
    public function bannerList()
    {
        $banner = $this->banner::with('attachment')->active()->orderBy('created_at', 'ASC')->get();
        $list = BannerResource::collection($banner);
        return $this->getResponse($list, true, 200, __('api_messages.list', ['type' => 'Banner']));
    }

    /**
     * -------------------------------------------------------
     * | Category List.                                      |
     * |                                                     |
     * -------------------------------------------------------
     */
    public function productDetail(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), ['id' => 'required']);
            /** Validation code */
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first(), 400);
            }
            $product = $this->product::where('id', $request->id)->with('attachment')->active()->first();
            if (!$product) {
                throw new Exception('Product not found', 200);
            }
            $data = new ProductResource($product);
            return $this->getResponse($data, true, 200, __('api_messages.list', ['type' => 'Product']));
        } catch (Exception $error) {
            return $this->getResponse($data, true, $error->getCode() ?? 501, $error->getMessage());
        }
    }
}
