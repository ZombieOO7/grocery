<?php

use App\Models\WebSetting;
use Illuminate\Support\Str;
use App\Models\AdminNotification;

/**
 * -------------------------------------------------------
 * | Date format for API.                                |
 * |                                                     |
 * | @param  object/array $request                       |
 * | @return data                                        |
 * -------------------------------------------------------
 */
function apiFormatDate($date)
{
    return $date ? $date->format(config('dateFormats.formates.API_DATE')) : '';
}

/**
 * -------------------------------------------------------
 * | Date format for API.                                |
 * |                                                     |
 * | @param  object/array $request                       |
 * | @return data                                        |
 * -------------------------------------------------------
 */
function apiACtFormatDate($date)
{
    return $date ? $date->format(config('dateFormats.formates.API_DATE_ACT')) : '';
}

/**
 * -------------------------------------------------------
 * | Date format                                         |
 * |                                                     |
 * | @param  object/array $request                       |
 * | @return data                                        |
 * -------------------------------------------------------
 */
function formatDate($date)
{
    return $date ? $date->format(config('dateFormats.formates.DATE')) : '';
}

/**
 * -------------------------------------------------------
 * | current date                                        |
 * |                                                     |
 * | @param  object/array $request                       |
 * | @return data                                        |
 * -------------------------------------------------------
 */
function getCurrentDate()
{
    return Carbon\Carbon::now();
}

/**
 * -------------------------------------------------------
 * | Date parse                                          |
 * |                                                     |
 * | @param  object/array $request                       |
 * | @return data                                        |
 * -------------------------------------------------------
 */
function parseDate($date, $dateFormat = null)
{
    $dateFormat = (isset($dateFormat)) ? $dateFormat : 'Y-m-d H:i:s';
    $dateObj = \Carbon\Carbon::parse($date);
    $formatedDate = $dateObj->format($dateFormat);

    return $formatedDate;
}

/**
 * -------------------------------------------------------
 * | String to json                                      |
 * |                                                     |
 * | @param  object/array $request                       |
 * | @return data                                        |
 * -------------------------------------------------------
 */
function stringToJson($string)
{
    return json_encode(json_decode($string));
}

/**
 * -------------------------------------------------------
 * | array to string                                     |
 * |                                                     |
 * | @param  object/array $request                       |
 * | @return data                                        |
 * -------------------------------------------------------
 */
function arrayToString($data)
{
    return implode(', ', $data);
}

/**
 * -------------------------------------------------------
 * | Calculate distance                                  |
 * |                                                     |
 * | @param  object/array $request                       |
 * | @return data                                        |
 * -------------------------------------------------------
 */
function calculateDistance($str1, $str2)
{
    $strDiff = levenshtein($str1, $str2);
    $threshold = 1 - $strDiff / 10;

    return $threshold;
}

/**
 * -------------------------------------------------------
 * | Get Fist word                                       |
 * |                                                     |
 * | @param  object/array $request                       |
 * | @return data                                        |
 * -------------------------------------------------------
 */
function getFirstWord(string $string)
{
    $result = strstr($string, ' ', true);

    return $result !== false ? $result : $string;
}

/**
 * -------------------------------------------------------
 * | Remove mull from array                              |
 * |                                                     |
 * | @param  object/array $request                       |
 * | @return data                                        |
 * -------------------------------------------------------
 */
function removeNullFromArray($arr)
{
    return $arr = array_map(function ($v) {
        return (is_null($v)) ? "" : $v;
    }, $arr);
}

/**
 * -------------------------------------------------------
 * | Validate valid url string                           |
 * |                                                     |
 * | @param  string $request                             |
 * | @return data                                        |
 * -------------------------------------------------------
 */
function validURL($string)
{
    if (filter_var($string, FILTER_VALIDATE_URL)) {
        return true;
    } else {
        return false;
    }
}

/**
 * -------------------------------------------------------
 * | Print data with pre tag                             |
 * |                                                     |
 * | @param  string $request                             |
 * | @return data                                        |
 * -------------------------------------------------------
 */
function pre($value)
{
    echo '<pre>';
    print_r($value);
    echo '</pre>';die;
}

/**
 * -------------------------------------------------------
 * | Dump data with pre tag                              |
 * |                                                     |
 * | @param  string $request                             |
 * | @return data                                        |
 * -------------------------------------------------------
 */
function dmp($value)
{
    echo '<pre>';
    var_dump($value);
    echo '</pre>';die;
}

/**
 * -------------------------------------------------------
 * | Short long content                                  |
 * |                                                     |
 * | @param  string $request                             |
 * | @return data                                        |
 * -------------------------------------------------------
 */
function shorter($input, $length)
{
    // No need to trim, already shorter than trim length
    if (strlen($input) <= $length) {
        return $input;
    }

    // Find last space within length
    $last_space = strrpos(substr($input, 0, $length), ' ');
    if (!$last_space) {
        $last_space = $length;
    }

    $trimmed_text = substr($input, 0, $last_space);

    // Add ellipses (...)
    $trimmed_text .= ' ...';

    return $trimmed_text;
}

/**
 * -------------------------------------------------------
 * | change array key name without changing order        |
 * |                                                     |
 * | @param  string $request                             |
 * | @return data                                        |
 * -------------------------------------------------------
 */
function replace_key($arr, $oldkey, $newkey)
{
    if (array_key_exists($oldkey, $arr)) {
        $keys = array_keys($arr);
        $keys[array_search($oldkey, $keys)] = $newkey;
        return array_combine($keys, $arr);
    }
    return $arr;
}

/**
 * -------------------------------------------------------
 * | change multiple array key name without              |
 * | changing order                                      |
 * |                                                     |
 * | @param  string $request                             |
 * | @return data                                        |
 * -------------------------------------------------------
 */
function recursive_change_key($arr, $set)
{
    if (is_array($arr) && is_array($set)) {
        $newArr = array();
        foreach ($arr as $k => $v) {
            $key = array_key_exists($k, $set) ? $set[$k] : $k;
            $newArr[$key] = is_array($v) ? recursive_change_key($v, $set) : $v;
        }
        return $newArr;
    }
    return $arr;
}

/**
 * -------------------------------------------------------
 * | Date format for API Time diff.                      |
 * |                                                     |
 * | @param  object/array $request                       |
 * | @return data                                        |
 * -------------------------------------------------------
 */
function apiTimeDiffFormatDate($date)
{
    return $date ? $date->format(config('dateFormats.formates.API_DATE_Time')) : '';
}

/**
 * -------------------------------------------------------
 * | Date format for API Time diff.                      |
 * |                                                     |
 * | @param  object/array $request                       |
 * | @return data                                        |
 * -------------------------------------------------------
 */
function apiReviewFormatDate($date)
{
    return $date ? $date->format(config('dateFormats.formates.API_REVIEW_DATE')) : '';
}

/**
 * -------------------------------------------------------
 * | Get admin notifications data                        |
 * |                                                     |
 * | @return array                                       |
 * -------------------------------------------------------
 */
function webNotificationData()
{
    $notifications = [];
    $datas = AdminNotification::whereRead(0)->orderBy('id', 'desc')->get();
    foreach ($datas as $key => $data) {
        $notifications[$key]['id'] = @$data->id;
        $notifications[$key]['content'] = @$data->content;
        $notifications[$key]['url'] = isset($data->url) ? URL::to('admin/' . $data->url) : '#';
        $notifications[$key]['time'] = isset($data->created_at) ? $data->created_at->diffForHumans() : '';
    }
    return @$notifications;
}

/**
 * -------------------------------------------------------
 * | Get settings data                                   |
 * |                                                     |
 * | @return array                                       |
 * -------------------------------------------------------
 */
function getWebSetting()
{
    $setting = WebSetting::first();
    return @$setting;
}

function monthList()
{
    $months = [
        '' => "Select Month",
        1 => "January",
        2 => "February",
        3 => "March",
        4 => "April",
        5 => "May",
        6 => "June",
        7 => "July",
        8 => "August",
        9 => "September",
        10 => "October",
        11 => "November",
        12 => "December",
    ];

    return $months;
}

function dateList(){
    $days = [];
    $days[''] = 'Select Date';
    for ($i = 1; $i <= 31; $i++) {
        if ($i <= 9) {
            $days[$i] = '0'.$i;
        } else {
            $days[] = $i;
        }
    }
    return $days;
}
/**
 * -------------------------------------------------------
 * | Convert date into timestamp                         |
 * |                                                     |
 * -------------------------------------------------------
 */
function timeStampConverter($date) 
{
    return strtotime($date)  * 1000 ;
}

function truncateString($title,$data){
    $result = Str::limit(@$data->title, 30);
    if(strlen(@$data->title) >= 30)
       $result =  @$result.'<a href="javascript:void(0);" class="shw-dsc" data-title="'.@$title.'"  data-description="'.@$data->title.'" data-toggle="modal" data-target="#DescModal">'.__('formname.read_more').'</a>';
    return @$result;
}
 function firebasetime(){
    date_default_timezone_set("Asia/Calcutta");
    $value = date("Y-m-d H:i:s");
    $timestamp = date('YmdHis', strtotime($value));
    return (int)$timestamp;
 }