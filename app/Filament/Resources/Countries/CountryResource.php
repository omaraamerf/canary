<?php

namespace App\Filament\Resources\Countries;

use App\Filament\Navigation\AdminGroup;
use App\Filament\Resources\Countries\Pages\CreateCountry;
use App\Filament\Resources\Countries\Pages\EditCountry;
use App\Filament\Resources\Countries\Pages\ListCountries;
use App\Models\Country;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class CountryResource extends Resource
{
    protected static ?string $model = Country::class;

    protected static string|BackedEnum|null $navigationIcon = 'lucide-globe';

    protected static string|UnitEnum|null $navigationGroup = AdminGroup::Setup;

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string { return __('دولة'); }

    public static function getPluralModelLabel(): string { return __('الدول'); }

    public static function getNavigationLabel(): string { return __('الدول'); }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label(__('الاسم بالعربية'))->required()->maxLength(120),
            TextInput::make('name_en')->label(__('الاسم بالإنجليزية'))->maxLength(120),
            TextInput::make('code')
                ->label(__('رمز الدولة (ISO)'))
                ->required()
                ->length(2)
                ->unique(ignoreRecord: true)
                ->dehydrateStateUsing(fn (string $state): string => strtoupper($state)),
            TextInput::make('sort_order')->label(__('الترتيب'))->numeric()->default(0)->required(),
            Toggle::make('active')->label(__('نشطة'))->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label(__('الدولة'))->searchable(),
                TextColumn::make('name_en')->label(__('بالإنجليزية'))->searchable(),
                TextColumn::make('code')->label(__('الرمز'))->badge(),
                TextColumn::make('regions_count')->label(__('المناطق'))->counts('regions')->sortable(),
                IconColumn::make('active')->label(__('نشطة'))->boolean(),
                TextColumn::make('sort_order')->label(__('الترتيب'))->sortable(),
            ])
            ->recordActions([EditAction::make()])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCountries::route('/'),
            'create' => CreateCountry::route('/create'),
            'edit' => EditCountry::route('/{record}/edit'),
        ];
    }
}
