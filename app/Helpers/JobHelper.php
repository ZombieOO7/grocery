<?php

namespace App\Helpers;

use App\Events\UserNotificationEvent;
use App\Models\Job;
use App\Models\JobImage;
use App\Models\Machine;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class JobHelper extends BaseHelper
{
    protected $job,$machine;
    public function __construct(Job $job, JobImage $jobImage, Notification $notification, User $user, Machine $machine)
    {
        $this->job = $job;
        $this->jobImage = $jobImage;
        $this->notification = $notification;
        $this->user = $user;
        $this->machine = $machine;
        parent::__construct();
    }
    /**
     * ------------------------------------------------------
     * | Get job list                                       |
     * |                                                    |
     * |-----------------------------------------------------
     */
    public function list()
    {
        return $this->job::orderBy('id', 'desc');
    }

    /**
     * ------------------------------------------------------
     * | job detail by id                                   |
     * |                                                    |
     * | @param $id                                         |
     * |-----------------------------------------------------
     */
    public function detailById($id)
    {
        return $this->job::whereId($id)->first();
    }

    /**
     * ------------------------------------------------------
     * | job store                                          |
     * |                                                    |
     * | @param Request $request,$uuid                      |
     * |-----------------------------------------------------
     */
    public function store(Request $request, $uuid = null)
    {
        if ($request->has('id') && $request->id != '') {
            $job = $this->job::findOrFail($request->id);

            // if job status id change
            if($job->job_status_id  != $request->job_status_id){
                $notificationId= $this->getNotificationId($request->job_status_id);
                $this->jobStatusChangeEvent($notificationId,$job->id,$request->job_status_id);
            }
            // if reassign engineer
            if($job->assigned_to  != $request->assigned_to){
                $this->jobStatusChangeEvent(6,$job->id,$request->job_status_id);
            }
        } else {
            $job = new Job();
            $request['created_by'] = Auth::id();
            $request['job_status_id'] = 1;
        }
        $job->fill($request->all())->save();
        if (!$request->has('id') || $request->id == '') {
            $this->jobStatusChangeEvent(1,$job->id,$request->job_status_id);
            $this->jobStatusChangeEvent(4,$job->id,$request->job_status_id);

            /** Send new job request notification to managers starts here */
            $message = 'Admin has requested new job';
            $managersList = $this->user::whereUserType(1)->whereStatus(1)->whereNotNull('email_verified_at')->whereIsVerify(1)->whereNull('deleted_at')->get();
            if ($managersList->count() > 0) {
                foreach ($managersList as $manager) {
                    event(new UserNotificationEvent($manager->id, $job->id, get_class($this->job), $message, 1)); /** Event activity record */
                }
            }
            $this->sendPushNotificationToMultiple($job,$managersList,'job_added',$message,'Job requested');
        }

        // upload multiple job images
        if ($request->hasFile('image')) {
            if($request->id != null){
                $oldJobImages = $this->jobImage::whereJobId($request->id)->get();
                foreach ($oldJobImages as $oldJobImage) {
                    $this->deleteImage($oldJobImage->path, config('constant.job.folder_name'));
                    $oldJobImage->forceDelete();
                }
            }
            foreach($request->image as $image)
                $this->storeImage($request, $job, $image);
        }
        return $job;
    }

    public function sendPushNotificationToMultiple($notificationData,$userNotificationObj,$type,$message,$title) 
    {
        foreach($userNotificationObj as $userObjKey => $userObjVal) {
            if (@$userObjVal->usersFcmTokens[0]) {
                $userToken = $userObjVal->usersFcmTokens[0]['fcm_token'];
                $userDeviceType = $userObjVal->usersFcmTokens[0]['device_type'];
                $fcmData = [
                    'job_id' => (int) $notificationData->id,
                    'type' => $type,
                    'message' => $message,
                ];
                $this->basicPushNotification($title, $message, $fcmData, $userToken, $userDeviceType);
            }
        }
    }


    /**
     * ------------------------------------------------------
     * | Store image                                        |
     * |                                                    |
     * | @param $request,$user                              |
     * |-----------------------------------------------------
     */
    public function storeImage($request, $job, $image)
    {
        $folderName = config('constant.job.folder_name');
        $avatarImage = $image;
        $mimeType = $image->getMimeType();
        $extension = $image->getClientOriginalExtension();
        $imageFunction = $this->uploadImage('uploads', $folderName, $avatarImage);
        $jobImage = $this->jobImage::create([
            'job_id' => $job->id,
            'image' => @$imageFunction[0],
            'path' => config('constant.storage_path') . $folderName . '/' . @$imageFunction[0],
            'thumb_path' => null,
            'mime_type' => $mimeType,
            'extension' => $extension,
        ]);
        return $request;
    }

    /**
     * ------------------------------------------------------
     * | Update status                                      |
     * |                                                    |
     * | @param $uuid                                       |
     * |-----------------------------------------------------
     */
    public function statusUpdate($uuid)
    {
        $job = $this->detail($uuid);
        $status = $job->status == config('constant.status_active_value') ? config('constant.status_inactive_value') : config('constant.status_active_value');
        $action = ($status == 1)?__('admin/messages.active'):__('admin/messages.inactive');
        $this->job::where('id', $job->id)->update(['status' => $status]);
        $msg = __('admin/messages.action_msg', ['action' => $action, 'type' => 'Job']);
        return $msg;
    }

    /**
     * ------------------------------------------------------
     * | job detail by uuid                                 |
     * |                                                    |
     * | @param $uuid                                       |
     * |-----------------------------------------------------
     */
    public function detail($uuid)
    {
        return $this->job::whereUuid($uuid)->firstOrFail();
    }

    /**
     * ------------------------------------------------------
     * | Delete job                                         |
     * |                                                    |
     * | @param $uuid                                       |
     * |-----------------------------------------------------
     */
    public function delete($uuid)
    {
        $job = $this->detail($uuid);
        $job->delete();
    }

    /**
     * ---------------------------------------------------------------
     * | Delete multiple job                                         |
     * |                                                             |
     * | @param Request $request                                     |
     * | @return Void                                                |
     * ---------------------------------------------------------------
     */
    public function multiDelete(Request $request)
    {
        $job = $this->job::whereIn('id', $request->ids);
        if ($request->action == config('constant.delete')) {
            $job->delete();
        } else {
            $status = $request->action == config('constant.inactive') ? config('constant.status_inactive_value') : config('constant.status_active_value');
            $job->update(['status' => $status]);
        }
    }
    /**
     * ---------------------------------------------------------------
     * | Dispaly Job Images                                          |
     * |                                                             |
     * | @param Request $request                                     |
     * | @return array                                               |
     * ---------------------------------------------------------------
     */
    public function jobImages($id){
        $images = $this->jobImage::whereJobId($id)->get();
        return $images;
    }
    /**
     * ---------------------------------------------------------------
     * | Delete Job Images                                           |
     * |                                                             |
     * | @param Request $request                                     |
     * | @return array                                               |
     * ---------------------------------------------------------------
     */
    public function deleteJobImage($uuid){
        $image = $this->jobImage::whereUuid($uuid)->first();
        $image->delete();
        $this->deleteImage($image->path, config('constant.job.folder_name'));
    }

    /**
     * ---------------------------------------------------------------
     * | Change Job priority                                         |
     * |                                                             |
     * | @param Request $request                                     |
     * | @return resonse $msg                                        |
     * ---------------------------------------------------------------
     */
    public function changePriority($request){
        $job = $this->detail($request->id);
        if($job){
            $job->update(['priority' => $request->priority]);
            $msg = ['msg'=>__('admin/messages.action_msg', ['action' => __('admin/messages.updated'), 'type' => 'Job priority']),'icon' => 'success'];
            return $msg;
        }
        $msg = ['msg'=>__('admin/messages.not_found'), 'icon' => 'info'];
        return $msg;
    }

    /**
     * ---------------------------------------------------------------
     * | Change Job assign engineer                                  |
     * |                                                             |
     * | @param Request $request                                     |
     * | @return resonse $msg                                        |
     * ---------------------------------------------------------------
     */
    public function changeAssingTo($request){
        $job = $this->detail($request->id);
        if($job){
            $job->update(['assigned_to' => $request->assigned_to]);
            // if reassign engineer
            $this->jobStatusChangeEvent(6,$job->id,$request->job_status_id);
            $msg = ['msg'=>__('admin/messages.assign_success_msg'),'icon' => 'success'];
            return $msg;
        }
        $msg = ['msg'=>__('admin/messages.not_found'), 'icon' => 'info'];
        return $msg;
    }
    /**
     * ---------------------------------------------------------------
     * | Send users notification to admin                            |
     * |                                                             |
     * | @param Request $notificationId, $jobId, $jobStatusIs        |
     * | @return                                                     |
     * ---------------------------------------------------------------
     */
    public function jobStatusChangeEvent($notificationId=null,$jobId,$jobStatusId){
        return;
        $notification = $this->notification::find($notificationId);
        $message = $notification->notification_name;
        /** job declined send notification to operator starts here */
        $usersList = $this->user::whereNotNull('email_verified_at')->whereIsVerify(1)->whereNull('deleted_at')->get();
        if ($usersList->count() > 0) {
            foreach ($usersList as $user) {
                event(new UserNotificationEvent($user->id, $jobId, get_class($this->job), $message, $jobStatusId)); /** Event activity record */
            }
        }
        /** job declined send notification to operator ends here */
    }

    /**
     * ---------------------------------------------------------------
     * | Set notification id acoording to job status id              |
     * |                                                             |
     * | @param Request $notificationId, $jobId, $jobStatusIs        |
     * | @return                                                     |
     * ---------------------------------------------------------------
     */
    public function getNotificationId($statusId){
        switch ($statusId) {
            case 1:
                // Job Request = New Job Request
                $notificationId=1;
                break;
            case 2:
                // Assigned = Engineer Assigned
                $notificationId=4;
                break;
            case 3:
                // OnGoing = On Going
                $notificationId=9;
                break;
            case 4:
                // Completed = Job Completed
                $notificationId=2;
                break;
            case 5:
                // Declined = Job request declined
                $notificationId=7;
                break;
            case 6:
                // Kiv = Kiv
                $notificationId=8;
                break;
            default:
                // Job Request = New Job Request
                $notificationId=1;
        }
        return $notificationId;
    }

    public function machineByLocation($id){
        // dd($id);
        $machineList = $this->machine::whereNull('deleted_at')->whereLocationId($id)->orderBy('title','asc')->pluck('title','id');
        return @$machineList;
    }
}
