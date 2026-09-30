<?php

namespace App\Http\Requests;

use App\Enums\PostCategory;
use App\Http\Requests\Concerns\ValidatesCommunityMedia;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePostRequest extends FormRequest
{
    use ValidatesCommunityMedia;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category' => ['required', Rule::enum(PostCategory::class)],
            'title' => ['required', 'string', 'min:5', 'max:150'],
            'body' => ['required', 'string', 'min:10', 'max:5000'],
            'breed_id' => ['nullable', 'exists:breeds,id'],
            'region_id' => ['nullable', 'exists:regions,id'],
            ...$this->mediaRules(maxImages: 4, maxVideos: 1),
        ];
    }
}
