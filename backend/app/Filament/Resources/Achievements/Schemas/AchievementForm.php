<?php

namespace App\Filament\Resources\Achievements\Schemas;

use App\Filament\Support\MediaSlot;
use App\Filament\Support\Publishing;
use App\Filament\Support\Translatable;
use App\Models\Achievement;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class AchievementForm
{
    private const TOOLBAR = [['bold', 'italic', 'link'], ['h3', 'bulletList', 'orderedList', 'blockquote'], ['undo', 'redo']];

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make()->columnSpanFull()->persistTabInQueryString()->tabs([
                Tab::make('الأساسية')->icon('heroicon-o-trophy')->columns(2)->schema([
                    Translatable::text('title', 'عنوان الإنجاز'),
                    TextInput::make('slug')->label('المعرّف في الرابط')->required()->unique(ignoreRecord: true)
                        ->alphaDash()->maxLength(100)->extraInputAttributes(['dir' => 'ltr']),
                    Repeater::make('achieverLinks')->label('صاحب / أصحاب الإنجاز')->relationship()->orderColumn('sort_order')
                        ->columnSpanFull()->columns(2)->defaultItems(request()->integer('organization') ? 0 : 1)->addActionLabel('إضافة منجز')
                        ->default(fn () => request()->integer('achiever') ? [['achiever_id' => request()->integer('achiever'), 'is_primary' => true]] : [])
                        ->schema([
                            Select::make('achiever_id')->label('المنجز')->relationship('achiever', 'name')->getOptionLabelFromRecordUsing(fn ($record) => $record->name)->searchable()->preload()->required()
                                ->distinct()->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                            Toggle::make('is_primary')->label('أساسي (يظهر اسمه على الإنجاز)')->default(true)->inline(false),
                        ]),
                    Select::make('organizations')->label('المؤسسات (لإنجاز المؤسسة نفسها أو لربطه بها)')->relationship('organizations', 'name')
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)->multiple()->preload()->searchable()->columnSpanFull()
                        ->default(fn () => request()->integer('organization') ? [request()->integer('organization')] : [])
                        ->requiredWithout('achieverLinks')->validationMessages(['required_without' => 'اختر منجزًا أو مؤسسة على الأقل.']),
                    Select::make('field_id')->label('المجال')->relationship('field', 'name')->getOptionLabelFromRecordUsing(fn ($record) => $record->name)->preload()->required(),
                    Select::make('scope_id')->label('المستوى')->relationship('scope', 'name')->getOptionLabelFromRecordUsing(fn ($record) => $record->name)->preload()->required(),
                    Translatable::text('discipline', 'التخصص (مثل: كرة القدم، الرواية). فارغ = اسم المجال', required: false),
                    Translatable::text('profile_tag', 'وسم الإنجاز في صفحة المنجز (مثل: جوائز فردية)', required: false),
                    DatePicker::make('achieved_at')->label('تاريخ الإنجاز')->required()->native(false),
                    Select::make('date_precision')->label('يُعرض التاريخ بـ')->options(Achievement::PRECISIONS)->required()->default('day'),
                    Translatable::text('country', 'الدولة', required: false),
                    Translatable::textarea('excerpt', 'الملخص (يظهر في البطاقات)', required: true, rows: 3),
                    Toggle::make('is_featured')->label('في «لوحة الشرف»')->helperText('يظهر أعلى صفحة الإنجازات (الأحدث بينها).'),
                    ...Publishing::fields(),
                ]),
                Tab::make('القصة')->icon('heroicon-o-document-text')->schema([
                    Grid::make(2)->schema([
                        RichEditor::make('body.ar')->label('نص القصة')->toolbarButtons(self::TOOLBAR),
                        RichEditor::make('body.en')->label('نص القصة (English)')->toolbarButtons(self::TOOLBAR)->extraInputAttributes(['dir' => 'ltr']),
                    ]),
                    Repeater::make('highlights')->label('أبرز ما في الإنجاز')->addActionLabel('إضافة نقطة')->defaultItems(0)->maxItems(6)->collapsible()
                        ->schema([
                            Translatable::text('lead', 'البداية بخط عريض (مثل: أول كاتبة عربية)'),
                            Translatable::text('text', 'التكملة', required: false),
                        ]),
                    Section::make('اقتباس داخل القصة')->collapsible()->schema([
                        Translatable::textarea('pull_quote', 'الاقتباس'),
                        Translatable::text('pull_quote_by', 'القائل', required: false),
                        Translatable::text('pull_quote_role', 'صفته', required: false),
                        Translatable::text('pull_quote_note', 'ملاحظة (مثل: ترجمة عن تصريحها)', required: false),
                        TextInput::make('pull_quote_after')->label('يظهر بعد الفقرة رقم')->numeric()->minValue(1)->maxValue(50)->default(2),
                    ]),
                    TextInput::make('video_url')->label('رابط فيديو (اختياري)')->url()->extraInputAttributes(['dir' => 'ltr']),
                ]),
                Tab::make('الصورة')->icon('heroicon-o-photo')->schema([
                    MediaSlot::make('cover', 'صورة الإنجاز', 'تظهر أعلى صفحة الإنجاز وفي البطاقات (تُقص حسب المكان).'),
                ]),
                Tab::make('الظهور في الرئيسية')->icon('heroicon-o-home')->columns(2)->schema([
                    Toggle::make('show_on_home')->label('في «أحدث الإنجازات» بالرئيسية')->live(),
                    Select::make('home_slot')->label('المكان')->options(['feature' => 'الإنجاز الكبير', 'card' => 'بطاقة', 'row' => 'سطر'])
                        ->visible(fn (Get $get) => (bool) $get('show_on_home')),
                    Translatable::text('home_title', 'العنوان المختصر في الرئيسية (فارغ = عنوان الإنجاز)', required: false)
                        ->visible(fn (Get $get) => (bool) $get('show_on_home')),
                ]),
            ]),
        ]);
    }
}
