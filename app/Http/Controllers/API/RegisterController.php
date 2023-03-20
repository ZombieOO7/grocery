<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Validation\Rule;
use Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Models\EmailTemplate;
use Exception;
use Illuminate\Support\Str;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Request\CreateUser;
use App\Models\FireBaseCredential;
use App\Models\UserRoleMaster;

class RegisterController extends BaseController
{
    public $successStatus = 200;
    protected $user;
    protected $emailTemplate;
    protected $userRoleMaster;

    /**
     * -------------------------------------------------------
     * | Create a new controller instance.                   |
     * |                                                     |
     * -------------------------------------------------------
     */
    public function __construct(User $user,EmailTemplate $emailTemplate,UserRoleMaster $userRoleMaster)
    {
        parent::__construct();
        $this->user = $user;
        $this->emailTemplate = $emailTemplate;
        $this->userRoleMaster = $userRoleMaster;
        $this->fillableCols = ['id','first_name', 'last_name', 'email', 'phone', 'user_type', 'company_id'];
    }

     /**
     * -------------------------------------------------------
     * | Sign up                                             |
     * |                                                     |
     * | @param $request                                     |
     * | @return Response                                    |
     * -------------------------------------------------------
     */
    public function register(Request $request)
    {
        /** Validation code start */
        $rules = [ 'email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL', 'first_name' => 'required|max:50', 'last_name' => 'required|max:50', 'phone' => 'required', 'device_id' => 'required', 'fcm_token' => 'required', 'device_type' => 'required', 'company_id' => 'required|exists:companies,id','user_type' => 'required','position_id' => 'required'];
        /** Validation code end */

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
           return $this->getResponse($this->blankObject,false,400,$validator->errors()->first()); /** Display data to frontend code. */
        } else {
            $userDataArr = $this->user->where('deleted_at', '=' ,null)->where('email', '=', strtolower($request->email))->get();
            if (count($userDataArr) == 0) {
                /** Set value in $request code start */
                $requestArr = $this->setRequestValue($request);
                /** Set value in $request code end */
                $randomPassword = Str::random();
                $serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/FirebaseKey.json');
                $auth = (new Factory)->withServiceAccount($serviceAccount)->createAuth();
                try  {
                    $checkEmail = $auth->getUserByEmail($request->email);
                    if (isset($checkEmail->uid)){
                        return $this->getResponse($this->blankObject,false,400,__('api_messages.register.email_already_taken')); /** Display data to frontend code. */
                    }
                } catch (Exception $e) {
                    $createdUser = $this->createFireBaseCredential($request, $randomPassword);
                    if (isset($createdUser->uid)) {
                        $userObj = $this->user::create($requestArr->all()); /** Create function for user */
                        if ($userObj) {
                            /** Update user code start */
                            $this->updateUserArr($request, $userObj , $createdUser, $randomPassword);
                            /** Update user end */
                        }
                        /** User response code start */
                        $userArr = $this->getUserArrResponse($request, $userObj);
                        /** User response code end */
                        return $this->getResponse($userArr,true,200,__('api_messages.register.account_created'));  /** Display data to frontend code. */
                    } else {
                        return $this->getResponse($this->blankObject,false,400,__('api_messages.register.somthing_went_wrong')); /** Display data to frontend code. */
                    }
                }
            } else {
                return $this->getResponse($this->blankObject,false,400,__('api_messages.register.email_already_taken')); /** Display data to frontend code. */
            }
        }
    }



    /**
     * -------------------------------------------------------
     * | Set custom values in request                        |
     * |                                                     |
     * | @param  object/array $request                       |
     * | @return response                                    |
     * -------------------------------------------------------
     */
    public function setRequestValue($request)
    {
        array_set($request, 'email', strtolower($request->email));
        array_set($request, 'first_name', strtolower($request->first_name));
        array_set($request, 'last_name', strtolower($request->last_name));
        array_set($request, 'status', 0);
        array_set($request, 'user_type', $request->user_type); 
        array_set($request, 'role_id', $request->position_id); 
        return $request;
    }

     /**
     * -------------------------------------------------------
     * | get user response                                   |
     * |                                                     |
     * | @param  object/array $request                       |
     * | @return response                                    |
     * -------------------------------------------------------
     */
    public function getUserArrResponse($request, $userObj)
    {
        $userArr = replace_key($userObj->only($this->fillableCols), 'id' , 'user_id');
        $userArr['profile_pic'] = $userObj->profile_image;
        $role = $this->userRoleMaster::whereId($request->position_id)->first();
        $userArr['position']['position_id'] = $userObj->role_id;
        $userArr['position']['position_name'] = @$role->name;
        $userArr['company']['company_id'] = $userObj->company->id;
        $userArr['company']['company_name'] = $userObj->company->title;
        /** Insert fcm token & device type code start */
        $this->userDeviceTokens($userObj, $request);
        /** Insert fcm token & device type code end */
        $userArr['firebase_credential']['firebase_email'] = $userObj->firebaseCredential[0]->username;
        $userArr['firebase_credential']['firebase_password'] = $userObj->firebaseCredential[0]->password;
        $userArr['firebase_credential']['firebase_uid'] = $userObj->firebaseCredential[0]->uid;
        $removedNullArr = removeNullFromArray($userArr); /** To remove null value from array */
        return $userArr;
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
            FireBaseCredential::create(['user_id' => $userObj->id,'username' => $request->email,'password' => $randomPassword, 'uid' => $createdUser->uid]);
        }
        /**End Firebase account create**/
        
        /** This is for email sending code start*/
        $emailDataObj = $this->emailTemplate::where('slug', config('constant.verify_email'))->first();
        if (isset($emailDataObj)) {
            $emailData = [ 'display_name' => $userObj->first_name. ' '.$userObj->last_name , 'content' => $emailDataObj->body ];
            $sub = $emailDataObj->subject;
            $file = 'email.verify_mail';
            $usersData = $userObj;
            $this->singleUserEmailNotifiaction($emailData, $sub, $usersData ,$file);
        }
        //** This is for email sending code end*/
    }

    /**
     * -------------------------------------------------------
     * | Firebase Credential store.                          |
     * |                                                     |
     * | @param $request                                     |
     * | @return response                                    |
     * -------------------------------------------------------
     */
    public function createFireBaseCredential($request, $randomPassword)
    {
        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__.'/FirebaseKey.json');
        $auth = (new Factory)->withServiceAccount($serviceAccount)->createAuth();
        $userProperties = [ 'email' => $request->email, 'emailVerified' => false, 'password' => $randomPassword, 'displayName' => $request->first_name . ' ' .$request->first_name,'disabled' => false ];
        return $auth->createUser($userProperties);
    }
}
