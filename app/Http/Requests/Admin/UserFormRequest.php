<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserFormRequest extends FormRequest
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
            'first_name' => 'required|max:'.config('constant.name_length'),
            'last_name' => 'required|max:'.config('constant.name_length'),
            // 'email' => ['required','max:'.config('constant.email_length')],
            'phone'=>['required','max:'.config('constant.phone_length')],
            'company_id'=>'required|exists:companies,id',
            // 'user_type'=>'required',
            'status' => 'required',
            'role_id' => 'required',
            'status' => [Rule::requiredIf(function () use ($request) {
                    if($request->id != null){
                        $user = User::whereId($request->id)->withCount(['jobs'])->first();
                        if($user && $user->jobs_count > 0){
                            return false;
                        }else{
                            return true;
                        }
                    }else{
                        return false;
                    }
                })
            ]
        ];
        if ($request->has('id') && $request->id != null) {
            // $validator['email'][] = 'unique:users,email,' . $request->id.',id,deleted_at,NULL';
            // $validator['phone'][] = 'unique:users,phone,' . $request->id.',id,deleted_at,NULL';
            $validator['image'] ='nullable|max:5120';
        } else {
            $validator['email'] = ['required','max:'.config('constant.email_length'),'unique:users,email,NULL,id,deleted_at,NULL'];
            $validator['user_type'] ='required';
            // $validator['phone'][] = 'unique:users,phone,NULL,id,deleted_at,NULL';
            $validator['password'] = 'required|max:'.config('constant.password_max_length').'|min:'.config('constant.password_min_length');
            $validator['confirm_password'] = 'required|same:password|max:'.config('constant.password_max_length').'|min:'.config('constant.password_min_length');
            $validator['image'] ='required|max:5120';
        }
        return $validator;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'company_id.required'=>'The company field is required.',
            'user_type.required'=>'The company position field is required.',
            'image.required'=>'The profile picture field is required.',
            'image.mimes'=>'The profile picture field is must be a file of type: jpeg, jpg, png.',
            'phone.unique'=>'The phone number has already been taken',
            'email.unique'=>'The email address has already been taken',
        ];
    }
}