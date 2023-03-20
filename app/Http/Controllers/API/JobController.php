<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Validation\Rule;
use Hash;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use App\Models\Company;
use App\Models\Machine;
use App\Models\Location;
use App\Models\Problem;
use App\Models\User;
use App\Models\Job;
use App\Models\JobImage;
use App\Models\JobLog;
use App\Events\UserNotificationEvent;
use App\Models\Admin;
use App\Models\UserNotification;

class JobController extends BaseController
{
    public $successStatus = 200;
    protected $machine;
    protected $location;
    protected $problem;
    protected $user;
    protected $job;
    protected $jobLog; 
    protected $jobImage;

    /**
     * -------------------------------------------------------
     * | Create a new controller instance.                   |
     * |                                                     |
     * -------------------------------------------------------
     */
    public function __construct(Machine $machine,Problem $problem,Location $location,User $user,Job $job,JobImage $jobImage,JobLog $jobLog)
    {
        parent::__construct();
        $this->machine = $machine;
        $this->location = $location;
        $this->problem = $problem;
        $this->user = $user;
        $this->job = $job;
        $this->jobImage = $jobImage;
        $this->jobLog = $jobLog;
    }

    /**
     * -------------------------------------------------------
     * | Add new job.                                        |
     * |                                                     |
     * | @param Request $request                             |
     * | @return Response                                    |
     * -------------------------------------------------------
     */
    public function add(Request $request) 
    {
        $rules = [  'machine_id' => 'required|exists:machines,id', 'location_id' => 'required|exists:locations,id', 'problem_id' => 'required|exists:problems,id', 'image.*' => 'image|mimes:jpeg,png,jpg|max:5120' ];
        $messages = ['image.*.max' => __('api_messages.image_size_msg')];
        $validator = Validator::make($request->all(), $rules,$messages); /** Validation code */

        if ( $validator->fails() ) {
            return $this->getResponse($this->blankObject,false,400,$validator->errors()->first());
        } else {
            $tokenUserId = $request->user()->token()->user_id;
            array_set($request,'user_id',$tokenUserId);
            array_set($request,'job_status_id',1);
            $this->dbStart();
            try {
                $jobObj = $this->job::create($request->all());
                $jobObj['machine_id'] = (int) $jobObj->machine_id;
                $jobObj['problem_id'] = (int) $jobObj->problem_id;
                $jobObj['location_id'] = (int) $jobObj->location_id;
                $jobObj['job_id'] = $jobObj->id;
                /** Upload job images */
                $jobsImageArr = $request->file('image');
                $jobsFolder = 'jobs';
                $jobsImg = [];
                if (isset($jobsImageArr) && !empty($jobsImageArr)) {
                    foreach ($jobsImageArr as $jobsImage) {
                        $jobsImg = $this->uploadImage('uploads', $jobsFolder, $jobsImage);
                        $this->jobImage::create([ 'job_id' => $jobObj->id, 'image' => $jobsImg ]);
                    }
                }
                $jobObj['job_status'] = $jobObj->job_status_id;
                $jobId = $jobObj['id'];
                unset($jobObj['id']);
                unset($jobObj['job_status_id']);
                $user = $this->user::whereId($tokenUserId)->first();
                $url = 'job/detail/'.$jobObj['uuid'];
                $message = $user->full_name_text.' has requested new job';
                $this->sendNotificationToAdmin($tokenUserId,$message,$jobId,$url); /*Notifiy Admin */

                /** Send new job request notification to managers starts here */
                $managersList = $this->user::where('id','<>',$tokenUserId)->whereUserType(1)->whereStatus(1)->whereNotNull('email_verified_at')->whereIsVerify(1)->whereNull('deleted_at')->get();
                if ($managersList->count() > 0) {
                    foreach ($managersList as $manager) {
                        event(new UserNotificationEvent($manager->id, $jobId, get_class($this->job), $message, 1)); /** Event activity record */
                    }
                }
                /** Send new job request notification to managers ends here */
                $job = Job::whereId($jobId)->first();
                $this->sendPushNotificationToMultiple($job,$managersList,'job_added',$message,'Job requested');
                $this->dbCommit();
                return $this->getResponse($this->blankObject,true,200, __('api_messages.job.added_msg'));
            } catch (Exception $e) {
                $this->dbEnd();
                return $this->getResponse($jobObj,false,400, __('api_messages.somthing_went_wrong'));
            }
        }
    }

    /**
     * -------------------------------------------------------
     * | Edit job.                                           |
     * |                                                     |
     * | @param Request $request                             |
     * | @return Response                                    |
     * -------------------------------------------------------
     */
    public function edit(Request $request) 
    {
        $rules = [ 'job_id'=>'required|integer','priority' => 'required','engineer_id' => 'required' ]; /** Validation code */
        
        
        $validator = Validator::make($request->all(), $rules); /** Validation code */

        if ( $validator->fails() ) {
            return $this->getResponse($this->blankObject,false,400,$validator->errors()->first());
        } else {
            $tokenUserId = $request->user()->token()->user_id;
            $jobObjData = $this->job::whereId($request->job_id)->whereStatus(config('constant.status_active_value'))->whereNull('deleted_at')->first();
            if ($jobObjData) {
                $this->dbStart();
                try {
                    // /** Upload job images */
                    // $jobsImageArr = $request->file('image');
                    // $jobsFolder = 'jobs';
                    // $jobsImg = [];
                    // if (isset($jobsImageArr) && !empty($jobsImageArr)) {
                    //     foreach ($jobObjData->jobImage as $imgObj) {
                    //         $image_path = public_path('uploads/jobs/') . $imgObj->image;
                    //         if(file_exists($image_path) && isset($imgObj->image)) {
                    //             unlink($image_path);
                    //         }
                    //     }
                    //     $jobObjData->jobImage()->forceDelete();
                    //     foreach ($jobsImageArr as $jobsImage) {
                    //         $jobsImg = $this->uploadImage('uploads', $jobsFolder, $jobsImage);
                    //         $this->jobImage::create([ 'job_id' => $jobObjData->id, 'image' => $jobsImg ]);
                    //     }
                    // }
                    if ( $jobObjData->assigned_to != $request->engineer_id ) { /** Check if new engineer assigned or not */
                        $removedEngineer = $jobObjData->assigned_to;
                        $assignedEngineer = $request->engineer_id;
                        $message = 'Job reassigned to new engineer';

                        /** Reassign engineer send notification to reassigned engineer & removed engineer starts here */
                        if ($removedEngineer != null) {
                            event(new UserNotificationEvent($removedEngineer, $jobObjData->id, get_class($this->job), $message, 6)); /** Event activity record */

                            $this->sendPushNotificationToSingle($jobObjData,$this->user::find($removedEngineer),'engineer_reassigned','You have been removed from job','Staff reassigned');
                        }
                        
                        event(new UserNotificationEvent($assignedEngineer, $jobObjData->id, get_class($this->job), $message, 6)); /** Event activity record */

                        
                        $this->sendPushNotificationToSingle($jobObjData,$this->user::find($assignedEngineer),'engineer_reassigned','Job reassigned to you','Staff reassigned');

                        /** Reassign engineer send notification to reassigned engineer & removed engineer ends here */
                        
                        /** Reassign engineer send notification to operator starts here */
                        $usersList = $this->user::whereId($jobObjData->user_id)->get();
                        
                        if ($usersList->count() > 0) {
                            foreach ($usersList as $user) {
                                event(new UserNotificationEvent($user->id, $jobObjData->id, get_class($this->job), $message, 6)); /** Event activity record */
                            }
                            $this->sendPushNotificationToMultiple($jobObjData,$usersList,'engineer_reassigned','Job reassigned to new engineer','Staff reassigned');
                        }
                        /** Reassign engineer send notification to operator ends here */   
                    }
                    $jobObjData->assigned_to = $request->engineer_id;
                    $jobObjData->update($request->all());
                    unset($jobObjData['id']);
                    $this->dbCommit();
                    return $this->getResponse($this->blankObject,true,200, 'Priorty changed & staff assigend successfully');
                } catch (Exception $e) {
                    $this->dbEnd();
                    return $this->getResponse($jobObj,false,400, __('api_messages.somthing_went_wrong'));
                }
            } else {
                return $this->getResponse($this->blankObject,false,400,__('api_messages.job.job_not_found'));
            }
        }
    }

    /**
     * -------------------------------------------------------
     * | Job detail.                                         |
     * |                                                     |
     * | @param Request $request                             |
     * | @return Response                                    |   
     * -------------------------------------------------------
     */
    public function detail(Request $request) 
    {
        $validator = Validator::make($request->all(), [ 'job_id' => 'required|exists:jobs,id' ]); /** Validation code */
        if ( $validator->fails() ) {
            return $this->getResponse($this->blankObject,false,400,$validator->errors()->first());
        } else {
            $jobDetail = $this->job::whereId($request->job_id)->whereDeletedAt(NULL)->whereStatus(1)->first();
            if ($jobDetail) {
                $jobArr['job_id'] = $jobDetail->id;
                // $jobArr['title'] = $jobDetail->title;
                // $jobArr['description'] = $jobDetail->description;
                $jobArr['job_status'] = $jobDetail->job_status_id;
                $jobArr['job_duration'] = @$jobDetail->total_job_duration != null ? $jobDetail->total_job_duration : "00:00:00";
                $jobArr['priority'] = $jobDetail->priority;
                $jobArr['images'] = $this->getJobImages($jobDetail);
                $jobArr['machine_name'] = $jobDetail->machine->title;
                $jobArr['location_name'] = $jobDetail->location->title;
                $jobArr['problem_name'] = $jobDetail->problem->title;
                $jobArr['comment'] = $jobDetail->comment;
                $jobArr['engineer_id'] = @$jobDetail->assign ? $jobDetail->assign->id : '';
                $jobArr['engineer_name'] = @$jobDetail->assign ? $jobDetail->assign->full_name : '';
                $jobArr['created_user_name'] = @$jobDetail->user->full_name ? $jobDetail->user->full_name : 'Admin';
                $jobArr['created_time'] = timeStampConverter($jobDetail->created_at);
                $jobArr['job_start_time'] = $jobDetail->jobLogs()->latest()->first() ? timeStampConverter($jobDetail->jobLogs()->latest()->first()->start_time) : "";
                //$jobArr['engineer_comment'] = @$jobDetail->incomplete_reason;
                $jobArr['decline_reason'] = @$jobDetail->reason;
                $jobArr['incomplete_reason'] = @$jobDetail->incomplete_reason;
                $jobArr['declined_by'] = @$jobDetail->declined_by;
                if ($jobDetail->declined_by != null) {
                    // if ($jobDetail->declined_by == "Admin") {
                    //     $user = Admin::find($jobDetail->declined_by_user);
                    //     $jobArr['declined_by_user'] = $user->first_name.' '.$user->last_name;
                    // } else {
                        $user = User::find($jobDetail->declined_by_user);
                        $jobArr['declined_by_user'] = $user->first_name.' '.$user->last_name;
                    //}
                } else {
                    $jobArr['declined_by_user'] = "";
                }

                // Update All notifications counts to read
                $tokenUserId = $request->user()->token()->user_id;
                $notificationList = UserNotification::whereUserId($tokenUserId)->whereModelId($jobDetail->id)->get();
                if ($notificationList->count() > 0) {
                    foreach($notificationList as $val) {
                        $n = UserNotification::find($val->id);
                        $n->update(['is_read' => 1]);
                    }
                }
                $jobDetail->update(['is_read' => 1]);
                $removedNullArr = removeNullFromArray($jobArr); /** To remove null value from array */
                return $this->getResponse($removedNullArr,true,200,__('api_messages.job.job_details'));
            } else {
                return $this->getResponse($this->blankObject,false,400,'Job is deleted or inactivated by admin');
            }
        }
    }

    /**
     * -------------------------------------------------------
     * | All Job List.                                       |
     * |                                                     |
     * | @param Request $request                             |
     * | @return Response                                    |
     * -------------------------------------------------------
     */
    public function allJobs(Request $request) 
    {
        $validator = Validator::make($request->all(), [ 'page_number' => 'required|integer|min:1' ]); /** Validation code */
        if ( $validator->fails() ) {
            return $this->getResponse($this->blankObject,false,400,$validator->errors()->first());
        } else {
            if ( isset($request->engineer_id) && $request->engineer_id != null) {
                $tokenUserId = $request->engineer_id;
            } else {
                $tokenUserId = $request->user()->token()->user_id;
            }
            
            $loggedInUser = $this->user::whereId($tokenUserId)->first();

            $currentPage = trim($request->page_number); // You can set this to any page you want to paginate to

            // Make sure that you call the static method currentPageResolver() before querying users
            Paginator::currentPageResolver(function () use ($currentPage) {
                return $currentPage;
            });

            $query = $this->job::whereStatus(config('constant.status_active_value'))->whereNull('deleted_at');
            if ( $loggedInUser->user_type == config('constant.manager') ) { /** Check if manager or not */
                $jobObjData = $query;
            } else if ( $loggedInUser->user_type == config('constant.engineer') ) {
                $jobObjData = $query->whereAssignedTo($tokenUserId);
            } else {
                $jobObjData = $query->whereUserId($tokenUserId);
            }
            
            $jobObjData = $query->orderBy('created_at', 'DESC');
            $jobObj = $jobObjData->paginate(config('constant.job_page_limit')); /** Pagination */
            $jobResults = $jobObj->toArray();
            $jobFinalArr = [];

            foreach ($jobObj as $jobObjKey => $jobObjVal) { /** Used to display all faq */
                $jobArr['job_id'] = $jobObjVal->id;
                $jobArr['title'] = $jobObjVal->title;
                $jobArr['description'] = $jobObjVal->description;
                $jobArr['job_status'] = $jobObjVal->job_status_id;
                $jobArr['priority'] = $jobObjVal->priority;
                $jobArr['images'] = $this->getIndexJobImage($jobObjVal);
                $jobArr['created_at'] = timeStampConverter($jobObjVal->created_at);
                $removedNullArr = removeNullFromArray($jobArr); /** To remove null value from array */
                array_push($jobFinalArr, $removedNullArr);
            }

            $jobArre['all_jobs_list'] = $jobFinalArr;
            $lastPage = $jobObj->lastPage(); /** Last page of record */
            if ($currentPage > $lastPage) { /** Check if this is last page or not */
                return $this->getResponse($this->blankObject,false,405,__('api_messages.no_records_found'),'',$jobObj,$lastPage);
            } else {
                return count($jobFinalArr) > 0 ? $this->getResponse($jobArre,true,200,__('api_messages.job.all_jobs_list'),'',$jobObj,$lastPage) : $this->getResponse($this->blankObject,false,405,__('api_messages.no_records_found'),'',$jobObj,$lastPage);
            }
        }
    }

    /**
     * -------------------------------------------------------
     * | Job List.                                           | 
     * |                                                     |
     * | @param Request $request                             |
     * | @return Response                                    |
     * -------------------------------------------------------
     */
    public function jobByStatus(Request $request) 
    {
        $validator = Validator::make($request->all(), [ 'page_number' => 'required|integer|min:1', 'job_status' => 'required','filter_by_priority' => 'required|integer' ]); /** Validation code */
        if ( $validator->fails() ) {
            return $this->getResponse($this->blankObject,false,400,$validator->errors()->first());
        } else {
            if (!in_array($request->filter_by_priority,[0,1,2,3,4])) { /** Check if given priority exists or not */
                return $this->getResponse($this->blankObject,false,400, __('api_messages.job.invalid_priority'));
            } else {
                if ( isset($request->engineer_id) && $request->engineer_id != null && $request->engineer_id != 0) {
                    $tokenUserId = $request->engineer_id;
                } else {
                    $tokenUserId = $request->user()->token()->user_id;
                }
                $currentPage = trim($request->page_number); // You can set this to any page you want to paginate to

                // Make sure that you call the static method currentPageResolver() before querying users
                Paginator::currentPageResolver(function () use ($currentPage) {
                    return $currentPage;
                });
                $loggedInUser = $this->user::whereId($tokenUserId)->first();

                if ($loggedInUser->user_type != config('constant.engineer')) {
                    if ($loggedInUser->user_type == config('constant.operator') && $request->job_status == 1) { // Check if operator
                        $newStatus = $this->job::whereIn('job_status_id',[1,2,5,6,7]);
                    } else if ($request->job_status == 1) { // Job request and assigned status
                        $newStatus = $this->job::whereIn('job_status_id',[1,2,5,7]);
                    } //else if ($request->job_status == 5) { // Unable to complete & decline jobs
                        //$newStatus = $this->job::whereIn('job_status_id',[5,7]);
                    //} 
                    else {
                        $newStatus = $this->job::whereJobStatusId($request->job_status);
                    }
    
                    if($request->filter_by_priority == 1) {
                        $jobObjData = $newStatus->whereStatus(1)->whereIn('priority',[0,2,3,4])->whereNull('deleted_at');
                    } else {
                        $jobObjData = $newStatus->whereStatus(1)->where('priority',$request->filter_by_priority)->whereNull('deleted_at');
                    }
                }
                
                
                if ( $loggedInUser->user_type == config('constant.operator') ) { /** Check if operator or not */
                    $jobObjData = $jobObjData->whereUserId($tokenUserId);
                }

                if ( $loggedInUser->user_type == config('constant.engineer')) { /** Check if engineer or not */ 
                    if ($request->job_status == 1) {

                        $newStatus = $this->job::whereIn('job_status_id',[1,2,5,7]);
                        if($request->filter_by_priority == 1) {
                            $jobObjData = $newStatus->whereStatus(1)->whereIn('priority',[0,2,3,4])->whereNull('deleted_at');

                            $jobObjDataQuery1 = $this->job::whereUserId($tokenUserId)->whereIn('job_status_id',[1,2,5,7])->whereStatus(1)->whereIn('priority',[0,2,3,4])->whereNull('deleted_at')->get();
                        } else {
                            $jobObjData = $newStatus->whereStatus(1)->where('priority',$request->filter_by_priority)->whereNull('deleted_at');

                            $jobObjDataQuery1 = $this->job::whereUserId($tokenUserId)->whereIn('job_status_id',[1,2,5,7])->whereStatus(1)->where('priority',$request->filter_by_priority)->whereNull('deleted_at')->whereUserId($tokenUserId)->get();
                        }
                        $jobObjDataQuery2 = $jobObjData->whereAssignedTo($tokenUserId)->get();

                    } else if ($request->job_status == 5) {

                        $newStatus = $this->job::whereIn('job_status_id',[5,7]);
                        if($request->filter_by_priority == 1) {
                            $jobObjData = $newStatus->whereStatus(1)->whereIn('priority',[0,2,3,4])->whereNull('deleted_at');
                            $jobObjDataQuery1 = $this->job->whereUserId($tokenUserId)->whereIn('job_status_id',[5,7])->whereStatus(1)->whereIn('priority',[0,2,3,4])->whereNull('deleted_at')->get();
                        } else {
                            $jobObjData = $newStatus->whereStatus(1)->where('priority',$request->filter_by_priority)->whereNull('deleted_at');
                            $jobObjDataQuery1 = $this->job->whereUserId($tokenUserId)->whereIn('job_status_id',[5,7])->whereStatus(1)->where('priority',$request->filter_by_priority)->whereNull('deleted_at')->get();
                        }
                        $jobObjDataQuery2 = $jobObjData->whereAssignedTo($tokenUserId)->get();
                        
                    } else {
                        
                        $newStatus = $this->job::whereJobStatusId($request->job_status);
                        if($request->filter_by_priority == 1) {
                            $jobObjData = $newStatus->whereStatus(1)->whereIn('priority',[0,2,3,4])->whereNull('deleted_at');
                            $jobObjDataQuery1 = $this->job->whereUserId($tokenUserId)->whereJobStatusId($request->job_status)->whereIn('priority',[0,2,3,4])->whereNull('deleted_at')->get();
                        } else {
                            $jobObjData = $newStatus->whereStatus(1)->where('priority',$request->filter_by_priority)->whereNull('deleted_at');
                            $jobObjDataQuery1 = $this->job->whereUserId($tokenUserId)->whereJobStatusId($request->job_status)->where('priority',$request->filter_by_priority)->whereNull('deleted_at')->get();
                        }
                        $jobObjDataQuery2 = $jobObjData->whereAssignedTo($tokenUserId)->get();
                       
                    }
                    
                    
                    $mergedAdminUser = $jobObjDataQuery1->merge($jobObjDataQuery2);
                    $jobObj = $mergedAdminUser->sortByDesc('created_at')->paginate(config('constant.job_page_limit'));
                    $jobResults = $jobObj->toArray();
    
                } else {
                    $jobObjData = $jobObjData->orderBy('created_at', 'DESC');
                    $jobObj = $jobObjData->paginate(config('constant.job_page_limit')); /** Pagination */
                    $jobResults = $jobObj->toArray();
                }
                
                $jobFinalArr = [];
                
                foreach ($jobObj as $jobObjKey => $jobObjVal) { /** Used to display all faq */
                    $jobArr['job_id'] = $jobObjVal->id;
                    // $jobArr['title'] = $jobObjVal->title;
                    // $jobArr['description'] = $jobObjVal->description;
                    $jobArr['machine_name'] = $jobObjVal->machine->title;
                    $jobArr['location'] = $jobObjVal->location->title;
                    $jobArr['problem'] = $jobObjVal->problem->title;
                    $jobArr['job_status'] = $jobObjVal->job_status_id;
                    $jobArr['priority'] = $jobObjVal->priority;
                    $jobArr['engineer_name'] = @$jobObjVal->assign ? @$jobObjVal->assign->full_name : '';
                    $jobArr['images'] = $this->getIndexJobImage($jobObjVal); /** Get first job image */
                    $jobArr['created_at'] = timeStampConverter($jobObjVal->created_at);
                    $jobArr['decline_reason'] = $jobObjVal->reason;
                    $jobArr['incomplete_reason'] = $jobObjVal->incomplete_reason;
                    $jobArr['declined_by'] = $jobObjVal->declined_by;
                    if ($jobObjVal->declined_by != null) {
                        // if ($jobObjVal->declined_by == "Admin") {
                        //     $user = Admin::find($jobObjVal->declined_by_user);
                        //     $jobArr['declined_by_user'] = $user->first_name.' '.$user->last_name;
                        // } else {
                            $user = User::find($jobObjVal->declined_by_user);
                            $jobArr['declined_by_user'] = $user->first_name.' '.$user->last_name;
                        //}
                    } else {
                        $jobArr['declined_by_user'] = "";
                    }
                    $removedNullArr = removeNullFromArray($jobArr); /** To remove null value from array */
                    array_push($jobFinalArr, $removedNullArr);
                }

                $jobArre['job_list'] = $jobFinalArr;
                $unreadCount = UserNotification::whereUserId($tokenUserId)->whereIsRead(0)->count();
                $jobArre['unread_notification_count'] = $unreadCount;
                $unreadJobRequestCount = 0;
                if ($loggedInUser->user_type == config('constant.engineer')) {
                    $unreadJobRequestCount = Job::whereUserId($loggedInUser->id)->where('status','<>',3)->whereIsRead(0)->count();
                    $unreadWorkOrderCount = Job::whereUserId($loggedInUser->id)->where('status',3)->whereIsRead(0)->count();
                } else if ($loggedInUser->user_type == config('constant.operator')) { 
                    $unreadJobRequestCount = Job::whereUserId($loggedInUser->id)->whereIsRead(0)->count();
                    $unreadWorkOrderCount = 0;
                }else {
                    $unreadJobRequestCount = Job::whereStatus(1)->whereIsRead(0)->count();
                    $unreadWorkOrderCount = 0;
                } 
                $jobArre['unread_job_request_count'] = $unreadJobRequestCount;
                $jobArre['unread_workorder_count'] = $unreadWorkOrderCount;
                $emptyArr['unread_notification_count'] = $unreadCount;
                $emptyArr['unread_job_request_count'] = 0;  
                $emptyArr['unread_workorder_count'] = $unreadWorkOrderCount;
                $lastPage = $jobObj->lastPage(); /** Last page of record */
                if ($currentPage > $lastPage) { /** Check if this is last page or not */
                    return  $this->getResponse($emptyArr,false,405,__('api_messages.no_records_found'),'',$jobObj,$lastPage);
                } else {
                    return count($jobFinalArr) > 0 ? $this->getResponse($jobArre,true,200,__('api_messages.job.jobs_list'),'',$jobObj,$lastPage) : $this->getResponse($emptyArr,false,405,__('api_messages.no_records_found'),'',$jobObj,$lastPage);
                }
            }
        }
    }

    

    /**
     * -------------------------------------------------------
     * | Get Job Images.                                     |
     * |                                                     |
     * | @param $jobObjVal                                   |
     * | @return Response                                    |   
     * -------------------------------------------------------
     */
    public function getJobImages($jobObjVal) 
    {
        $image = [];
        if (count($jobObjVal->jobImage ) > 0) { /** Show image of project */
            foreach ($jobObjVal->jobImage  as $val) {
                $image[] = [ 'image_id' => (int) $val->id, 'image_url' => $val->job_image ];
            }
        } else { /** Show default image of project */
            $image[] = [ 'image_id' => '', 'image_url' => asset('images/default.png') ];
        }
        return $image;
    }  
    
    /**
     * -------------------------------------------------------
     * |  Get First Job Image.                               |
     * |                                                     |
     * | @param $jobObjVal                                   |
     * | @return Response                                    |       
     * -------------------------------------------------------
     */
    public function getIndexJobImage($jobObjVal) 
    {
        if (count($jobObjVal->jobImage ) > 0) { /** Show image of project */
            //$image['image_id'] = $jobObjVal->jobImage[0]->id;
            $image['image_url'] = $jobObjVal->jobImage[0]->job_image;
        } else { /** Show default image of project */
            //$image['image_id'] = '';
            $image['image_url'] =  asset('images/default.png');
        }
        return $image;
    }

    /**
     * -------------------------------------------------------
     * | Filter By Priority.                                 |
     * |                                                     |
     * | @param Request $request                             |   
     * | @return Response                                    |
     * -------------------------------------------------------
     */
    public function filterByPriority(Request $request) 
    {
        $validator = Validator::make($request->all(), [ 'priority' => 'required|integer|min:1','page_number' => 'required|integer|min:1','job_status' => 'required|integer|min:1' ]); /** Validation code */
        if ( $validator->fails() ) {
            return $this->getResponse($this->blankObject,false,400,$validator->errors()->first());
        } else {
            if (!in_array($request->priority,[0,1,2,3,4])) { /** Check if given priority exists or not */
                return $this->getResponse($this->blankObject,false,400, __('api_messages.job.invalid_priority'));
            } else {
                if ( isset($request->engineer_id) && $request->engineer_id != null) {
                    $tokenUserId = $request->engineer_id;
                } else {
                    $tokenUserId = $request->user()->token()->user_id;
                }
                $currentPage = trim($request->page_number); // You can set this to any page you want to paginate to

                // Make sure that you call the static method currentPageResolver() before querying users
                Paginator::currentPageResolver(function () use ($currentPage) {
                    return $currentPage;
                });
                
                $loggedInUser = $this->user::whereId($tokenUserId)->first();

                $jobQuery = $this->job::whereStatus(config('constant.status_active_value'))->whereNull('deleted_at')->whereJobStatusId($request->job_status);

                if ( $request->priority == 1 ) { /** Check if filter by all */
                    $jobObjData = $jobQuery;
                } else {
                    if ( $loggedInUser->user_type == config('constant.manager') ) { /** Check if manager or not */
                        $jobObjData = $jobQuery->wherePriority($request->priority);
                    } else if ( $loggedInUser->user_type == config('constant.engineer') ) { /** Check if engineer or not */ 
                        $jobObjData = $jobQuery->whereAssignedTo($loggedInUser->id)->wherePriority($request->priority);
                    } else {
                        $jobObjData = $jobQuery->whereUserId($tokenUserId)->wherePriority($request->priority);
                    }
                }
                
                $jobObjData = $jobObjData->orderBy('created_at', 'DESC');
                $jobObj = $jobObjData->paginate(config('constant.job_page_limit')); /** Pagination */
                $jobResults = $jobObj->toArray();
                $jobFinalArr = [];
    
                foreach ($jobObj as $jobObjKey => $jobObjVal) { /** Used to display all faq */
                    $jobArr['job_id'] = $jobObjVal->id;
                    // $jobArr['title'] = $jobObjVal->title;
                    // $jobArr['description'] = $jobObjVal->description;
                    $jobArr['machine_name'] = $jobObjVal->machine->title;
                    $jobArr['location'] = $jobObjVal->location->title;
                    $jobArr['problem'] = $jobObjVal->problem->title;
                    $jobArr['job_status'] = $jobObjVal->job_status_id;
                    $jobArr['priority'] = $jobObjVal->priority;
                    $jobArr['engineer_name'] = @$jobObjVal->assign ? @$jobObjVal->assign->full_name : '';
                    $jobArr['images'] = $this->getIndexJobImage($jobObjVal);
                    $jobArr['created_at'] = timeStampConverter($jobObjVal->created_at);
                    $removedNullArr = removeNullFromArray($jobArr); /** To remove null value from array */
                    array_push($jobFinalArr, $removedNullArr);
                }
    
                $jobArre['job_list'] = $jobFinalArr;
                $lastPage = $jobObj->lastPage(); /** Last page of record */
                if ($currentPage > $lastPage) { /** Check if this is last page or not */
                    return  $this->getResponse($this->blankObject,false,405,__('api_messages.no_records_found'),'',$jobObj,$lastPage);
                } else {
                    return count($jobFinalArr) > 0 ? $this->getResponse($jobArre,true,200,__('api_messages.job.jobs_list'),'',$jobObj,$lastPage) : $this->getResponse($this->blankObject,false,405,__('api_messages.no_records_found'),'',$jobObj,$lastPage);
                }
            }
        }
    }

    /**
     * -------------------------------------------------------
     * | Decline Job.                                        |
     * |                                                     |
     * | @param Request $request                             |
     * | @return Response                                    |      
     * -------------------------------------------------------
     */
    public function declineJob(Request $request) 
    {
        $validator = Validator::make($request->all(), [ 'job_id' => 'required|integer','reason' => 'required' ]); /** Validation code */
        if ( $validator->fails() ) {
            return $this->getResponse($this->blankObject,false,400,$validator->errors()->first());
        } else {
            $tokenUserId = $request->user()->token()->user_id;
            $userInfo = $this->user::find($tokenUserId);
            $jobObjData = $this->job::whereId($request->job_id)->whereStatus(config('constant.status_active_value'))->whereDeletedAt(null)->first();
            if ( @$jobObjData ) {
                 if ($userInfo->user_type == 2) { // check if engineer
                    if ($jobObjData->assignedTo == NULL ) {
                        return $this->getResponse($this->blankObject,false,400,__('api_messages.job.you_cannot_declined_job'));
                    } 
                } 
                // if ($jobObjData->job_status_id == 2) { // Assigned
                //     return $this->getResponse($this->blankObject,false,400, 'Job is already assigned');
                // }
                if ($jobObjData->job_status_id == 6) { // KIV
                    return $this->getResponse($this->blankObject,false,400, 'Job is already put in KIV');
                }
                if ($jobObjData->job_status_id == 5) { // Declined
                    return $this->getResponse($this->blankObject,false,400, 'Job is already declined');
                }
                if ( $jobObjData->job_status_id == config('constant.job_status_list.declined') ) { /** Check if already declined or not */ 
                    return $this->getResponse($this->blankObject,false,400, __('api_messages.job.already_declined'));    
                } else {
                    $message = 'Job request declined';
                    /** job declined send notification to operator/engineer who created job */
                    $users = $this->user::find($jobObjData->user_id);
                    if (@$users) {
                    event(new UserNotificationEvent($users->id, $jobObjData->id, get_class($this->job), $message, 7)); /** Event activity record */
                    $this->sendPushNotificationToSingle($jobObjData,$users,'job_declined','Job request declined','Job Declined ');
                    }
                    $jobObjData->update(['job_status_id' => config('constant.job_status_list.declined'), 'reason' => $request->reason,'declined_by' => @$userInfo->user_type == 1 ? 'Admin' : 'Staff','declined_by_user' => $tokenUserId]);
                    $url = 'job/detail/'.$jobObjData->uuid;
                    $this->sendNotificationToAdmin($tokenUserId,$jobObjData->title.$message,$jobObjData->id,$url); /*Notifiy Admin */

                    // Declined by info
                    // $jobObjData->update([]);
                    return $this->getResponse($this->blankObject,true,200,__('api_messages.job.declined_msg'));
                }
            } else {
                return $this->getResponse($this->blankObject,false,400,__('api_messages.job.job_not_found'));
            }
        }
    }

    /**
     * -------------------------------------------------------
     * | Start Job.                                          |
     * |                                                     |
     * | @param Request $request                             |
     * | @return Response                                    |
     * -------------------------------------------------------
     */
    public function startJob(Request $request) 
    {
        $validator = Validator::make($request->all(), [ 'job_id' => 'required|integer' ]); /** Validation code */
        if ( $validator->fails() ) {
            return $this->getResponse($this->blankObject,false,400,$validator->errors()->first());
        } else {
            $tokenUserId = $request->user()->token()->user_id;
            $jobObjData = $this->job::whereId($request->job_id)->whereStatus(config('constant.status_active_value'))->whereDeletedAt(null)->first();
            if ( $jobObjData ) {
                if ($jobObjData->assigned_to != $tokenUserId) {
                    return $this->getResponse($this->blankObject,false,400,'Job is not assigned  to you');
                }
                if ( $jobObjData->job_status_id == config('constant.job_status_list.assigned') &&  $jobObjData->assigned_to != NULL ) { /** Check if already declined or not */ 
                    // if ( $jobObjData->jobLogs->count() > 0 ) {
                    //     return $this->getResponse($this->blankObject,false,400,__('api_messages.job.job_already_started'));
                    // } else {
                        /** Send job started notification to manager who has assigned this job*/
                        $user = $this->user::find($tokenUserId);
                        $message = $user->full_name_text.' Job started';

                        if ($jobObjData->assigned_by != null) {
                            $users = $this->user::find($jobObjData->assigned_by);
                            event(new UserNotificationEvent($users->id, $jobObjData->id, get_class($this->job), $message, 5)); /** Event activity record */
                            $this->sendPushNotificationToSingle($jobObjData,$users,'job_started',$message,'Job Started');
                        }

                        // Send push notifications to operators who has created the jobs
                        $operatorUsr = $this->user::whereId($jobObjData->user_id)->whereUserType(3)->first();
                        if ($operatorUsr) {
                            $this->sendPushNotificationToSingle($jobObjData,$operatorUsr,'job_started',$message,'Job Started');
                            event(new UserNotificationEvent($operatorUsr->id, $jobObjData->id, get_class($this->job), $message, 5)); /** Event activity record */
                        }
                        
                        /** Update job status to completed */
                        $jobObjData->update(['total_job_duration' => NULL,'job_status_id' => config('constant.job_status_list.ongoing')]);
                        $jobLog = new JobLog();
                        $log = $jobLog->create([ 'job_id' => $jobObjData->id, 'assigned_to' => @$jobObjData->assigned_to != NULL ? $jobObjData->assigned_to : NULL,'start_by' => $tokenUserId,'start_time' => now() ]);
                        $list['job_id'] = $jobObjData->id;
                        $list['job_start_datetime'] = timeStampConverter($log->start_time);
                        // $logArr['job_log'] = $list;
                        return $this->getResponse($list,true,200,__('api_messages.job.job_started_msg'));
                    //}
                } else {
                    return $this->getResponse($this->blankObject,false,400,__('api_messages.job.job_not_assigned_yet'));
                }
            } else {
                return $this->getResponse($this->blankObject,false,400,__('api_messages.job.job_not_found'));
            }
        }
    }

    /**
     * -------------------------------------------------------
     * | End Job.                                            |
     * |                                                     |
     * | @param Request $request                             |
     * | @return Response                                    |
     * -------------------------------------------------------
     */
    public function endJob(Request $request) 
    {
        $validator = Validator::make($request->all(), [ 'job_id' => 'required|integer','time_duration' => 'required','type' => 'required|min:1|max:2','comment' => Rule::requiredIf(($request->type == 2)) ]); /** Validation code */
        if ( $validator->fails() ) {
            return $this->getResponse($this->blankObject,false,400,$validator->errors()->first());
        } else {
            $tokenUserId = $request->user()->token()->user_id;
            $jobObjData = $this->job::whereId($request->job_id)->whereStatus(config('constant.status_active_value'))->whereNull('deleted_at')->first();
            if ( $jobObjData ) {
                if ( $jobObjData->assigned_to != NULL ) { /** Check if already declined or not */
                    if ($jobObjData->assigned_to != $tokenUserId) {
                        return $this->getResponse($this->blankObject,false,400,'Job is not assigned to you');
                    }
                    if ( $jobObjData->jobLogs->count() > 0 ) {
                        if ( @$jobObjData->jobLogsEmptyDuration[0] ) {
                            /** Update job status to completed */
                            if ($request->type == 1) {
                                $jobObjData->update(['total_job_duration' => $request->time_duration,'job_status_id' => config('constant.job_status_list.completed')]);
                            } else {
                                $jobObjData->update(['total_job_duration' => $request->time_duration,'job_status_id' => config('constant.job_status_list.unable_to_complete'),'incomplete_reason' => $request->comment]);
                            }
                            $log = $jobObjData->jobLogsEmptyDuration[0];
                            $log->update(['end_by' => $tokenUserId,'duration' => $request->time_duration,'end_time' => now()]);
                            $user = $this->user::whereId($tokenUserId)->first();
                            $url = 'job/detail/'.$jobObjData->uuid;
                            $message = $user->full_name_text.' Job completed';
                            $this->sendNotificationToAdmin($tokenUserId,$jobObjData->title.$message,$jobObjData->id,$url); /*Notifiy Admin */

                            /** Send job completed notification to manager who has assigned this job starts here */
                            if ($jobObjData->assigned_by != null) {
                                if ($request->type == 1) {
                                    $users = $this->user::find($jobObjData->assigned_by);
                                    event(new UserNotificationEvent($users->id, $jobObjData->id, get_class($this->job), $message, 2)); /** Event activity record */
                                    $this->sendPushNotificationToSingle($jobObjData,$users,'job_completed',$message,'Job Completed');

                                    // Send push notifications to operators who has created the jobs
                                    $operatorUsr = $this->user::whereId($jobObjData->user_id)->whereUserType(3)->first();
                                    if ($operatorUsr) {
                                        event(new UserNotificationEvent($operatorUsr->id, $jobObjData->id, get_class($this->job), $message, 2)); /** Event activity record */
                                        $this->sendPushNotificationToSingle($jobObjData,$operatorUsr,'job_completed',$message,'Job Completed');
                                    }
                                } else {
                                    $users = $this->user::find($jobObjData->assigned_by);
                                    event(new UserNotificationEvent($users->id, $jobObjData->id, get_class($this->job), $message, 11)); /** Event activity record */
                                    $this->sendPushNotificationToSingle($jobObjData,$users,'job_incompleted','Job in completed','Job InCompleted');
                                    // Send push notifications to operators who has created the jobs
                                    $operatorUsr = $this->user::whereId($jobObjData->user_id)->whereUserType(3)->first();
                                    if ($operatorUsr) {
                                        event(new UserNotificationEvent($operatorUsr->id, $jobObjData->id, get_class($this->job), $message, 11)); /** Event activity record */
                                        $this->sendPushNotificationToSingle($jobObjData,$operatorUsr,'job_incompleted','Job in completed','Job InCompleted');
                                    }
                                }
                                
                            }



                            return $this->getResponse($this->blankObject,true,200,__('api_messages.job.job_ended_msg'));    
                        } else {
                            return $this->getResponse($this->blankObject,false,400,__('api_messages.job.job_already_ended'));    
                        }
                    } else {
                        return $this->getResponse($this->blankObject,false,400,__('api_messages.job.job_not_started_yet'));
                    }
                } else {
                    return $this->getResponse($this->blankObject,false,400,__('api_messages.job.job_not_assigned_yet'));
                }
            } else {
                return $this->getResponse($this->blankObject,false,400,__('api_messages.job.job_not_found'));
            }
        }
    }

    /**
     * -------------------------------------------------------
     * | Accept Job.                                         |
     * |                                                     |
     * | @param Request $request                             |
     * | @return Response                                    |
     * -------------------------------------------------------
     */
    public function acceptJob(Request $request) 
    {
        $validator = Validator::make($request->all(), [ 'job_id' => 'required|exists:jobs,id', 'priority' => Rule::requiredIf(($request->type == 2)), 'engineer_id' => Rule::requiredIf(($request->type == 2)),  'type' => 'required|integer' ]); /** Validation code */
        if ( $validator->fails() ) {
            return $this->getResponse($this->blankObject,false,400,$validator->errors()->first());
        } else {
            if ($request->engineer_id != 0) {
                $user = $this->user::whereId($request->engineer_id)->whereStatus(config('constant.status_active_value'))->whereNull('deleted_at')->whereUserType(config('constant.engineer'))->first();
                if ( !$user ) {
                    return $this->getResponse($this->blankObject,false,400, __('api_messages.job.engineer_no_longer_msg'));
                } 
            }
            // if ( isset($request->priority) && $request->priority != "" && !in_array($request->priority,[2,3,4]) ) { /** Check if given priority exists or not */
            //     return $this->getResponse($this->blankObject,false,400, __('api_messages.job.invalid_priority'));
            // } else {
                // 1 = KIV, 2 = Assign Engineer
                if ( !in_array($request->type,[1,2]) ) { /** Check if given type exists or not */
                    return $this->getResponse($this->blankObject,false,400, __('api_messages.job.invalid_type_provided'));
                } else {
                    $tokenUserId = $request->user()->token()->user_id;
                    $user = $this->user::find($tokenUserId);
                    $jobObjData = $this->job::whereId($request->job_id)->whereStatus(config('constant.status_active_value'))->whereNull('deleted_at')->first();
                    if ($user->user_type == 2 && $jobObjData->assigned_to != $tokenUserId) {
                        return $this->getResponse($this->blankObject,false,400,'Job is not assigned to you');
                    }
                    if ( $jobObjData ) {
                        // Check if job is already assigned or put in KIV
                        // if ($jobObjData->job_status_id == 2) { // Assigned
                        //     return $this->getResponse($this->blankObject,false,400, 'Job is already assigned');
                        // }
                        if ($jobObjData->job_status_id == 6) { // KIV
                            return $this->getResponse($this->blankObject,false,400, 'Job is already put in KIV');
                        }
                        // if ($jobObjData->job_status_id == 5) { // Declined
                        //     return $this->getResponse($this->blankObject,false,400, 'Job is already declined');
                        // }
                        $user = $this->user::whereId($tokenUserId)->first();
                        $url = 'job/detail/'.$jobObjData->uuid;
                        $message = $user->full_name_text.' Job request accepted';
                        $assignedMessage = 'Engineer assigned to job';
                        $this->sendNotificationToAdmin($tokenUserId,$jobObjData->title.$message,$jobObjData->id,$url); /*Notifiy Admin */

                        if ( $request->type == 1 ) { // KIV (Keep In View)
                            $jobObjData->update(['job_status_id' => config('constant.job_status_list.kiv') ]);
                            /** Send job request accepted to operator starts here */
                            $usersList = $this->user::whereId($jobObjData->user_id)
                            ->where('user_type','<>',2)->get();
                            if ($usersList->count() > 0) {
                                foreach ($usersList as $user) {
                                    event(new UserNotificationEvent($user->id, $jobObjData->id, get_class($this->job), 'Job request is put on KIV', 8)); /** Event activity record */
                                }
                                $this->sendPushNotificationToMultiple($jobObjData,$usersList,'job_accepted','Job request is put on KIV','Job KIV');
                            }
                            /** Send job request accepted to operator ends here */

                            /** Send to assigned engineer **/
                            if ($jobObjData->user_id != null) {
                                $assignedEngineer = $this->user::find($jobObjData->user_id);
                                $this->sendPushNotificationToSingle($jobObjData,$assignedEngineer,'job_accepted','Job request is put on KIV','Job KIV');    
                                event(new UserNotificationEvent($assignedEngineer->id, $jobObjData->id, get_class($this->job), 'Job request is put on KIV', 8)); /** Event activity record */
                            }
                            

                            return $this->getResponse($this->blankObject,true,200, __('api_messages.job.kiv_saved'));
                        } else { // Assign Engineer
                            
                            /** Send job request accepted to operator starts here */
                            $usersList = $this->user::whereId($jobObjData->user_id)->get();
                            if ($usersList->count() > 0) {
                                

                                if ( $jobObjData->assigned_to != null &&  $jobObjData->assigned_to != $request->engineer_id ) { /** Check if new engineer assigned or not */
                                    $removedEngineer = $jobObjData->assigned_to;
                                    $assignedEngineer = $request->engineer_id;
                                    $message = 'Job reassigned to new engineer';
            
                                    /** Reassign engineer send notification to reassigned engineer & removed engineer starts here */
                                    if ($removedEngineer != null) {
                                        event(new UserNotificationEvent($removedEngineer, $jobObjData->id, get_class($this->job), $message, 6)); /** Event activity record */
            
                                        $this->sendPushNotificationToSingle($jobObjData,$this->user::find($removedEngineer),'engineer_reassigned','You have been removed from job','Engineer reassigned');
                                    }
                                    
                                    event(new UserNotificationEvent($assignedEngineer, $jobObjData->id, get_class($this->job), $message, 6)); /** Event activity record */
            
                                    
                                    $this->sendPushNotificationToSingle($jobObjData,$this->user::find($assignedEngineer),'engineer_reassigned','Job reassigned to you','Engineer reassigned');
            
                                    /** Reassign engineer send notification to reassigned engineer & removed engineer ends here */
                                    
                                    /** Reassign engineer send notification to operator starts here */
                                    $usersList = $this->user::whereId($jobObjData->user_id)->get();
                                    
                                    if ($usersList->count() > 0) {
                                        foreach ($usersList as $user) {
                                            event(new UserNotificationEvent($user->id, $jobObjData->id, get_class($this->job), $message, 6)); /** Event activity record */
                                        }
                                        $this->sendPushNotificationToMultiple($jobObjData,$usersList,'engineer_reassigned','Job reassigned to new engineer','Engineer reassigned');
                                    }
                                    /** Reassign engineer send notification to operator ends here */   
                                } else {
                                    foreach ($usersList as $user) {
                                        event(new UserNotificationEvent($user->id, $jobObjData->id, get_class($this->job), $message, 3)); /** Event activity record */
                                        event(new UserNotificationEvent($user->id, $jobObjData->id, get_class($this->job), $assignedMessage, $request->type == 1 ? 8 : 4)); /** Event activity record */
                                    }

                                    $this->sendPushNotificationToMultiple($jobObjData,$usersList,'job_accepted',$message,'Job Accepted');

                                    $engineerList = $this->user::whereId($request->engineer_id)->get();
                                    $this->sendPushNotificationToMultiple($jobObjData,$engineerList,'engineer_assigned','Engineer assigned to job','Engineer Assigned');
                                }
                            }
                            $jobObjData->update([
                                'assigned_to' => $request->engineer_id, 
                                'priority' => $request->priority,
                                'job_status_id' => config('constant.job_status_list.assigned'),
                                'assigned_by' => $tokenUserId 
                            ]);
                            /** Send job request accepted to operator ends here */
                            return $this->getResponse($this->blankObject,true,200, __('api_messages.job.priority_set_msg'));
                        }   
                    } else {
                        return $this->getResponse($this->blankObject,false,400,__('api_messages.job.job_not_found'));
                    }
                }
            //}
        }
    }


    /** 
     * --------------------------------------------------------------------------------
     * | Send Push Notifications To Multpile Users                                    |   
     * |                                                                              | 
     * | @param $notificationData,$userNotificationObj,$type,$message,$title          |
     * |                                                                              |                     
     * --------------------------------------------------------------------------------
     */
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
     * --------------------------------------------------------------------------------
     * | Send Push Notifications To Single User                                       |   
     * |                                                                              | 
     * | @param $notificationData,$userNotificationObj,$type,$message,$title          |
     * |                                                                              |                     
     * --------------------------------------------------------------------------------
     */
    public function sendPushNotificationToSingle($notificationData,$userNotificationObj,$type,$message,$title) 
    {
        if (@$userNotificationObj->usersFcmTokens[0]) {
            $userToken = $userNotificationObj->usersFcmTokens[0]['fcm_token'];
            $userDeviceType = $userNotificationObj->usersFcmTokens[0]['device_type'];
            $fcmData = [
                'job_id' => (int) $notificationData->id,
                'type' => $type,
                'message' => $message,
            ];
            $this->basicPushNotification($title, $message, $fcmData, $userToken, $userDeviceType);
        }
    }

    public function moveToJobRequest(Request $request) 
    {
        $validator = Validator::make($request->all(), [ 'job_id' => 'required|exists:jobs,id']); /** Validation code */
        if ( $validator->fails() ) {
            return $this->getResponse($this->blankObject,false,400,$validator->errors()->first());
        } else {
            $tokenUserId = $request->user()->token()->user_id;
            $user = $this->user::find($tokenUserId);
            if (@$user->user_type == 1) {
                $job = $this->job::find($request->job_id);
                $job->update(['job_status_id' => 1]);
                return $this->getResponse($this->blankObject,true,200, 'Status changed to job request successfully');
            } else {
                return $this->getResponse($this->blankObject,false,400, 'You are not allowed, only manager can do this change');
            }
        }
    }
}
?>