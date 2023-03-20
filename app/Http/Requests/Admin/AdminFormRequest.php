<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class AdminFormRequest extends FormRequest
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

        $routeName = Route::currentRouteName();
        if($routeName == 'profile_update'){
            $validator = [
                'first_name' => 'required|max:'.config('constant.name_length'),
                'last_name' => 'required|max:'.config('constant.name_length'),
                'email' => ['required','max:'.config('constant.email_length')],
            ];
        }else{
            $validator = [
                'first_name' => 'required|max:20',
                'last_name' => 'required|max:20',
                'email' => ['required'],
                'role.*' => 'required',
                'status' => 'required',
            ];
            if ($request->has('id') && $request->id != null) {
                $validator['email'][] = 'unique:admins,email,' . $request->id;
            } else {
                $validator['email'][] = 'unique:admins,email';
                $validator['password'] = 'required';
            }
        }
        return $validator;
    }
}