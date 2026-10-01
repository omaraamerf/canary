<?php

namespace App\Filament\Resources\Articles\Schemas;

use App\Enums\ArticleStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('category_id')
                    ->label(__('القسم'))
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('title')
                    ->label(__('العنوان'))
                    ->required(),
                Textarea::make('summary')
                    ->label(__('الملخص'))
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('content')
                    ->label(__('المحتوى'))
                    ->required()
                    ->helperText(__('اترك سطرًا فارغًا بين الفقرات. «## » لعنوان، و«### » لعنوان فرعي، و«- » لعنصر قائمة، و«> » لملاحظة بارزة، و**نص** للخط العريض. تظهر العناوين في فهرس المقال.'))
                    ->rows(12)
                    ->columnSpanFull(),
                Textarea::make('featured_image')
                    ->label(__('رابط الصورة البارزة'))
                    ->columnSpanFull(),
                TextInput::make('tags')
                    ->label(__('الوسوم'))
                    ->helperText(__('افصل الوسوم بفاصلة.')),
                Select::make('status')
                    ->label(__('الحالة'))
                    ->options([
                        ArticleStatus::Draft->value => __('مسودة'),
                        ArticleStatus::Published->value => __('منشور'),
                        ArticleStatus::Archived->value => __('مؤرشف'),
                    ])
                    ->required()
                    ->default(ArticleStatus::Draft->value),
                DateTimePicker::make('published_at')->label(__('تاريخ النشر')),
            ]);
    }
}
