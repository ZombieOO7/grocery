<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class CMSFormRequest extends FormRequest
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
            'page_title' => ['required'],
            'page_content' => 'required',
            // 'status' => 'required',

        ];
        if ($request->has('id') && $request->id != null) {
            $validator['title'][] = 'unique:cms,page_title,'.$request->id.',id,deleted_at,NULL';
        } else {
            $validator['title'][] = 'unique:cms,page_title,NULL,id,deleted_at,NULL';
        }
        return $validator;
    }
}