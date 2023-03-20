<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\UserHelper;
use App\Http\Requests\Admin\UserFormRequest;
use App\Models\FireBaseCredential;
use App\Models\User;
use App\Models\UserRoleMaster;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Request\CreateUser;
use Kreait\Firebase\ServiceAccount;
use Redirect;
use Yajra\Datatables\Datatables;

class UserController extends BaseController
{
    private $helper;
    public function __construct(UserHelper $helper, UserRoleMaster $userRole)
    {
        $this->helper = $helper;
        $this->userRole = $userRole;
    }

    /**
     * -----------------------------------------------------
     * | User list                                         |
     * |                                                   |
     * | @return View                                      |
     * -----------------------------------------------------
     */
    public function index()
    {
        try {
            $statusList = $this->statusList();
            return view('admin.user.index', ['statusList' => @$statusList]);
        } catch (Exception $e) {
            abort(404);
        }
    }

    /**
     * -----------------------------------------------------
     * | User datatables data                              |
     * |                                                   |
     * | @param Request $request                           |
     * | @return Response                                  |
     * -----------------------------------------------------
     */
    public function getdata(Request $request)
    {
        try {
            $userDataTable = user::orderBy('created_at', 'desc')
                ->where(function ($query) use ($request) {
                    if ($request->status) {
                        $query->activeSearch($request->status);
                    }
                })->verifiedUser()->get();
            return Datatables::of($userDataTable)
                ->addColumn('action', function ($user) {
                    return View::make('admin.user._add_action', ['user' => $user])->render();
                })
                ->editColumn('status', function ($user) {
                    return $user->active_tag;
                })
                ->editColumn('company_id', function ($user) {
                    return @$user->company->title;
                })
                ->editColumn('user_type', function ($user) {
                    return @config('constant.user_types')[$user->user_type];
                })
                ->editColumn('first_name', function ($user) {
                    return View::make('admin.user._add_message', ['title'=>__('formname.first_name'),'name' => @$user->first_name])->render();
                })
                ->editColumn('last_name', function ($user) {
                    return View::make('admin.user._add_message', ['title'=>__('formname.last_name'),'name' => @$user->last_name])->render();
                })
                ->addColumn('checkbox', function ($user) {
                    return View::make('admin.user._add_checkbox', ['user' => $user])->render();
                })
                ->editColumn('created_at', function ($user) {
                    return $user->proper_created_at;
                })
                ->rawColumns(['first_name','last_name','created_at', 'user_type', 'company_id', 'checkbox', 'action', 'status'])
                ->make(true);
        } catch (Exception $e) {
            abort('404');
        }
    }

    /**
     * -----------------------------------------------------
     * | Create/Update User form                           |
     * |                                                   |
     * | @param $id                                        |
     * | @return View                                      |
     * -----------------------------------------------------
     */
    public function create($uuid = null)
    {
        try {
            $job = [];
            if (isset($uuid)) {
                $user = $this->helper->findUserByUuid($uuid);
                $job = $this->helper->jobDetail($user->id);
                $subRoleList = $this->userRole::whereUserType($user->user_type)->pluck('name','id');
            }
            $userTypeList = $this->userTypeList();
            $statusList = $this->properStatusList();
            $companyList = $this->companyList();
            return view('admin.user.create_user', ['user' => @$user, 'statusList' => @$statusList, 'userTypeList' => @$userTypeList, 'companyList' => @$companyList, 'job'=>@$job, 'subRoleList' => @$subRoleList]);
        } catch (Exception $e) {
            return redirect()->route('user_index')->with('error', $e->getMessage());
            // abort('404');
        }
    }

    /**
     * -----------------------------------------------------
     * | Store/Edit User form                              |
     * |                                                   |
     * | @param UserFormRequest $request                   |
     * | @return Redirect                                  |
     * -----------------------------------------------------
     */
    public function store(UserFormRequest $request, $uuid=null)
    {
        $this->helper->dbStart();
        try {
            $msg = ($request->has('id') && !empty($request->id)) ?  __('admin/messages.action_msg', ['action' => __('admin/messages.updated'), 'type' => 'User']):
             __('admin/messages.action_msg', ['action' => __('admin/messages.created'), 'type' => 'User']);

            if (empty($request->id)) {
                $randomPassword = Str::random();
                $serviceAccount = ServiceAccount::fromJsonFile(__DIR__ . '/FirebaseKey.json');
                $auth = (new Factory)->withServiceAccount($serviceAccount)->createAuth();
                $createdUser = $this->createFireBaseCredential($request, $randomPassword);
                if (isset($createdUser->uid)) {
                    $userObj = $this->helper->store($request);
                    if ($userObj) {
                        /** Update user code start */
                        $this->updateUserArr($request, $userObj, $createdUser, $randomPassword);
                        /** Update user end */
                    }
                }
            } else {
                $userObj = User::find($request->id);
                $this->sendPushNotificationToSingle($userObj,'profile_updated','Your Profile Updated By The Admin','Profile Updated');
                $this->helper->store($request);
            }
            $this->helper->dbEnd();
            return redirect()->route('user_index')->with('message', $msg);
        } catch (Exception $e) {
            $this->helper->dbRollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * -----------------------------------------------------
     * | Delete User record                                |
     * |                                                   |
     * | @param Request $request                           |
     * | @return Response                                  |
     * -----------------------------------------------------
     */
    public function destroyUser(Request $request)
    {
        $this->helper->dbStart();
        try {
            if (isset($request->id)) {
                /** Remove record from firebase */
                $user = User::whereId($request->id)->firstOrFail();
                if (isset($user->firebaseCredential[0]->uid)) {
                    $serviceAccount = ServiceAccount::fromJsonFile(__DIR__ . '/FirebaseKey.json');
                    $auth = (new Factory)->withServiceAccount($serviceAccount)->createAuth();
                    $userFireBaseUid = $auth->getUser($user->firebaseCredential[0]->uid);
                    if (isset($userFireBaseUid)) {
                        $auth->deleteUser($user->firebaseCredential[0]->uid);
                    }
                    $user->firebaseCredential()->forceDelete();
                }

                $msg = $this->helper->delete($request->id);
                $this->helper->dbEnd();
                $msg = __('admin/messages.action_msg', ['action' => __('admin/messages.deleted'), 'type' => 'User']);
                return response()->json(['msg' => $msg, 'icon' => 'success']);
            } else {
                return response()->json(['msg' => __('admin/messages.not_found'), 'icon' => 'info']);
            }
        } catch (Exception $e) {
            $this->helper->dbRollBack();
            return response()->json(['msg' => $e->getMessage(), 'icon' => 'info']);
            // return response()->json(['msg' => __('admin/messages.not_found'), 'icon' => 'info']);
        }
    }

    /**
     * -----------------------------------------------------
     * | Multiple Delete User record                       |
     * |                                                   |
     * | @param Request $request                           |
     * | @return Response                                  |
     * -----------------------------------------------------
     */
    public function multideleteUser(Request $request)
    {
        $this->helper->dbStart();
        try {
            if ($request->action == config('constant.inactive') || $request->action == config('constant.active')) {
                $this->helper->multiDelete($request);
                $action = ($request->action == config('constant.active')) ? __('admin/messages.active') : __('admin/messages.inactive');
                $msg = __('admin/messages.action_msg', ['action' => $action, 'type' => 'User']);
                return response()->json(['msg' => $msg, 'icon' => 'success']);
            } else {
                $users = User::whereIn('id',$request->ids)->get();
                foreach($users as $user){
                    if (isset($user->firebaseCredential[0]->uid)) {
                        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__ . '/FirebaseKey.json');
                        $auth = (new Factory)->withServiceAccount($serviceAccount)->createAuth();
                        $userFireBaseUid = $auth->getUser($user->firebaseCredential[0]->uid);
                        if (isset($userFireBaseUid)) {
                            $auth->deleteUser($user->firebaseCredential[0]->uid);
                        }
                        $user->firebaseCredential()->forceDelete();
                    }
                }
                $this->helper->multiDelete($request);
                $msg = __('admin/messages.action_msg', ['action' => __('admin/messages.deleted'), 'type' => 'User']);
            }
            $this->helper->dbEnd();
            return response()->json(['msg' => @$msg, 'icon' => 'success']);
        } catch (Exception $e) {
            $this->helper->dbRollBack();
            return response()->json(['msg' => $e->getMessage(), 'icon' => 'info']);
            // return response()->json(['msg' => __('admin/messages.not_found'), 'icon' => 'info']);
        }
    }

    /**
     * -----------------------------------------------------
     * | Update status                                     |
     * |                                                   |
     * | @param Request $request                           |
     * | @return Response                                  |
     * -----------------------------------------------------
     */
    public function updateStatus(Request $request)
    {
        $this->helper->dbStart();
        try {
            if (isset($request->id)) {
                $msg = $this->helper->statusUpdate($request->id);
                $this->helper->dbEnd();
                return response()->json($msg);
            }
        } catch (Exception $e) {
            $this->helper->dbRollBack();
            return response()->json(['msg' => $e->getMessage(), 'icon' => 'info']);
            // return response()->json(['msg' => __('admin/messages.not_found'), 'icon' => 'info']);
        }
    }
    /**
     * -----------------------------------------------------
     * | Display user detail                               |
     * |                                                   |
     * | @param Request $request                           |
     * | @return View                                      |
     * -----------------------------------------------------
     */
    public function detail($id)
    {
        try{
            $user = $this->helper->findUserById($id);
            $jobs = $this->helper->jobDetail($id);
            $userTypeList = $this->userTypeList();
            $subRoleList = $this->userRole::whereUserType($user->user_type)->pluck('name','id');
            return view('admin.user.profile', ['user' => @$user,'jobs'=>@$jobs,'userTypeList'=>@$userTypeList, 'subRoleList' => @$subRoleList]);
        } catch (Exception $e) {
            abort('404');
        }
    }

    /**
     * -----------------------------------------------------
     * | Verify user                                       |
     * |                                                   |
     * | @param $id $status                                |
     * | @return View                                      |
     * -----------------------------------------------------
     */
    public function verify($id, $status)
    {
        try{
            $user = $this->helper->verify($id, $status);
            $action = ($status == 1) ? __('admin/messages.verify') : __('admin/messages.decline');
            return redirect()->back()->with(['message' => __('admin/messages.verified_msg', ['action' => $action])]);
        } catch (Exception $e) {
            return response()->json(['msg' => __('admin/messages.not_found'), 'icon' => 'info']);
        }
    }

    /**
     * -------------------------------------------------------
     * | uCreate Firebase Credentials                        |
     * |                                                     |
     * | @param  object/array $request                       |
     * | @return response                                    |
     * -------------------------------------------------------
     */
    public function createFireBaseCredential($request, $randomPassword)
    {
        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__ . '/FirebaseKey.json');
        $auth = (new Factory)->withServiceAccount($serviceAccount)->createAuth();
        $userProperties = ['email' => $request->email, 'emailVerified' => false, 'password' => $randomPassword, 'displayName' => $request->first_name . ' ' . $request->first_name, 'disabled' => false];
        return $auth->createUser($userProperties);
    }

    /**
     * -------------------------------------------------------
     * | update user data                                    |
     * |                                                     |
     * | @param  object/array $request                       |
     * | @return response                                    |
     * -------------------------------------------------------
     */
    public function updateUserArr($request, $userObj, $createdUser, $randomPassword)
    {
        /** Firebase account create and update code start **/
        if ($createdUser) {
            FireBaseCredential::create(['user_id' => $userObj->id, 'username' => $request->email, 'password' => $randomPassword, 'uid' => $createdUser->uid]);
        }
        /**End Firebase account create**/
    }

    /**
     * -------------------------------------------------------
     * | update user position                                |
     * |                                                     |
     * | @param  object/array $request                       |
     * | @return response                                    |
     * -------------------------------------------------------
     */
    public function changePosition(Request $request){
        $this->helper->dbStart();
        try {
            $msg = $this->helper->changePosition($request);
            $this->helper->dbEnd();
            return response()->json($msg);
        } catch (Exception $e) {
            $this->helper->dbRollBack();
            return response()->json(['msg' => $e->getMessage(), 'icon' => 'info']);
        }
    }

    /**
     * -------------------------------------------------------
     * | get users job detail                                |
     * |                                                     |
     * | @param  object/array $request                       |
     * | @return response                                    |
     * -------------------------------------------------------
     */
    public function jobDetail(Request $request){
        try {
            $job = $this->helper->jobDetail($request->id);
            if(count($job)>0){
                $msg = ['msg'=>__('admin/messages.assign_job_status'),'icon'=>'info'];
                return response()->json($msg);
            }
        } catch (Exception $e) {
            return response()->json(['msg' => $e->getMessage(), 'icon' => 'info']);
        }
    }

    /**
     * -------------------------------------------------------
     * | get multiple users job detail                       |
     * |                                                     |
     * | @param  object/array $request                       |
     * | @return response                                    |
     * -------------------------------------------------------
     */
    public function multiUserJobDetail(Request $request){
        try {
            $job = $this->helper->multiUserJobDetail($request->id);
            if(count($job)>0){
                $msg = ['msg'=>__('admin/messages.assign_job_status'),'icon'=>'info'];
                return response()->json($msg);
            }
        } catch (Exception $e) {
            return response()->json(['msg' => $e->getMessage(), 'icon' => 'info']);
        }
    }

    public function sendPushNotificationToSingle($userNotificationObj,$type,$message,$title) 
    {
        if (@$userNotificationObj->usersFcmTokens[0]) {
            $userToken = $userNotificationObj->usersFcmTokens[0]['fcm_token'];
            $userDeviceType = $userNotificationObj->usersFcmTokens[0]['device_type'];
            $fcmData = [
                'type' => $type,
                'message' => $message,
            ];
            $this->basicPushNotification($title, $message, $fcmData, $userToken, $userDeviceType);
        }
    }
}
