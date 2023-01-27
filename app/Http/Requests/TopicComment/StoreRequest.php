<?php

namespace App\Http\Requests\TopicComment;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'comment' => ['required', 'string', 'max:500'],
            'image' => ['sometimes', 'required', 'image', 'mimes:png,jpg,jpeg,gif', 'max:2024']
        ];
    }
}
