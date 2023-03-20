<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingFormRequest;
use App\Models\WebSetting;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public $constant, $activetab;
    public function __construct()
    {
        $this->activetab = config('constant')['websetting']['active_tab'];

    }
    public function index(Request $request)
    {
        $settings = [];
        $settings = WebSetting::first();
        $data['setting'] = $settings;
        return view('admin.setting.index', $data);
    }
    public function store(SettingFormRequest $request, $id = null)
    {
        try {
            // $inputs = Input::except('_token', 'id');
            $currentPath = Route::currentRouteName();
            $settings = WebSetting::firstOrNew(['id' => $id]);

            if ($request->hasFile('logo')) {
                $logo = $request->file('logo');
                $request->logo = $this->fileupload($logo, ($settings->logo) ? $settings->logo : null);
            }
            if ($request->hasFile('favicon')) {
                $favicon = $request->file('favicon');
                $request->favicon = $this->fileupload($favicon, ($settings->logo) ? $settings->favicon : null);
            }

            // Store Android App Icon
            if ($request->hasFile('android_app_icon')) { // Check if request has file
                $androidAppIcon = $request->file('android_app_icon');
                $request->android_app_icon = $this->fileupload($androidAppIcon, ($settings->android_app_icon) ? $settings->android_app_icon : null);
            }

            // Store Ios App Icon
            if ($request->hasFile('ios_app_icon')) { // Check if request has file
                $iosAppIcon = $request->file('ios_app_icon');
                $request->ios_app_icon = $this->fileupload($iosAppIcon, ($settings->android_app_icon) ? $settings->android_app_icon : null);
            }

            $settings->fill($request->all())->save();
            $msg = __('admin/messages.action_msg', ['action' => __('admin/messages.updated'), 'type' => 'Settings']);
            return Redirect::route('web_setting_index')->with(['message'=>@$msg,'active_tab'=>@$request->active_tab]);
        } catch (Exception $exception) {
            return back()->withError($exception->getMessage())->withInput();
        }
    }
    public function fileupload($file, $name = null)
    {
        if ($name != null) {
            Storage::disk('local')->delete('websetting/' . $name);
        }
        $filename = time() . '_' . $file->getClientOriginalName();
        Storage::disk('local')->putFileAs('websetting/', $file, $filename);
        return $filename;
    }
}
