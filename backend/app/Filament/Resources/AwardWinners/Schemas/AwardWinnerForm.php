<?php

namespace App\Filament\Resources\AwardWinners\Schemas;

use App\Filament\Support\MediaSlot;
use App\Filament\Support\Translatable;
use App\Models\AwardCategory;
use Closure;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;

/** Fields of a winner, shared by the winners page and the edition's winners list. */
class AwardWinnerForm
{
    /** @param  Closure(Get): ?int  $awardId */
    public static function fields(Closure $awardId): array
    {
        return [
            Grid::make(3)->schema([
                Select::make('award_category_id')->label('الفرع / الفئة')
                    ->options(fn (Get $get) => AwardCategory::query()->where('award_id', $awardId($get))->whereIn('kind', ['branch', 'category'])
                        ->orderBy('sort_order')->get()->mapWithKeys(fn ($c) => [$c->id => $c->name])),
                Select::make('rank')->label('المركز')->options([1 => 'الأول', 2 => 'الثاني', 3 => 'الثالث', 0 => 'تقديرية / خاصة'])->placeholder('بلا ترتيب'),
                Select::make('gender')->label('الجنس')->options(['m' => 'ذكر', 'f' => 'أنثى']),
            ]),
            Translatable::text('name', 'الاسم (شخص أو جهة)', required: false),
            Repeater::make('team')->label('الفريق (للفرق والمشاريع المشتركة)')->defaultItems(0)->addActionLabel('إضافة اسم')
                ->simple(TextInput::make('ar')->label('الاسم')->required()),
            Translatable::text('work', 'العمل / المشروع الفائز', required: false),
            Translatable::text('field', 'المجال', required: false),
            Translatable::text('award_label', 'اسم الجائزة الخاصة (مثل: الجائزة التقديرية)', required: false),
            Translatable::textarea('why', 'نظير ماذا فاز؟', rows: 4),
            Translatable::text('place', 'المكان / الولاية', required: false),
            Select::make('achiever_id')->label('ملف المنجز في الموقع')->relationship('achiever', 'name')->searchable()->preload()
                ->getOptionLabelFromRecordUsing(fn ($record) => $record->name),
            Grid::make(2)->schema([
                TextInput::make('source_title')->label('المصدر'),
                TextInput::make('source_url')->label('رابط المصدر')->url()->extraInputAttributes(['dir' => 'ltr']),
            ]),
            Grid::make(2)->schema([
                Toggle::make('is_group')->label('صورة جماعية (فرقة)'),
                Toggle::make('is_withheld')->label('المركز محجوب'),
            ]),
            Section::make('الصور')->collapsible()->schema([
                MediaSlot::make('photo', 'الصورة'),
                MediaSlot::make('logo', 'شعار الجهة', focal: false),
            ]),
        ];
    }
}
