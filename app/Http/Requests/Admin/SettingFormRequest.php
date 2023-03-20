<?php

namespace App\Http\Requests\Admin;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;

class SettingFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        $validator = [
            'send_email' =>'required',
            'notify' => 'required',
        ];
        return $validator;
        /* if($this->active_tab == 'general_tab'){
            return [
                'logo' => 'nullable|mimes:jpeg,png,jpg|max:2048',
                'favicon' => 'nullable|mimes:svg,png,ico|max:2048',
            ];
        }
        if($this->active_tab == 'social_media_tab'){
            return [
                'facebook_url' => 'nullable|max:150',
                'youtube_url' => 'nullable|max:150',
                'twitter_url' => 'nullable|max:150',
                'google_url' =>  'nullable|max:150',
            ];
        }
        if($this->active_tab == 'meta_tab')
        {
            return [
                'meta_keywords'=>'nullable|max:150',
                'meta_description'=>'nullable|max:300',
            ];
        }

        if($this->active_tab == 'push_notification_tab')
        {
            return [
                'android_app_icon' => 'nullable|mimes:jpeg,png,jpg|max:2048',
                'ios_app_icon' => 'nullable|mimes:jpeg,png,jpg|max:2048',
                'ios_fcm_key'=> 'nullable',
                'android_fcm_key'=> 'nullable',
            ];
        } */
    }
}
