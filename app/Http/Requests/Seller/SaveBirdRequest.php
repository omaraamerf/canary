<?php

namespace App\Http\Requests\Seller;

use App\Http\Requests\BirdRequest;

class SaveBirdRequest extends BirdRequest
{
    public function rules(): array
    {
        return $this->commonRules();
    }
}
