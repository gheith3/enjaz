<?php

namespace App\Filament\Resources\Pages\Schemas;

use App\Filament\Support\Translatable;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('العنوان ومحركات البحث')->columnSpanFull()->schema([
                Translatable::text('title', 'عنوان الصفحة'),
                Translatable::textarea('meta_description', 'الوصف (يظهر في نتائج البحث وعند المشاركة)'),
                SpatieMediaLibraryFileUpload::make('og_image')->label('صورة المشاركة (1200×630)')
                    ->collection('og_image')->image()->imageEditor(),
                Toggle::make('noindex')->label('إخفاء الصفحة عن محركات البحث'),
            ]),
            Section::make('رأس الصفحة')->description('النصوص الظاهرة في أعلى الصفحة، إن كان تصميمها يعرضها.')
                ->columnSpanFull()->collapsible()->schema([
                    Translatable::text('hero_eyebrow', 'العنوان الصغير', required: false),
                    Translatable::text('hero_title', 'العنوان الرئيسي', required: false),
                    Translatable::textarea('hero_lead', 'النص التعريفي'),
                    Translatable::text('hero_cta_label', 'نص الزر', required: false),
                    TextInput::make('hero_cta_url')->label('رابط الزر')->maxLength(255)
                        ->extraInputAttributes(['dir' => 'ltr']),
                ]),
        ]);
    }
}
