<?php

namespace App\Http\Controllers\API;

use App\Helpers\BaseHelper;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Http\Resources\UserResource;
use Validator;
use App\Models\User;
use App\Models\UserDeviceToken;
use App\Models\oAuthAccessTokens;
use App\Models\UserRoleMaster;
use Exception;

class LoginController extends BaseController
{
    public $successStatus = 200;
    protected $user;
    protected $userDeviceToken;
    protected $oAuthAccessTokens;
    protected $userRoleMaster;
    protected $helper;

    /**
     * -------------------------------------------------------
     * | Create a new controller instance.                   |
     * |                                                     |
     * -------------------------------------------------------
     */
    public function __construct(User $user, UserDeviceToken $userDeviceToken, oAuthAccessTokens $oAuthAccessTokens, BaseHelper $helper)
    {
        parent::__construct();
        $this->user = $user;
        $this->userDeviceToken = $userDeviceToken;
        $this->oAuthAccessTokens = $oAuthAccessTokens;
        $this->helper = $helper;
    }

    /**
     * -------------------------------------------------------
     * | Login function.                                     |
     * |                                                     |
     * | @param  object/array $request                       |
     * | @return response                                    |
     * -------------------------------------------------------
     */
    public function login(Request $request)
    {
        $this->helper->dbStart();
        try {
            $rules = [
                'phone' => 'required|numeric|min:10',
                'phone_code' => 'required|numeric|min:2',
                'otp' => 'required|numeric|min:4',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first(), 400);
            }
            $phone = strtolower(trim($request->phone));
            $phoneCode = strtolower(trim($request->phone_code));
            /** Check phone is exist in database */
            $userObj = $this->user
                ->where(['phone' => $phone, 'phone_code' => $phoneCode])
                ->first();
            if (!$userObj) {
                throw new Exception('invalid phone number', 400);
            }
            if($request->has('email')){
                $rules = ["email"=>"required|email|unique:users,email,{$userObj->id},id,deleted_at,NULL"];
            }
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                throw new Exception($validator->errors()->first(), 400);
            }
            $userObj->update($request->all());
            if ($userObj->status == null || $userObj->status == 0 || $userObj->deleted_at != null) {
                throw new Exception(__('api_messages.user_account_restricted'), 400);
            }
            $otpData = $this->userDeviceToken::where(['otp' => $request->otp, 'user_id' => $userObj->id])->first();
            if (!$otpData) {
                throw new Exception(__('api_messages.invalid_otp'), 400);
            }
            $token = $userObj->createToken(config('constant.oAuthAccessTokensName'))->accessToken;
            $otpData->update(['otp' => null, 'device_token' => $token]);
            $userArr = new UserResource($userObj);
            /** User response code*/
            $data = json_decode($userArr->toJson(), true);
            $this->helper->dbEnd();
            return $this->getResponse($data, true, 200, __('api_messages.login.login_success'), $token);
        } catch (Exception $error) {
            $this->helper->dbRollBack();
            return $this->getResponse($this->blankObject, false, $error->getCode() ?? 501, $error->getMessage());
        }
    }

    /**
     * -------------------------------------------------------
     * | Logout function.                                    |
     * |                                                     |
     * | @param  object/array $request                       |
     * | @return response                                    |
     * -------------------------------------------------------
     */
    public function logout(Request $request)
    {
        if (isset($request->fcm_token) && !empty($request->fcm_token)) {
            /** Remove fcm_token code start */
            $userData = $this->userDeviceToken::where('fcm_token', 'LIKE', '%' . $request->fcm_token . '%')->first();
            if (isset($userData)) {
                $authData = $this->oAuthAccessTokens->where('user_id', '=', $userData->user_id)->where('name', '=', config('constant.oAuthAccessTokensName'))->first();
                if ($authData) {
                    $authData->delete();
                    /** Logout the user from previous device */
                    $userData->forceDelete();
                    /** Delete previous device fcm token */
                }
            }
        }
        return $this->getResponse($this->blankObject, true, 200, __('api_messages.login.logout_msg'));
    }


    /**
     * -------------------------------------------------------
     * | Get User response                                   |
     * |                                                     |
     * | @param  object/array $request                       |
     * | @return response                                    |
     * -------------------------------------------------------
     */
    public function getUserResponse($request, $userObj)
    {
        $userArr = replace_key($userObj->only($this->fillableCols), 'id', 'user_id');
        /** Used to replace key name in array */
        $userArr['profile_pic'] = $userObj->profile_image;
        $userArr['position']['position_id'] = $userObj->role_id;
        $role = $this->userRoleMaster::whereId($userObj->role_id)->first();
        $userArr['position']['position_name'] = @$role->name;
        $userArr['company']['company_id'] = $userObj->company->id;
        $userArr['company']['company_name'] = $userObj->company->title;
        $this->userDeviceTokens($userObj, $request);
        /** Insert fcm token & device type code*/
        /** Firebase response code start */
        $userArr['firebase_credential']['firebase_email'] = count($userObj->firebaseCredential) > 0 ? $userObj->firebaseCredential[0]->username : "";
        $userArr['firebase_credential']['firebase_password'] = count($userObj->firebaseCredential) > 0 ? isset($userObj->firebaseCredential[0]->password) ? $userObj->firebaseCredential[0]->password : "" : "";
        $userArr['firebase_credential']['firebase_uid'] = count($userObj->firebaseCredential) > 0 ? isset($userObj->firebaseCredential[0]->uid) ? $userObj->firebaseCredential[0]->uid : "" : "";
        /** Firebase response code end */
        $removedNullArr = removeNullFromArray($userArr);
        /** To remove null value from array */
        return $removedNullArr;
    }

    /**
     * -------------------------------------------------------
     * | Login function.                                     |
     * |                                                     |
     * | @param  object/array $request                       |
     * | @return response                                    |
     * -------------------------------------------------------
     */
    public function sendOtp(Request $request)
    {
        $this->helper->dbStart();
        try {
            $validator = Validator::make($request->all(), [
                'phone' => 'required|numeric|min:10',
                'phone_code' => 'required|numeric|min:2',
                'device_id' => 'required',
                'device_type' => 'required|numeric|min:1',
            ]);
            $phone = strtolower(trim($request->phone));
            $phoneCode = strtolower(trim($request->phone_code));
            if ($validator->fails()) {
                throw  new Exception($validator->errors()->first(), 400);
            } else {
                /** Check phone number is exist in database */
                $userObj = $this->user->updateOrCreate([
                    'phone' => $phone,
                    'phone_code' => $phoneCode,
                ], $request->all());
                $isNewRegister = 0;
                if ($userObj->wasRecentlyCreated == true) {
                    $userObj->update(['status' => 1]);
                    $isNewRegister = 1;
                }
                if ($userObj->status == 0 || $userObj->deleted_at != null) {
                    throw new Exception(__('api_messages.user_account_restricted'), 400);
                }
                $request['otp'] = rand(1000, 9999);
                $phoneNumber = $userObj->phone;
                $this->sendPhoneOtp($request['otp'],$phoneNumber);
                $request['user_id'] = $userObj->id;
                $this->userDeviceToken->create($request->all());
                $response = [
                    'phone' => $userObj->phone_code . '' . $userObj->phone,
                    'otp' => $request->otp,
                    'is_new_register' => $isNewRegister
                ];
                $this->helper->dbEnd();
                return $this->getResponse($response, true, 200, __('api_messages.otp_send'));
            }
        } catch (Exception $error) {
            $this->helper->dbRollBack();
            return $this->getResponse($this->blankObject, false, $error->getCode() ?? 501, $error->getMessage());
        }
    }
    public function sendPhoneOtp($otp,$phoneNumber){
        try{
            $fields = array(
                "variables_values" => $otp,
                "route" => "otp",
                "numbers" => $phoneNumber,
            );
            $curl = curl_init();
            
            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://www.fast2sms.com/dev/bulkV2",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_SSL_VERIFYHOST => 0,
              CURLOPT_SSL_VERIFYPEER => 0,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => json_encode($fields),
              CURLOPT_HTTPHEADER => array(
                "authorization:bNLohtru2V5xJzcwvmeM0EdiF3I8QUAXsHjpZSa9GWk1yqn4KOLMeYtvq2Kp9Jx8IHzC3b1ToNR5hFaZ",
                "accept: */*",
                "cache-control: no-cache",
                "content-type: application/json"
              ),
            ));
            $response = json_decode(curl_exec($curl));
            $err = curl_error($curl);
            curl_close($curl);
            if ($err) {
                throw $err; 
            }
            if($response->return == false){
                throw New Exception($response->message,$response->status_code);
            }
        } catch (Exception $error) {
            throw $error;
        }
    }
}
