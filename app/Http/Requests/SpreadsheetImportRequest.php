<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SpreadsheetImportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->user()) {
            return $this->user()->isSuperAdmin();
        }

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
            'spreadsheet' => [
                'required',
                'regex:/^data:application\/[a-z\-\.]+;base64,/',
            ],
            'organisation_id' => [
                'sometimes',
                'exists:organisations,id',
            ],
        ];
    }
}
