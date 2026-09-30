<?php

namespace App\Filament\Shared\Schemas;

use App\Models\Bird;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use App\Models\Breed;

class BirdForm
{
    public static function configure(Schema $schema, bool $admin): Schema
    {
        return $schema->components([
            Section::make(__('بيانات الطائر'))
                ->schema([
                    TextInput::make('title')->label(__('عنوان الإعلان'))->required()->maxLength(180),
                    Select::make('breed_id')
                        ->label(__('السلالة'))
                        ->relationship('breed', 'name')
                        ->getOptionLabelFromRecordUsing(fn (Breed $record): string => $record->localized_name)
                        ->searchable()
                        ->preload()
                        ->required(),
                    Select::make('sex')->label(__('الجنس'))->options([
                        'male' => __('ذكر'),
                        'female' => __('أنثى'),
                        'unknown' => __('غير محدد'),
                    ])->required(),
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
                ])->columns(2),

            Section::make(__('السعر والموقع'))
                ->schema([
                    TextInput::make('price')->label(__('السعر'))->numeric()->minValue(0)->required(),
                    TextInput::make('currency')->label(__('العملة'))->default('SAR')->required()->maxLength(3)->visible($admin),
                    Select::make('region_id')->label(__('المحافظة'))->relationship('region', 'name')->searchable()->preload()->required()->visible($admin),
                    TextInput::make('city')->label(__('المدينة'))->required()->maxLength(120),
                    Select::make('delivery_type')->label(__('طريقة التسليم'))->options([
                        'pickup' => __('استلام شخصي'),
                        'delivery' => __('توصيل'),
                        'agreement' => __('بالاتفاق'),
                    ])->required(),
                    Select::make('status')->label(__('الحالة'))->options([
                        'available' => __('متاح'),
                        'reserved' => __('محجوز'),
                        'sold' => __('مباع'),
                    ])->default('available')->required()->visible($admin),
                    Toggle::make('featured')->label(__('إعلان مميز'))->visible($admin),
                ])->columns(2),

            Section::make(__('الصور والفيديو'))
                ->schema([
                    FileUpload::make('images')
                        ->label(__('صور جديدة'))
                        ->multiple()
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
                ]),

            Section::make(__('المراجعة'))
                ->schema([
                    Select::make('approval_status')->label(__('حالة المراجعة'))->options([
                        'pending' => __('بانتظار المراجعة'),
                        'approved' => __('منشور'),
                        'rejected' => __('مرفوض'),
                    ])->default('approved')->required(),
                    Textarea::make('rejection_reason')->label(__('سبب الرفض')),
                ])->visible($admin),
        ]);
    }
}
