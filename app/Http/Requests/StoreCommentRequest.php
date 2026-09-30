<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\ValidatesCommunityMedia;
use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    use ValidatesCommunityMedia;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'min:2', 'max:3000'],
            ...$this->mediaRules(maxImages: 3, maxVideos: 0),
        ];
    }
}
