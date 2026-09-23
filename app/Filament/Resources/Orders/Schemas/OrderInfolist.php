<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Filament\Shared\Schemas\OrderInfolist as SharedOrderInfolist;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return SharedOrderInfolist::configure($schema);
    }
}
