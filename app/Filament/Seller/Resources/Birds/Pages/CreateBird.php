<?php

namespace App\Filament\Seller\Resources\Birds\Pages;

use App\Filament\Seller\Resources\Birds\BirdResource;
use App\Filament\Shared\Schemas\BirdForm;
use App\Services\BirdService;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;
use Illuminate\Database\Eloquent\Model;

class CreateBird extends CreateRecord
{
    use HasWizard;

    protected static string $resource = BirdResource::class;

    protected function getSteps(): array
    {
        return BirdForm::steps(admin: false);
    }

    protected function handleRecordCreation(array $data): Model
    {
        return app(BirdService::class)->createForSeller($data, auth()->user());
    }
}
