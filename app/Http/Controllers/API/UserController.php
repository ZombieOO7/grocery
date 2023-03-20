<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Validator;
use Illuminate\Validation\Rule;
use Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Models\UserSupport;
use App\Models\ContactUs;
use Exception;
use App\Models\UserDeviceToken;
use App\Models\UserRoleMaster;

class UserController extends BaseController
{
    public $successStatus = 200;
    protected $user;
    protected $userSupport;
    protected $contactUs;
    protected $userRoleMaster;

    /**
     * -------------------------------------------------------
     * | Create a new controller instance.                   |
     * |                                                     |
     * -------------------------------------------------------
     */
    public function __construct(User $user,ContactUs $contactUs,UserSupport $userSupport,UserRoleMaster $userRoleMaster)
    {
        parent::__construct();
        $this->user = $user;
        $this->userSupport = $userSupport;
        $this->contactUs = $contactUs;
        $this->userRoleMaster = $userRoleMaster;
        $this->fillableCols = ['id','first_name', 'last_name', 'email', 'phone'];
    }

    /**
     * -------------------------------------------------------
     * | Set a new User Password                             |
     * |                                                     |
     * | @param $request                                     |
     * | @return Response                                    |
     * -------------------------------------------------------
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [ 'old_password' => 'required', 'new_password' => 'required_with:confirm_password|same:confirm_password','confirm_password' => 'required' ]); /** Validation code */
        if ($validator->fails()) {
            return $this->getResponse($this->blankObject,false,400,$validator->errors()->first());
        } else {
            $tokenUserId = $request->user()->token()->user_id;
            $userObj = $this->user::whereId($tokenUserId)->first();
            if (Hash::check($request->old_password, @$userObj->password)) { 
                $userObj->password = Hash::make($request->get('new_password')); /** Set new password */
                $userObj->save();
                return $this->getResponse($this->blankObject,true,200,__('api_messages.user.password_changed'));
            } else {
                return $this->getResponse($this->blankObject,false,400,'Old password do not match with our records!');
            }
        }
    }

    /**
     * -------------------------------------------------------
     * | Contact Us                                          |
     * |                                                     |
     * | @param $request                                     |
     * | @return Response                                    |
     * -------------------------------------------------------
     */
    public function contactUs(Request $request)
    {
        $validator = Validator::make($request->all(), [ 'message' => 'required','subject' => 'required']); /** Validation code */
        $tokenUserId = $request->user()->token()->user_id; /** Auth id */
        array_set($request,'user_id',$tokenUserId);
        $this->sendNotificationToAdmin($tokenUserId,'Contact us request received');/** Notifiy Admin */
        return $validator->fails() ? $this->getResponse($this->blankObject,false,400,$validator->errors()->first()) : $this->getResponse($this->contactUs::create($request->all()),true,200,'Contact us details submitted successfully.');
    }

    /**
     * -------------------------------------------------------
     * | Support                                             |
     * |                                                     |
     * | @param $request                                     |
     * | @return Response                                    |
     * -------------------------------------------------------
     */
    public function support(Request $request)
    {
        $validator = Validator::make($request->all(), [ 'email' => 'required', 'message' => 'required']); /** Validation code */
        $tokenUserId = $request->user()->token()->user_id; /** Auth id */
        array_set($request,'user_id',$tokenUserId);
        return $validator->fails() ? $this->getResponse($this->blankObject,false,400,$validator->errors()->first()) : $this->getResponse($this->userSupport::create($request->all()),true,200,'Your details submitted successfully.');
    }

    /**
     * -------------------------------------------------------
     * | User Profile Detail                                 |
     * |                                                     |
     * | @param $request                                     |
     * | @return Response                                    |
     * -------------------------------------------------------
     */
    public function profileDetail(Request $request)
    {
        $query = $this->user::whereNotNull('email_verified_at')->whereIsVerify(1);
        if (isset($request->engineer_id) && $request->engineer_id != null && $request->engineer_id > 0) {
            $userObj = $query->whereId($request->engineer_id)->first();
            if ( isset($userObj) ) {
                $userArr = $this->userResponse($userObj, 'profile',$request->bearerToken()); /** Response code */
                $response = $this->getResponse($userArr,true,200,__('api_messages.user.profile_detail'));
            } else {
                $response = $this->getResponse($this->blankObject,false,405,__('api_messages.no_records_found'));
            }
        } else {
            $tokenUserId = $request->user()->token()->user_id; /** Auth id */
            $userObj = $query->whereId($tokenUserId)->first(); /** User object list */
            if ( isset($userObj) ) {
                $userArr = $this->userResponse($userObj, 'profile',$request->bearerToken()); /** Response code */
                $response = $this->getResponse($userArr,true,200,__('api_messages.user.profile_detail'));
            } else {
                $response = $this->getResponse($this->blankObject,false,405,__('api_messages.no_records_found'));
            }
        }
        return $response;
    }

    /**
     * -------------------------------------------------------
     * | Update Profile Detail                               |
     * |                                                     |
     * | @param $request                                     |
     * | @return Response                                    |
     * -------------------------------------------------------
     */
    public function updateProfile(Request $request) 
    {
        $tokenUserId = $request->user()->token()->user_id; /** Auth id */
        $rules = [
            'profile_pic' => 'image|mimes:jpeg,png,jpg',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,email,'.$tokenUserId.',id,deleted_at,NULL',
            'phone' => 'required',
            'company_id' => 'required|exists:companies,id',
            'position_id' => 'required',
            'user_type' => 'required',
        ]; /** Validation code */

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return  $this->getResponse($this->blankObject,false,400,$validator->errors()->first());
        } else {
            $userObj = $this->user::whereId($tokenUserId)->whereNotNull('email_verified_at')->whereIsVerify(1)->first();
            if ($userObj) {
                $this->dbStart();
                try {
                    if ($request->profile_pic && $request->profile_pic != null) {  
                        $profilePic = $request->file('profile_pic');
                        if (isset($profilePic) && !empty($profilePic)) {
                            $image_path = public_path('uploads/users/') . $userObj->profile_pic;
                            if(file_exists($image_path) && isset($userObj->profile_pic)) {
                                unlink($image_path);
                            }
                            $profileImag =  $this->uploadImage('uploads', 'users', $profilePic);
                            $userObj->profile_pic = $profileImag;
                        } else {
                            $userObj->profile_pic = $userObj->profile_pic;
                        }
                    }
                    $userObj->user_type = $request->user_type;
                    $userObj->first_name = $request->first_name;
                    $userObj->last_name = $request->last_name;
                    $userObj->email = $request->email;
                    $userObj->phone = $request->phone;
                    $userObj->company_id = $request->company_id;
                    $userObj->role_id = $request->position_id;
                    $userObj->save();
                    $userObj = $userObj->refresh();
                    $userArr = $this->userResponse($userObj, 'detail',$request->bearerToken());/** Response here */
                    $this->dbCommit();
                    return $this->getResponse($userArr,true,200,__('api_messages.user.your_profile_updated'));
                } catch (Exception $e) {
                    $this->dbEnd();
                    return $this->getResponse($jobObj,false,400, __('api_messages.somthing_went_wrong'));
                }
            } else {
                return $this->getResponse($this->blankObject,false,405,__('api_messages.no_records_found'));
            }
        }
    }

    /**
     * -------------------------------------------------------
     * | User  response                                      |
     * |                                                     |
     * | @param $request                                     |
     * | @return Response                                    |
     * -------------------------------------------------------
     */
    public function userResponse($userObj, $type,$token)
    {
        $userArr = replace_key($userObj->only($this->fillableCols), 'id' , 'user_id'); /** Rename the key name */
        $userArr['profile_pic'] = $userObj->profile_image;
        
        $role = $this->userRoleMaster::whereId($userObj->role_id)->first();
        $userArr['position']['position_id'] = $userObj->role_id;
        $userArr['position']['position_name'] = @$role->name;
        $userArr['company']['company_id'] = $userObj->company->id;
        $userArr['company']['company_name'] = $userObj->company->title;
        //$this->userDeviceTokens($userObj, $request); /** Insert fcm token & device type code*/
        /** Firebase data code start */
        $userArr['firebase_credential']['firebase_email'] = count($userObj->firebaseCredential) > 0 ? isset($userObj->firebaseCredential[0]->username) ? $userObj->firebaseCredential[0]->username : ""  : "";
        $userArr['firebase_credential']['firebase_password'] = count($userObj->firebaseCredential) > 0 ? isset($userObj->firebaseCredential[0]->password) ? $userObj->firebaseCredential[0]->password : "" : "";
        $userArr['firebase_credential']['firebase_uid'] = count($userObj->firebaseCredential) > 0 ? isset($userObj->firebaseCredential[0]->uid) ? $userObj->firebaseCredential[0]->uid : "" : "";
        $userArr['oauth_token'] = $token;
        /** Firebase data code end */
        $userArr = removeNullFromArray($userArr); /** Remove null value */
        return $userArr;
    }


    /**
    * -------------------------------------------------------
    * | refresh token |
    * | |
    * | @param $request |
    * | @return Response |
    * -------------------------------------------------------
    */
    public function refreshToken(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'device_id' => 'required',
            'fcm_token' => 'required',
        ]);
        
        if ($validator->fails()) {
            return $this->getResponse($this->blankObject,false,400,$validator->errors()->first());
        } else {
            // Get requested parameters
            $userId = $request->user()->token()->user_id;
            $deviceId = $request->device_id;
            $fcmToken = $request->fcm_token;
        
            //$tokenUserId = $request->user()->token()->user_id; // Get oauth token user id
            // Verify oauth token user with request user
            if ($userId) {
                $getUserObj = $this->user->where('id',$userId)->whereHas('usersFcmTokens')->with('usersFcmTokens')->first();
        
                $device = isset($getUserObj->usersFcmTokens[0]->device_id) && !empty($getUserObj->usersFcmTokens[0]->device_id) ? $getUserObj->usersFcmTokens[0]->device_id : "";
        
                if ($device == $deviceId) {
                    $update = UserDeviceToken::where('user_id',$userId)->update([ 'fcm_token' => $fcmToken]);
                } else {
                    $update = UserDeviceToken::where('user_id',$userId)->update(['fcm_token' => $fcmToken]);
                }
        
                return $update == 1 ? $this->getResponse($this->blankObject,true,200,'Token is updated.') : $this->getResponse($this->blankObject,true,400,'Token is not updated.');
            } else {
                return $this->getResponse($this->blankObject,false,403,'Please Login again');
            }
        }
    }
        
}
?>