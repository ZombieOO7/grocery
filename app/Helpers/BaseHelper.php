<?php
namespace App\Helpers;

use App\Models\EmailTemplate;
use App\Models\WebSetting;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use DB;

class BaseHelper
{
    public $mode;
    public function __construct()
    {

    }
    /**
     * -------------------------------------------------------------
     * | DB transation start                                       |
     * |                                                           |
     * | @return Void                                              |
     * -------------------------------------------------------------
     */
    public function dbStart()
    {
        DB::beginTransaction();
    }

    /**
     * -------------------------------------------------------------
     * | DB transation end                                         |
     * |                                                           |
     * | @return Void                                              |
     * -------------------------------------------------------------
     */

    public function dbEnd()
    {
        DB::commit();
    }

    /**
     * -------------------------------------------------------------
     * | DB transation roll back                                   |
     * |                                                           |
     * | @return Void                                              |
     * -------------------------------------------------------------
     */
    public function dbRollBack()
    {
        DB::rollback();
    }
    /**
     * -------------------------------------------------------------
     * | Delete image                                              |
     * |                                                           |
     * | @param $imageName,$viewFolderName                         |
     * | @return Void                                              |
     * -------------------------------------------------------------
     */
    public function deleteImage($imageName, $viewFolderName)
    {
        $path = base_path($imageName);
        // $path = str_replace('public', '', $path);
        if (File::exists($path)) {
            File::delete($path);
        }
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
    public function uploadImage($mainFolderName, $folderName, $image)
    {
        if (!file_exists(base_path('public/' . $mainFolderName . '/' . $folderName))):
            @mkdir(base_path('public/' . $mainFolderName . '/' . $folderName), 0777, true);
        endif;
        if (@chmod(base_path('public/' . $mainFolderName . '/' . $folderName), 0777) && @chmod(base_path('public/' . $mainFolderName), 0777) && @chmod(base_path('public'), 0777)):
            @chmod(base_path('public'), 0777, true);@chmod(base_path('public/' . $mainFolderName), 0777, true);@chmod(base_path('public/' . $mainFolderName . '/' . $folderName), 0777, true);
        endif;

        $tempImageName = $image;
        $originalName = $image->getClientOriginalName();

        $new_name = rand() . '_' . $tempImageName->getClientOriginalName();
        $image->move(public_path($mainFolderName . '/' . $folderName), $new_name);
        $url = public_path($mainFolderName . '/' . $folderName);

        if (@chmod(base_path('public/' . $mainFolderName . '/' . $folderName . '/' . $new_name), 0777)):
            @chmod(base_path('public/' . $mainFolderName . '/' . $folderName . '/' . $new_name), 0777, true);
        endif;
        return [$new_name, $originalName];
    }

    /**
     * ------------------------------------------------------
     * | Send Mail to user                                  |
     * |                                                    |
     * |-----------------------------------------------------
     */
    public function sendMail($view, $data, $message = null, $subject, $userdata)
    {
        $setting = getWebSetting();
        if($setting && $setting->send_email == 1){
            Mail::send($view, $data, function ($message) use ($userdata, $subject) {
                $message->to($userdata->email)->subject($subject);
            });
        }
    }

    /**
     * ------------------------------------------------------
     * | Send Bulk Mail to users                            |
     * |                                                    |
     * |-----------------------------------------------------
     */
    public function bulkSendMail($view, $data, $message = null, $subject, $userdata, $emails){
        Mail::send($view, $data, function($message) use ($userdata,$emails,$subject)
        {
            $message->to($emails)->subject($subject);
        });
    }
    /**
     * -------------------------------------------------------
     * | Get email template data                             |
     * |                                                     |
     * | @return array                                       |
     * -------------------------------------------------------
     */
    public function getTemplate($slug){
        $template = EmailTemplate::whereSlug($slug)->first();
        return @$template;
    }

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

}
