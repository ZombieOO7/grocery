<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use View;
use App\Models\Company;
use App\Models\Job;
use App\Models\JobStatus;
use App\Models\Location;
use App\Models\Machine;
use App\Models\Notification;
use App\Models\Problem;
use App\Models\User;

class BaseController extends Controller
{
    public $blankObject;

    public function __construct()
    {
        $this->blankObject = (object) [];
    }

    /**
     * Show status dropdown
     * @return Array
     */
    public function statusList()
    {
        return [
            '' => 'Select Status',
            'Active' => 'Active',
            'Inactive' => 'Inactive',
        ];
    }
    /**
     * add select option in dropdown
     * @return Array
     */
    public function mergeSelectOption($a,$type)
    {
        return  ['' => __('formname.select_type',['type'=>@$type])]+$a;
    }

    /**
     * Show order dropdown
     * @return Array
     */
    public function userTypeList()
    {
        return $this->mergeSelectOption(config('constant.user_types'),'position');
    }

    /**
     * partial view
     * @return View
     */
    public function getPartials($blade,$review){
        return View::make($blade, $review)->render();
    }

    /**
     * Show Enginner dropdown
     * @return Array
     */
    public function engineerList(){
        $user = User::active()->notDelete()->whereUserType('2')->get()->pluck('full_name_with_email_text','id');
        return $this->mergeSelectOption($user->toArray(),'staff');
    }

    /**
     * Show job status list dropdown
     * @return Array
     */
    public function jobStatusList()
    {
        return $this->mergeSelectOption(config('constant.job_status_text'),'job status');
    }

    /**
     * Show job priority list dropdown
     * @return Array
     */
    public function jobPriorityList()
    {
        return $this->mergeSelectOption(config('constant.priorites_text'),'job priority');
    }

    /**
     * Show status dropdown
     * @return Array
     */
    public function properStatusList()
    {
        return $this->mergeSelectOption([
            0 => 'Inactive',
            1 => 'Active',
        ],'status');
    }

    /**
     * Show status dropdown
     * @return Array
     */
    public function permissionStatusList()
    {
        return $this->mergeSelectOption(config('constant.permission_status'),'Permission status');
    }

    /**
     * Show status dropdown
     * @return Array
     */
    public function permissionStatus()
    {
        return $this->combineValues(config('constant.permission_status'),'Permission status');
    }

    /**
     * combile key and value
     * @return Array
     */
    public function combineValues($a,$type)
    {
        return  ['' => __('formname.select_type',['type'=>@$type])]+array_combine($a,$a);
    }

    /**
     * get registerd users list
     * which not verified by admin
     * @return Array
     */
    public function usersList(){
        $user = User::notDelete()
                ->orderBy('id','desc');
        return @$user;
    }

    public function reportType(){
        return $this->mergeSelectOption(config('constant.report_type'),'Report Type');
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

    /**
     * Show Enginner dropdown
     * @return Array
     */
    public function allEngineerList(){
        $user = User::whereNull('deleted_at')->whereNotNull('email_verified_at')->whereUserType('2')->get()->pluck('full_name_with_email_text','id');
        return $this->mergeSelectOption($user->toArray(),'staff');
    }

    /**
     * get registerd users list
     * which not verified by admin
     * @return Array
     */
    public function categoryList(){
        $user = Category::orderBy('id','desc')->pluck('title','id');
        return @$user;
    }

    /**
     * Show status dropdown
     * @return Array
     */
    public function stockStatusList()
    {
        return $this->mergeSelectOption([
            0 => 'Out Of Sock',
            1 => 'Available',
        ],'stock status');
    }
}
?>