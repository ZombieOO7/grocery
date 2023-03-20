<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class SubCategoryFormRequest extends FormRequest
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
            'title' => ['required','max:30'],
            'category_id' => 'required',
            'status' => 'required',
        ];
        if ($request->has('id') && $request->id != null) {
            $validator['title'][] = 'unique:sub_categories,title,' . $request->id;
        } else {
            $validator['title'][] = 'unique:sub_categories,title';
        }
        return $validator;
    }
}
