<?php

namespace App\Filament\Seller\Resources\Birds\Schemas;

use App\Filament\Shared\Schemas\BirdForm as SharedBirdForm;
use Filament\Schemas\Schema;

class BirdForm
{
    public static function configure(Schema $schema): Schema
    {
        return SharedBirdForm::configure($schema, admin: false);
    }
}
