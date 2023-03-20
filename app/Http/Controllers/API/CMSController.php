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
use App\Models\CMS;
use App\Models\User;


class CMSController extends BaseController
{
    public $successStatus = 200;
    protected $cms;
    protected $user;

    /**
     * -------------------------------------------------------
     * | Create a new controller instance.                   |
     * |                                                     |
     * -------------------------------------------------------
     */
    public function __construct(CMS $cms,User $user)
    {
        parent::__construct();
        $this->cms = $cms;
        $this->user = $user;
    }

    /**
     * -------------------------------------------------------
     * | Get CMS Page details.                               |
     * |                                                     |
     * | @param Request $request                             |
     * | @return Response                                    |
     * -------------------------------------------------------
     */
    public function detail(Request $request) 
    {
        $validator = Validator::make($request->all(), [ 'slug' => 'required|exists:cms,page_slug']); /** Validation code */
        if ($validator->fails()) {
            return $this->getResponse($this->blankObject,false,400,$validator->errors()->first());
        } else {
            $cmsDetail = $this->cms::wherePageSlug($request->slug)->first();
            $cmsArr['title'] = $cmsDetail->page_title;
            $cmsArr['content'] = $cmsDetail->page_content;
            $removedNullArr = removeNullFromArray($cmsArr); /** To remove null value from array */
            return $this->getResponse($removedNullArr,true,200,$cmsDetail->page_title.' details');
        }
    }
}