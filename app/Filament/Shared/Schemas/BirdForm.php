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

class BirdForm
{
    public static function configure(Schema $schema, bool $admin): Schema
    {
        return $schema->components([
            Section::make('بيانات الطائر')
                ->schema([
                    TextInput::make('title')->label('عنوان الإعلان')->required()->maxLength(180),
                    Select::make('breed_id')->label('السلالة')->relationship('breed', 'name')->searchable()->preload()->required(),
                    Select::make('sex')->label('الجنس')->options([
                        'male' => 'ذكر',
                        'female' => 'أنثى',
                        'unknown' => 'غير محدد',
                    ])->required(),
                    TextInput::make('hatch_year')->label('سنة الفقس')->numeric()->minValue(2000)->maxValue(now()->year),
                    TextInput::make('color')->label('اللون')->required()->maxLength(80),
                    Select::make('molt_status')->label('مرحلة الريش')->options([
                        'ready' => 'جاهز',
                        'young' => 'فرخ',
                        'molting' => 'غيار ريش',
                    ]),
                    Toggle::make('breeding_ready')->label('جاهز للتزاوج'),
                    Select::make('singing_status')->label('التغريد')->options([
                        'singing' => 'يغرد',
                        'not_singing' => 'لا يغرد',
                        'young' => 'فرخ',
                        'female' => 'أنثى',
                    ]),
                    TextInput::make('ring_number')->label('رقم الحلقة')->maxLength(80),
                    Textarea::make('description')->label('الوصف')->rows(5)->columnSpanFull(),
                ])->columns(2),

            Section::make('السعر والموقع')
                ->schema([
                    TextInput::make('price')->label('السعر')->numeric()->minValue(0)->required(),
                    TextInput::make('currency')->label('العملة')->default('SAR')->required()->maxLength(3)->visible($admin),
                    Select::make('region_id')->label('المحافظة')->relationship('region', 'name')->searchable()->preload()->required()->visible($admin),
                    TextInput::make('city')->label('المدينة')->required()->maxLength(120),
                    Select::make('delivery_type')->label('طريقة التسليم')->options([
                        'pickup' => 'استلام شخصي',
                        'delivery' => 'توصيل',
                        'agreement' => 'بالاتفاق',
                    ])->required(),
                    Select::make('status')->label('الحالة')->options([
                        'available' => 'متاح',
                        'reserved' => 'محجوز',
                        'sold' => 'مباع',
                    ])->default('available')->required()->visible($admin),
                    Toggle::make('featured')->label('إعلان مميز')->visible($admin),
                ])->columns(2),

            Section::make('الصور والفيديو')
                ->schema([
                    FileUpload::make('images')
                        ->label('صور جديدة')
                        ->multiple()
                        ->storeFiles(false)
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/bmp', 'image/avif', 'image/heic', 'image/heif'])
                        ->maxFiles((int) config('services.cloudinary.max_images', 8))
                        ->maxSize((int) config('services.cloudinary.image_max_kb', 10240))
                        ->required(fn (?Bird $record): bool => $record === null),
                    FileUpload::make('videos')
                        ->label('فيديوهات جديدة')
                        ->multiple()
                        ->storeFiles(false)
                        ->acceptedFileTypes(['video/mp4', 'video/quicktime', 'video/webm'])
                        ->maxFiles((int) config('services.cloudinary.max_videos', 3))
                        ->maxSize((int) config('services.cloudinary.video_max_kb', 51200)),
                    CheckboxList::make('remove_media')
                        ->label('حذف صور أو فيديوهات حالية')
                        ->helperText('حدد الفيديو المطلوب حذفه ثم احفظ التعديلات؛ لا يلزم رفع فيديو جديد بدلاً منه.')
                        ->options(fn (?Bird $record): array => $record?->media()
                            ->get()
                            ->mapWithKeys(fn ($media) => [
                                $media->id => ($media->type === 'image' ? 'صورة' : 'فيديو').' #'.$media->id,
                            ])->all() ?? [])
                        ->visible(fn (?Bird $record): bool => $record?->exists === true)
                        ->columns(2),
                ]),

            Section::make('المراجعة')
                ->schema([
                    Select::make('approval_status')->label('حالة المراجعة')->options([
                        'pending' => 'بانتظار المراجعة',
                        'approved' => 'منشور',
                        'rejected' => 'مرفوض',
                    ])->default('approved')->required(),
                    Textarea::make('rejection_reason')->label('سبب الرفض'),
                ])->visible($admin),
        ]);
    }
}
