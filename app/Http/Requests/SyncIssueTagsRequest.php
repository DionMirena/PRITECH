<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SyncIssueTagsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tag_id' => ['required', 'integer', 'exists:tags,id'],
        ];
    }
}
