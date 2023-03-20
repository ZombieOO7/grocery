<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class CategoryFormRequest extends FormRequest
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
            'title' => ['required', 'max:' . config('constant.name_length')],
            'status' => 'required',
            // 'image' => 'required',
        ];
        if ($request->has('id') && $request->id != null) {
            $validator['title'][] = 'unique:categories,title,' . $request->id . ',id,deleted_at,NULL';
        } else {
            $validator['title'][] = 'unique:categories,title,NULL,id,deleted_at,NULL';
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
            'title.unique' => __('admin/messages.name', ['type' => 'Category']),
        ];
    }
}
