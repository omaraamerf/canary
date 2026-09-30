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
        return __('الملف الشخصي');
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
            Section::make(__('بيانات الحساب'))
                ->schema([
                    $this->getNameFormComponent()->label(__('الاسم')),
                    $this->getEmailFormComponent()->label(__('البريد الإلكتروني')),
                    TextInput::make('phone')
                        ->label(__('رقم الهاتف'))
                        ->tel()
                        ->required()
                        ->maxLength(30),
                ])
                ->columns(2),
            Section::make(__('بيانات البائع'))
                ->description(__('هذه البيانات تمثل حسابك كبائع وتظهر مع إعلاناتك.'))
                ->schema([
                    TextInput::make('display_name')
                        ->label(__('اسم المتجر أو المربي'))
                        ->required()
                        ->maxLength(140),
                    Select::make('region_id')
                        ->label(__('المنطقة'))
                        ->options(fn (): array => Region::query()
                            ->where('active', true)
                            ->orderBy('sort_order')
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->all())
                        ->searchable()
                        ->required(),
                    Textarea::make('bio')
                        ->label(__('نبذة'))
                        ->rows(4)
                        ->maxLength(1200)
                        ->columnSpanFull(),
                ])
                ->columns(2),
            Section::make(__('تغيير كلمة المرور'))
                ->schema([
                    $this->getPasswordFormComponent()->label(__('كلمة المرور الجديدة')),
                    $this->getPasswordConfirmationFormComponent()->label(__('تأكيد كلمة المرور')),
                    $this->getCurrentPasswordFormComponent()->label(__('كلمة المرور الحالية')),
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
