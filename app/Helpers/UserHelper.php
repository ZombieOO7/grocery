<?php

namespace App\Helpers;

use App\Models\Job;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserHelper extends BaseHelper
{
    protected $user;
    public function __construct(Job $job, User $user)
    {
        $this->user = $user;
        $this->job = $job;
        parent::__construct();
    }

    /**
     * -----------------------------------------------------
     * | Find all users list                               |
     * |                                                   |
     * -----------------------------------------------------
     */
    public function getAllUsers()
    {
        return User::groupBy('id');
    }

    /**
     * -----------------------------------------------------
     * | Find user detail by id                            |
     * |                                                   |
     * | @param $id                                        |
     * -----------------------------------------------------
     */
    public function findUserById($id)
    {
        return User::find($id);
    }

    /**
     * -----------------------------------------------------
     * | Update status                                     |
     * |                                                   |
     * | @param $id,$status                                |
     * -----------------------------------------------------
     */
    public function updateStatusById($id, $status)
    {
        return User::where('id', $id)->update(['status' => $status]);
    }

    /**
     * -----------------------------------------------------
     * | Update Multiple status                            |
     * |                                                   |
     * | @param $ids,$status                               |
     * -----------------------------------------------------
     */
    public function updateMultipleStatus($ids, $status)
    {
        return User::whereIn('id', $ids)->update(['status' => $status]);
    }

    /**
     * -----------------------------------------------------
     * | Create or Update user                             |
     * |                                                   |
     * | @param $ids,$status                               |
     * -----------------------------------------------------
     */
    public function store($request)
    {
        $request->password = ($request->password == null)?$request->request->remove('password'):$request['password']=Hash::make($request->password);
        $request['email_verified_at'] = Carbon::now()->timestamp;
        $request['register_as_user'] = 0;
        $user = $this->findUserById($request->id);
        if($request->id == null){
            $request['register_as_user'] = 0;
        }else{
            $request['register_as_user'] = $user->register_as_user;
        }
        $user = $this->user::updateOrCreate(
            ['id' => @$request->id],
            $request->all()
        );
        if($request->id == null){
            $view = 'admin.user.__welcome';
            $subject = 'Welcome To '.env('APP_NAME');
            $this->sendMail($view, ['user'=>$user,'password'=>@$request->confirm_password], null, $subject, $user);
        }else{
            $this->updateProfileMail($user->id);
        }
        if ($request->hasFile('image')): $this->storeImage($request, $user);
        endif;
        return $user;
    }

    /**
     * ------------------------------------------------------
     * | Store image                                        |
     * |                                                    |
     * | @param $request,$user                              |
     * |-----------------------------------------------------
     */
    public function storeImage($request, $user)
    {
        $folderName = config('constant.user.folder_name');
        if ($request->id != null):
            $this->deleteImage($user->path, $folderName);
            $this->deleteImage($user->thumb_path, $folderName);
        endif;
        $avatarImage = $request->file('image');
        $mimeType = $request->file('image')->getMimeType();
        $extension = $request->file('image')->getClientOriginalExtension();
        $imageFunction = $this->uploadImage('uploads', $folderName, $avatarImage);
        $user = $this->user::updateOrCreate([
            'id' => $user->id,
        ], [
            'profile_pic' => @$imageFunction[0],
            'path' => config('constant.storage_path') . $folderName . '/' . @$imageFunction[0],
            'thumb_path' => null,
            'mime_type' => $mimeType,
            'extension' => $extension,
        ]);
        return $request;
    }

    /**
     * ------------------------------------------------------
     * | Verify user                                        |
     * |                                                    |
     * | @param $id,$status                                 |
     * |-----------------------------------------------------
     */
    public function verify($id, $status){
        $user = $this->findUserById($id);
        $user->update(['status'=>1]);
        $view = 'admin.user.__verifyMail';
        $subject = env('APP_NAME').' Verification';
        $this->sendMail($view, ['user'=>$user,'status'=>$status], null, $subject, $user);
    }

    /**
     * ------------------------------------------------------
     * | Update status                                      |
     * |                                                    |
     * | @param $uuid                                       |
     * |-----------------------------------------------------
     */
    public function statusUpdate($id)
    {
        $user = $this->findUserById($id);
        $status = $user->status == config('constant.status_active_value') ? config('constant.status_inactive_value') : config('constant.status_active_value');
        $action = ($status == 1)?__('admin/messages.active'):__('admin/messages.inactive');
        $this->user::where('id', $user->id)->update(['status' => $status]);
        $msg = ['msg'=>__('admin/messages.action_msg', ['action' => $action, 'type' => 'User']),'icon'=>'success'];
        $this->updateProfileMail($user->id);
        return $msg;
    }

    /**
     * ------------------------------------------------------
     * | Delete company                                     |
     * |                                                    |
     * | @param $uuid                                       |
     * |-----------------------------------------------------
     */
    public function delete($uuid)
    {
        $user = $this->findUserById($uuid);
        $this->deleteProfile($user->id);
        $user->delete();
    }

    /**
     * ---------------------------------------------------------------
     * | Delete multiple company                                     |
     * |                                                             |
     * | @param Request $request                                     |
     * | @return Void                                                |
     * ---------------------------------------------------------------
     */
    public function multiDelete(Request $request)
    {
        $user = $this->user::whereIn('id', $request->ids);
        if ($request->action == config('constant.delete')) {
            $this->deleteProfile($request->ids,'multiple');
            $user->delete();
        } else {
            $status = $request->action == config('constant.inactive') ? config('constant.status_inactive_value') : config('constant.status_active_value');
            $user->update(['status' => $status]);
            $this->updateProfileMail($request->ids,'multiple');
        }
    }

    /**
     * ---------------------------------------------------------------
     * | get job's of that own user                                  |
     * |                                                             |
     * | @param $id                                                  |
     * | @return array                                               |
     * ---------------------------------------------------------------
     */
    public function jobDetail($id){
        $detail = $this->job::whereAssignedTo($id)->get();
        return $detail;
    }

    /**
     * -----------------------------------------------------
     * | Find user detail by id                            |
     * |                                                   |
     * | @param $id                                        |
     * -----------------------------------------------------
     */
    public function findUserByUuid($id)
    {
        return User::whereUuid($id)->first();
    }

    /**
     * ---------------------------------------------------------------
     * | Change user position                                        |
     * |                                                             |
     * | @param Request $request                                     |
     * | @return resonse $msg                                        |
     * ---------------------------------------------------------------
     */
    public function changePosition($request){
        $user = $this->findUserByUuid($request->id);
        if($user){
            $user->update(['user_type' => $request->userType]);
            $this->updateProfileMail($user->id);
            $msg = ['msg'=>__('admin/messages.action_msg', ['action' => __('admin/messages.updated'), 'type' => 'User position']),'icon' => 'success'];
            return $msg;
        }
        $msg = ['msg'=>__('admin/messages.not_found'), 'icon' => 'info'];
        return $msg;
    }

    /**
     * ---------------------------------------------------------------
     * | get job's of that multiples user's                          |
     * |                                                             |
     * | @param $ids                                                  |
     * | @return array                                               |
     * ---------------------------------------------------------------
     */
    public function multiUserJobDetail($id){
        $detail = $this->job::whereIn('assigned_to',$id)->get();
        return $detail;
    }

    public function updateProfileMail($id,$type=null){
        $view = 'admin.user.__profile_update';
        $template = $this->getTemplate('update-profile');
        $subject = $template->subject ?? 'Profile updated by admin';
        if($type == 'multiple'){
            $users = $this->user::whereIn('id', $id)->get();
            foreach($users as $user){
                $keywords = [
                    '[USER FULL NAME]'=> $user->full_name_text,
                ];
                $content = str_replace(array_keys($keywords),array_values($keywords), $template->body);
                $this->sendMail($view, ['user'=>$user, 'content'=>@$content], null, $subject, $user);
            }
        }else{
            $user = $this->user::find($id);
            $keywords = [
                '[USER FULL NAME]'=> $user->full_name_text,
            ];
            $content = str_replace(array_keys($keywords),array_values($keywords), $template->body);
            $this->sendMail($view, ['user'=>$user, 'content'=>@$content], null, $subject, $user);
        }
    }

    public function deleteProfile($id,$type=null){
        $view = 'admin.user.__delete_profile';
        $template = $this->getTemplate('delete-profile');
        $subject = $template->subject ?? 'Profile deleted by admin';
        if($type == 'multiple'){
            $users = $this->user::whereIn('id', $id)->get();
            foreach ($users as $user) {
                $keywords = [
                    '[USER FULL NAME]'=> $user->full_name_text,
                ];
                $content = str_replace(array_keys($keywords),array_values($keywords), $template->body);
                $this->sendMail($view, ['user'=>$user, 'content'=>@$content], null, $subject, $user);
            }
        }else{
            $user = $this->user::find($id);
            $keywords = [
                '[USER FULL NAME]'=> $user->full_name_text,
            ];
            $content = str_replace(array_keys($keywords),array_values($keywords), $template->body);
            $this->sendMail($view, ['user'=>$user, 'content'=>@$content], null, $subject, $user);
        }
    }
}
