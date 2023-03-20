<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Jobs\SingleEmailJob;
use App\Jobs\MultipleEmailJob;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\UserDeviceToken;
use DB;
use Image;
use Exception;
use App\Models\AdminNotification;
use App\Events\GetNotification;

class BaseController extends Controller
{

    public $actionCreate;

    public $actionSave;

    public $actionUpdate;

    public $actionNotFound;

    public $actionUpload;

    public $actionDelete;

    public $actionStatus;

    public $statusActive;

    public $statusInactive;

    public $statusDeleted;

    public $statusError;

    public $notChanged;

    public $getCurrentDate;
    public $methodNotFound;
    public $blankObject;

    public $passwordChange;
    public $passwordWrong;


    /**
     * -------------------------------------------------------
     * | Create a new controller instance.                   |
     * |                                                     |
     * -------------------------------------------------------
     */
    public function __construct()
    {
        /*
         * Action
         */
        $this->actionCreate = 'CREATE';
        $this->actionSave = 'SAVE';
        $this->actionUpdate = 'UPDATE';
        $this->actionNotFound = 'NOT_FOUND';
        $this->actionUpload = 'UPLOAD';
        $this->actionDelete = 'DELETE';
        $this->actionStatus = 'STATUS_CHANGED';
        $this->statusActive = 'ACTIVE';
        $this->statusInactive = 'INACTIVE';
        $this->statusDeleted = ['status' => 'deleted', 'deleted_at' => Carbon::now()];
        $this->statusError = 'ERROR';
        $this->notChanged = 'NOT_CHANGED';
        $this->getCurrentDate = Carbon::now();
        $this->methodNotFound = 'METHOD_NOT_FOUND';
        $this->blankObject = (object) [];
        $this->passwordChange = 'PASSWORD_CHANGED';
        $this->passwordWrong = 'PASSWORD_WRONG';
    }

    /**
    * -------------------------------------------------------
    * | Date format for API.                                |
    * |                                                     |
    * | @param  object/array $request                       |
    * | @return data                                        |
    * -------------------------------------------------------
    */
    public function basicPushNotification($title, $body, $data, $userToken, $userDeviceType)
    {
        if ($userDeviceType == 'ios') {
            $payloadArr = [
                "to" => $userToken,
                "notification" => [
                    "body" => $body,
                    "title" => $title,
                    "sound" => "default"
                ],
                "data" => $data,
            ];
        } else {
            $data['title'] = $title;
            $data['message'] = $body;
            $payloadArr = [
                "to" => $userToken,
                "data" => $data,
            ];
        }
        $jsonData = json_encode($payloadArr);

        $url = 'https://fcm.googleapis.com/fcm/send';
        $serverKey = env('FCM_SERVER_KEY');

        $headers = array(
            'Content-Type: application/json',
            'Authorization: key='.$serverKey
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        $result = curl_exec($ch);
        $line=date('Y-m-d G:i:s')."\n".json_encode($payloadArr)."\n".$result."\n";
          file_put_contents('public/uploads/notification_app.txt', $line.PHP_EOL , FILE_APPEND);
        if ($result === FALSE) {

        }
        curl_close($ch);
    }

    /**
     * -------------------------------------------------------
     * | Get current date.                                   |
     * |                                                     |
     * | @return date                                        |
     * -------------------------------------------------------
     */
    protected function getCurrentDate()
    {
        return Carbon::now();
    }

    /**
     * -------------------------------------------------------
     * | Create or Update User Device Token                  |
     * |                                                     |
     * | @param  object $userData                            |
     * | @param  array $request                              |
     * | @return response                                    |
     * -------------------------------------------------------
     */
    public function userDeviceTokens ($userData, $request) {
        $usersDeviceTokens = UserDeviceToken::updateOrCreate([
            'user_id' => $userData->id,
            'device_id' => $request->device_id,
            'device_type' => strtolower($request->device_type),
        ],[
            'user_id' => $userData->id,
            'device_type' => strtolower($request->device_type),
            'device_id' => $request->device_id,
            'fcm_token' => $request->fcm_token,
        ]);

        return $usersDeviceTokens;
    }


    /**
     * -------------------------------------------------------
     * | Response Structure                                  |
     * |                                                     |
     * | @param  object/array $response                      |
     * | @param  int $status                                 |
     * | @param  int $statusCode                             |
     * | @param  string $message                             |
     * | @param  object/array $oauthToken                    |
     * | @return data                                        |
     * -------------------------------------------------------
     */
    public function getResponse ($response, $status, $statusCode, $message, $oauthToken = null, $results = null, $lastPage = null) {
        $data['data'] = $response;
        if ($oauthToken != null) {
            $data['data']['oauth_token'] = $oauthToken;
        }
        if ($results != null && $lastPage != null) {
           $data['current_page'] = $results->currentPage();
           $data['last_page'] = $lastPage;
           $data['per_page'] = $results->perPage();
           $data['total_pages'] = ceil($results->total() / $results->perPage());
        }
        $data['status'] = $status;
        $data['status_code'] = $statusCode;
        $data['message'] = $message;
        return response()->json($data, 200);
    }

    /**
     * -------------------------------------------------------
     * | Upload Image Code                                   |
     * |                                                     |
     * | @param  string $mainFolderName                      |
     * | @param  string $folderName                          |
     * | @param  string $image                               |
     * | @return string                                      |
     * -------------------------------------------------------
     */
    public function uploadImage ($mainFolderName, $folderName , $image) 
    {
        if (!file_exists(base_path('public/'.$mainFolderName.'/'.$folderName ))):
            @mkdir(base_path('public/'.$mainFolderName.'/'.$folderName), 0777, true);
        endif;

        if (@chmod(base_path('public/'.$mainFolderName.'/'.$folderName), 0777) && @chmod(base_path('public/'.$mainFolderName), 0777) &&  @chmod(base_path('public'), 0777)):
            @chmod(base_path('public'), 0777 , true); @chmod(base_path('public/'.$mainFolderName), 0777 , true); @chmod(base_path('public/'.$mainFolderName.'/'.$folderName), 0777 , true);
        endif;

        $tempImageName = $image;

        $new_name = rand(). '_' . $tempImageName->getClientOriginalName();

        $image->move(public_path($mainFolderName.'/'.$folderName), $new_name);
        $url = public_path($mainFolderName.'/'.$folderName);

        if (@chmod(base_path('public/'.$mainFolderName.'/'.$folderName.'/'.$new_name), 0777)):
           @chmod(base_path('public/'.$mainFolderName.'/'.$folderName.'/'.$new_name), 0777 , true);
        endif;

        return $new_name;
    }

    /**
     * -------------------------------------------------------
     * | Send mail function                                  |
     * |                                                     |
     * | @param  object/array $dataObj                       |
     * | @param  string $file                                |
     * | @param  string $subject                             |
     * | @param  object/array $request                       |
     * | @return mail                                        |
     * -------------------------------------------------------
     */
    public function sendMail($emailData, $file, $subject, $request) 
    {
        Mail::send($file, $emailData, function ($message) use ($emailData, $subject, $request) {
            $message->subject($subject);
            $message->from(env('MAIL_FROM_ADDRESS'), env('APP_NAME'));
            $message->to($request);
        });
    }

    /**
     * -------------------------------------------------------
     * | Single Email Notification                           |
     * |                                                     |
     * | @return Response                                    |
     * -------------------------------------------------------
     */
    public function singleUserEmailNotifiaction($emailData, $sub, $users ,$file)
    {
        dispatch(new SingleEmailJob($emailData, $sub, $users ,$file))->delay(now()->addSeconds(30));
    }


    /**
     * -------------------------------------------------------
     * | Multiple Email Notification                         |
     * |                                                     |
     * | @return Response                                    |
     * -------------------------------------------------------
     */
    public function multipleUserEmailNotifiaction($emailData, $sub, $users , $file)
    {
        dispatch(new MultipleEmailJob($emailData, $sub, $users ,$file))->delay(now()->addSeconds(30));
    }

    /**
     * -------------------------------------------------------
     * | Default uuid generate.                              |
     * |                                                     |
     * | @return String                                      |
     * -------------------------------------------------------
     */
    protected function getUuid()
    {
        return (string) Str::uuid();
    }


    /**
     * -------------------------------------------------------
     * | Begine Transaction.                                 |
     * |                                                     |
     * -------------------------------------------------------
     */
    public function dbStart()
    {
        return DB::beginTransaction();
    }

    /**
     * -------------------------------------------------------
     * | Commit Transaction.                                 |
     * |                                                     |
     * -------------------------------------------------------
     */
    public function dbCommit()
    {
        return DB::commit();
    }

    /**
     * -------------------------------------------------------
     * | RollBack Transaction.                               |
     * |                                                     |
     * -------------------------------------------------------
     */
    public function dbEnd()
    {
        return DB::rollback();
    }

    /**
     * -------------------------------------------------------
     * | Send Notifications To Admin                         |
     * |                                                     |
     * | @param $request                                     |
     * | @return Response                                    |
     * -------------------------------------------------------
     */
    public function sendNotificationToAdmin($userId,$content,$jobId = null,$url = null) 
    {
        // $notification = new AdminNotification();
        // $notification->user_id = $userId;
        // $notification->content = $content;
        // $notification->url = $url;
        // if ($jobId != null) {
        //     $notification->job_id = $jobId;
        // }
        // $notification->save();
        // $notifications = $this->getNotification();
        // $totalNotification = count($notifications);
        // return event(new GetNotification($totalNotification,$notifications));
    }

    /**
     * -------------------------------------------------------
     * | Get Notifications data                              |
     * |                                                     |
     * | @return array                                       |
     * -------------------------------------------------------
     */
    public function getNotification()
    {
        $notifications = [];
        $datas = AdminNotification::whereRead(0)->orderBy('id','desc')->get();
        foreach ($datas as $key => $data) {
            $notifications[$key]['content'] = $data->content;
            $notifications[$key]['time'] = date('Y-m-d',strtotime($data->created_at));
        }
        return $notifications;
    }

}
