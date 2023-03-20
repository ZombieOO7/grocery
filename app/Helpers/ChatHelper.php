<?php
namespace App\Helpers;

use App\Models\ChatGroup;
use App\Models\Company;
use App\Models\Message;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\UserGroupMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class ChatHelper extends BaseHelper
{

    protected $message, $firebase, $database;
    public function __construct(ChatGroup $chatGroup, User $user,Message $message, UserGroup $userGroup,UserGroupMessage $userGroupMessage)
    {
        $this->message = $message;
        $this->userGroup = $userGroup;
        $this->userGroupMessage = $userGroupMessage;
        $this->chatGroup = $chatGroup;
        $this->user = $user;

        $serviceAccount = ServiceAccount::fromJsonFile(__DIR__ . '/Chat2FirebaseKey.json');
        $firebase = (new Factory)->withServiceAccount($serviceAccount)
                    ->withDatabaseUri('https://fir-chat-b6f7e.firebaseio.com')
                    ->create();
        $this->database =  $firebase->getDatabase();
        parent::__construct();
    }

    /**
     * ------------------------------------------------------
     * | company store                                      |
     * |                                                    |
     * | @param Request $request,$uuid                      |
     * |-----------------------------------------------------
     */
    public function store(Request $request, $uuid = null)
    {
        $message = new Message();
        $request['is_read'] = 0;
        $message->fill($request->all())->save();
        $message['group_id'] = (int)$request->group_id;
        $createPost =   $this->database->getReference('messages/'.$message->id)->set($message);
        $userIds = $this->userGroup::whereGroupId($request->group_id)->pluck('user_id');
        $data = [];
        foreach($userIds as $key => $id){
            $data['user_id'] = (int)$id;
            $data['is_read'] = 0;
            $data['message_id'] = (int)$message->id;
            $data['group_id'] = (int)$request->group_id;
            $data['admin_id']=null;
            $createPost =   $this->database->getReference('user_group_messages/'. $request->group_id)->push($data);
        }
        $data = [
            'admin_id'=>(int)$request->admin_id,
            'is_read' => 1,
            'group_id' => (int)$request->group_id,
            'message_id' => (int)$message->id,
            'user_id' => null,
        ];
        $createPost =   $this->database->getReference('user_group_messages/'. $request->group_id)->push($data);
        return $message;
    }

    public function groupMessage($request){
        $messages = $this->database->getReference('messages')
                    ->orderByChild("group_id")
                    ->equalTo((int)$request->id)
                    ->getvalue();
        
        if($messages){
            $groupMessageIds = array_keys($messages);
            $fromDate = ($request->fromDate != null)?date("Y-m-d",strtotime($request->fromDate)):null;
            $toDate = ($request->toDate != null)?date("Y-m-d",strtotime($request->toDate."+1 day")):null;
            $messages = $this->message::whereIn('id',$groupMessageIds)
                        ->where(function($q)use($request,$fromDate,$toDate){
                            if($request->user_id != null){
                                $q->where('sender_id',$request->user_id);
                            }
                            if($fromDate != null && $toDate != null){
                                $q->whereBetween('created_at',[$fromDate,$toDate]);
                            }
                        })
                        ->orderBy('id','asc')->get();
            return $messages;    
        }
    }
    public function getGroups($request){
        if($request->message != null && $request->message !=''){
            $messages = $this->database->getReference('messages')
                        ->orderByChild("message")
                        ->equalTo($request->message)
                        ->getvalue();
            $groupIds = array_column($messages, 'group_id');
            $groups =   $this->chatGroup::whereIn('id',$groupIds)
                        ->get();
            $grpMsg = $this->groupWithMsg($groups);
            return @$grpMsg;
        }
        if($request->user_id!=null || ($request->fromDate!=null &&$request->toDate!=null)){
            $groupIds = $this->userGroup::whereUserId($request->user_id)->pluck('group_id');
            $groups =   $this->chatGroup::whereIn('id',$groupIds)
                        ->get();
            $grpMsg = $this->groupWithMsg($groups);
            return @$grpMsg;
        }
        // get all groups 
        $groups = $this->chatGroup::get();
        $grpMsg = $this->groupWithMsg($groups);
        return $grpMsg;
    }

    public function groupWithMsg($groups){
        // foreach($groups as $key => $group){
        //     $messages = $this->database->getReference('messages/')
        //                 ->orderByChild("group_id")
        //                 ->equalTo((int)$group->id)
        //                 ->getvalue();
        //     if($messages){
        //         $messageIds = array_keys($messages);
        //         $lastMsgId = max($messageIds);
        //         $message = $this->message::whereId($lastMsgId)->first();
        //         $groups[$key]['message'] = $message;

        //         $unReadMsg =   $this->database->getReference('user_group_messages/'. $group->id)
        //                         ->orderByChild("admin_id")
        //                         ->equalTo(1)
        //                         ->getvalue();
        //             $counter = 0;
        //             foreach($unReadMsg as $msg){
        //                 if($msg['is_read'] == 0){
        //                     $counter++;
        //                 }
        //             }
        //             $groups[$key]['un_read_message_count'] = $counter; 
        //     }
        // }
        return $groups;
    }
}
