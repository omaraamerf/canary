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
                    ->label('القسم')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('title')
                    ->label('العنوان')
                    ->required(),
                Textarea::make('summary')
                    ->label('الملخص')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('content')
                    ->label('المحتوى')
                    ->required()
                    ->rows(12)
                    ->columnSpanFull(),
                Textarea::make('featured_image')
                    ->label('رابط الصورة البارزة')
                    ->columnSpanFull(),
                TextInput::make('tags')
                    ->label('الوسوم')
                    ->helperText('افصل الوسوم بفاصلة.'),
                Select::make('status')
                    ->label('الحالة')
                    ->options([
                        ArticleStatus::Draft->value => 'مسودة',
                        ArticleStatus::Published->value => 'منشور',
                        ArticleStatus::Archived->value => 'مؤرشف',
                    ])
                    ->required()
                    ->default(ArticleStatus::Draft->value),
                DateTimePicker::make('published_at')->label('تاريخ النشر'),
            ]);
    }
}
