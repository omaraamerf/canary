<?php

namespace App\Filament\Seller\Pages;

use App\Models\Region;
use App\Models\User;
use App\Services\SellerService;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use SensitiveParameter;

class EditProfile extends BaseEditProfile
{
    public static function getLabel(): string
    {
        return 'الملف الشخصي';
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        /** @var User $seller */
        $seller = $this->getUser();
        $seller->loadMissing('sellerProfile');

        return [
            ...$data,
            'phone' => $seller->phone,
            'display_name' => $seller->sellerProfile?->display_name,
            'region_id' => $seller->sellerProfile?->region_id ?? $seller->region_id,
            'bio' => $seller->sellerProfile?->bio,
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('بيانات الحساب')
                ->schema([
                    $this->getNameFormComponent()->label('الاسم'),
                    $this->getEmailFormComponent()->label('البريد الإلكتروني'),
                    TextInput::make('phone')
                        ->label('رقم الهاتف')
                        ->tel()
                        ->required()
                        ->maxLength(30),
                ])
                ->columns(2),
            Section::make('بيانات البائع')
                ->description('هذه البيانات تمثل حسابك كبائع وتظهر مع إعلاناتك.')
                ->schema([
                    TextInput::make('display_name')
                        ->label('اسم المتجر أو المربي')
                        ->required()
                        ->maxLength(140),
                    Select::make('region_id')
                        ->label('المنطقة')
                        ->options(fn (): array => Region::query()
                            ->where('active', true)
                            ->orderBy('sort_order')
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->all())
                        ->searchable()
                        ->required(),
                    Textarea::make('bio')
                        ->label('نبذة')
                        ->rows(4)
                        ->maxLength(1200)
                        ->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('تغيير كلمة المرور')
                ->schema([
                    $this->getPasswordFormComponent()->label('كلمة المرور الجديدة'),
                    $this->getPasswordConfirmationFormComponent()->label('تأكيد كلمة المرور'),
                    $this->getCurrentPasswordFormComponent()->label('كلمة المرور الحالية'),
                ])
                ->columns(2)
                ->collapsed(),
        ]);
    }

    protected function handleRecordUpdate(Model $record, #[SensitiveParameter] array $data): Model
    {
        $record = parent::handleRecordUpdate(
            $record,
            Arr::only($data, ['name', 'email', 'password']),
        );

        /** @var User $record */
        app(SellerService::class)->updateDetails(
            $record,
            Arr::only($data, ['phone', 'display_name', 'region_id', 'bio']),
        );

        return $record;
    }
}
