<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DownloadUrlRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'target_url' => 'required|active_url|regex:/^https:\/\/store\.line\.me\/emojishop\/product\/.+$/',
            'img_name' => 'required|alpha_num:ascii|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'target_url.required' => 'URLは必須です',
            'target_url.active_url' => 'URLを確認してください',
            'target_url.regex' => 'URLを確認してください。LINE絵文字だけダウンロード可能です。スタンプはできません。',
            'img_name.required' => '接頭語は必須です',
            'img_name.alpha_num' => '接頭語は半角英数字のみです',
            'img_name.max' => '接頭語は最大２０文字です',
        ];
    }
}
