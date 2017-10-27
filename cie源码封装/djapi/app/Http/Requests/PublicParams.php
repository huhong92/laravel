<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PublicParams extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'uuid'	=> 'required|unique:posts',
			'token'	=> 'required|max:32',
			'sign'	=> 'required|max:32',
        ];
    }
	
	public function messages()
	{
		return [
			'uuid.required' => '参数错误'
		];
	}
}
