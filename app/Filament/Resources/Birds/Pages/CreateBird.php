<?php

namespace App\Filament\Resources\Birds\Pages;

use App\Filament\Resources\Birds\BirdResource;
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
        return BirdForm::steps(admin: true);
    }

    protected function handleRecordCreation(array $data): Model
    {
        return app(BirdService::class)->createForAdmin($data, auth()->user());
    }
}
