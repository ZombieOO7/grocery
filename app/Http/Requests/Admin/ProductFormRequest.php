<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class ProductFormRequest extends FormRequest
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
            'title' => ['required', 'max:200'],
            'category_id' => 'required',
            'sub_category_id' => 'required',
            // 'short_description' => 'required',
            'description' => 'required',
            'status' => 'required',
            'stock_status' => 'required',
            'price' => 'required',
            'status' => 'required',
            // 'images' => 'required',
        ];
        return $validator;
    }
}
