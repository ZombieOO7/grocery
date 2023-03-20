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
use App\Models\Machine;

class QrCodeController extends BaseController
{
    public $successStatus = 200;
    protected $machine;

    /**
     * -------------------------------------------------------
     * | Create a new controller instance.                   |
     * |                                                     |
     * -------------------------------------------------------
     */
    public function __construct(Machine $machine)
    {
        parent::__construct();
        $this->machine = $machine;
    }

    public function qrCode(Request $request) 
    {
        $validator = Validator::make($request->all(), [ 'qr_code' => 'required|exists:machines,qr_code' ]); /** Validation code */
        if ( $validator->fails() ) {
            return $this->getResponse($this->blankObject,false,400,$validator->errors()->first());
        } else {
            $data = $this->machine::whereQrCode($request->qr_code)->first();
            $arr = [];
            $arr['machine']['machine_id'] = $data->id;
            $arr['machine']['machine_name'] = $data->title;
            $arr['location']['location_id'] = $data->location_id;
            $arr['location']['location_name'] = $data->location->title;
            if ($data) {
                return $this->getResponse($arr,true,200,'Success');
            } else {
                return $this->getResponse($this->blankObject,false,400,'No data found');
            }
        }
    }
}
?>