<?php

namespace App\Filament\Shared\Schemas;

use App\Enums\Currency;
use App\Enums\SettingKey;
use App\Models\Bird;
use App\Models\Breed;
use App\Models\Region;
use App\Models\Setting;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;

/**
 * The bird listing form for both panels. Editing shows every part at once in sections;
 * creating walks through the same parts as wizard steps (see steps()) and ends on a summary.
 */
class BirdForm
{
    public static function configure(Schema $schema, bool $admin): Schema
    {
        return $schema->components([
            Section::make(__('بيانات الطائر'))->schema(self::details())->columns(2),
            Section::make(__('الصور والفيديو'))->schema(self::media()),
            Section::make(__('السعر والتسليم'))->schema(self::pricing($admin))->columns(2),
            Section::make(__('المراجعة'))->schema(self::approval())->visible($admin),
        ]);
    }

    /** @return list<Step> */
    public static function steps(bool $admin): array
    {
        return [
            Step::make(__('بيانات الطائر'))->icon('lucide-bird')->schema(self::details())->columns(2),
            Step::make(__('الصور والفيديو'))->icon('lucide-images')->schema(self::media()),
            Step::make(__('السعر والتسليم'))->icon('lucide-banknote')->schema(self::pricing($admin))->columns(2),
            Step::make(__('المراجعة'))
                ->icon('lucide-list-checks')
                ->description(__('تأكد من البيانات قبل الحفظ.'))
                ->schema([
                    Grid::make(['default' => 1, 'md' => 2])->schema(self::summary()),
                    ...($admin ? self::approval() : [self::publishingNote()]),
                ]),
        ];
    }

    private static function details(): array
    {
        return [
            TextInput::make('title')->label(__('عنوان الإعلان'))->required()->maxLength(180),
            Select::make('breed_id')
                ->label(__('السلالة'))
                ->relationship('breed', 'name')
                ->getOptionLabelFromRecordUsing(fn (Breed $record): string => $record->localized_name)
                ->searchable()
                ->preload()
                ->required(),
            Select::make('sex')->label(__('الجنس'))->options(self::sexOptions())->required(),
            TextInput::make('hatch_year')->label(__('سنة الفقس'))->numeric()->minValue(2000)->maxValue(now()->year),
            TextInput::make('color')->label(__('اللون'))->required()->maxLength(80),
            Select::make('molt_status')->label(__('مرحلة الريش'))->options([
                'ready' => __('جاهز'),
                'young' => __('فرخ'),
                'molting' => __('غيار ريش'),
            ]),
            Toggle::make('breeding_ready')->label(__('جاهز للتزاوج')),
            Select::make('singing_status')->label(__('التغريد'))->options([
                'singing' => __('يغرد'),
                'not_singing' => __('لا يغرد'),
                'young' => __('فرخ'),
                'female' => __('أنثى'),
            ]),
            TextInput::make('ring_number')->label(__('رقم الحلقة'))->maxLength(80),
            Textarea::make('description')->label(__('الوصف'))->rows(5)->columnSpanFull(),
        ];
    }

    private static function media(): array
    {
        return [
            FileUpload::make('images')
                ->label(__('صور جديدة'))
                ->helperText(__('اسحب الصور إلى هنا أو اضغط للاختيار. الصورة الأولى تظهر في بطاقة الإعلان.'))
                ->multiple()
                ->reorderable()
                ->imagePreviewHeight('140')
                ->panelLayout('grid')
                ->storeFiles(false)
                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/bmp', 'image/avif', 'image/heic', 'image/heif'])
                ->maxFiles((int) config('services.cloudinary.max_images', 8))
                ->maxSize((int) config('services.cloudinary.image_max_kb', 10240))
                ->required(fn (?Bird $record): bool => $record === null),
            FileUpload::make('videos')
                ->label(__('فيديوهات جديدة'))
                ->multiple()
                ->storeFiles(false)
                ->acceptedFileTypes(['video/mp4', 'video/quicktime', 'video/webm'])
                ->maxFiles((int) config('services.cloudinary.max_videos', 3))
                ->maxSize((int) config('services.cloudinary.video_max_kb', 51200)),
            CheckboxList::make('remove_media')
                ->label(__('حذف صور أو فيديوهات حالية'))
                ->helperText(__('حدد الفيديو المطلوب حذفه ثم احفظ التعديلات؛ لا يلزم رفع فيديو جديد بدلاً منه.'))
                ->options(fn (?Bird $record): array => $record?->media()
                    ->get()
                    ->mapWithKeys(fn ($media) => [
                        $media->id => ($media->type === 'image' ? __('صورة') : __('فيديو')).' #'.$media->id,
                    ])->all() ?? [])
                ->visible(fn (?Bird $record): bool => $record?->exists === true)
                ->columns(2),
        ];
    }

    private static function pricing(bool $admin): array
    {
        return [
            TextInput::make('price')->label(__('السعر'))->numeric()->minValue(0)->required(),
            Select::make('currency')
                ->label(__('العملة'))
                ->options(Currency::options())
                ->default(fn (): string => self::lastUsedCurrency())
                ->required(),
            Select::make('region_id')->label(__('المحافظة'))->options(fn (): array => Region::groupedOptions())->searchable()->required()->visible($admin),
            TextInput::make('city')->label(__('المدينة'))->required()->maxLength(120),
            Select::make('delivery_type')->label(__('طريقة التسليم'))->options(self::deliveryOptions())->required(),
            Select::make('status')->label(__('الحالة'))->options([
                'available' => __('متاح'),
                'reserved' => __('محجوز'),
                'sold' => __('مباع'),
            ])->default('available')->required()->visible($admin),
            Toggle::make('featured')->label(__('إعلان مميز'))->visible($admin),
        ];
    }

    private static function approval(): array
    {
        return [
            Select::make('approval_status')->label(__('حالة المراجعة'))->options([
                'pending' => __('بانتظار المراجعة'),
                'approved' => __('منشور'),
                'rejected' => __('مرفوض'),
            ])->default('approved')->required(),
            Textarea::make('rejection_reason')->label(__('سبب الرفض')),
        ];
    }

    /** Read-only recap of the earlier steps. */
    private static function summary(): array
    {
        return [
            TextEntry::make('summary_title')->label(__('عنوان الإعلان'))
                ->state(fn (Get $get): string => $get('title') ?: '—'),
            TextEntry::make('summary_breed')->label(__('السلالة'))
                ->state(fn (Get $get): string => Breed::find($get('breed_id'))?->localized_name ?? '—'),
            TextEntry::make('summary_sex')->label(__('الجنس'))
                ->state(fn (Get $get): string => self::sexOptions()[$get('sex')] ?? '—'),
            TextEntry::make('summary_price')->label(__('السعر'))
                ->state(fn (Get $get): string => filled($get('price')) ? number_format((float) $get('price')).' '.Currency::labelFor($get('currency')) : '—'),
            TextEntry::make('summary_delivery')->label(__('طريقة التسليم'))
                ->state(fn (Get $get): string => collect([self::deliveryOptions()[$get('delivery_type')] ?? null, $get('city')])->filter()->implode('، ') ?: '—'),
            TextEntry::make('summary_media')->label(__('الصور والفيديو'))
                ->state(fn (Get $get): string => __(':images صور و:videos فيديو', ['images' => count($get('images') ?? []), 'videos' => count($get('videos') ?? [])])),
        ];
    }

    private static function publishingNote(): TextEntry
    {
        return TextEntry::make('publishing_note')
            ->hiddenLabel()
            ->icon('lucide-info')
            ->color('gray')
            ->state(fn (): string => Setting::boolean(SettingKey::ListingApprovalRequired->value, true)
                ? __('تراجع الإدارة الإعلان قبل ظهوره في الموقع، ويظهر وضعه في قائمة طيوري.')
                : __('يظهر الإعلان في الموقع فور الحفظ.'));
    }

    private static function sexOptions(): array
    {
        return [
            'male' => __('ذكر'),
            'female' => __('أنثى'),
            'unknown' => __('غير محدد'),
        ];
    }

    private static function deliveryOptions(): array
    {
        return [
            'pickup' => __('استلام شخصي'),
            'delivery' => __('توصيل'),
            'agreement' => __('بالاتفاق'),
        ];
    }

    // New listings start in the currency the user priced their previous one in.
    private static function lastUsedCurrency(): string
    {
        $last = auth()->user()?->birds()->latest()->value('currency');

        return Currency::tryFrom((string) $last)?->value ?? Currency::USD->value;
    }
}
