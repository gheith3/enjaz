<?php

namespace App\Filament\Resources\Organizations\RelationManagers;

use App\Filament\Support\MediaSlot;
use App\Filament\Support\Translatable;
use App\Models\OrganizationLeader;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

/** Presidents over time, current leadership and board. */
class LeadersRelationManager extends RelationManager
{
    protected static string $relationship = 'leaders';

    protected static ?string $title = 'القيادات والرؤساء';

    protected static ?string $modelLabel = 'شخص';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('kind')->label('القائمة')->options(OrganizationLeader::KINDS)->required()->default('president'),
            Translatable::text('name', 'الاسم'),
            Translatable::text('title', 'اللقب (مثل: معالي، د.)', required: false),
            Translatable::text('role', 'المنصب', required: false),
            Grid::make(3)->schema([
                TextInput::make('from_year')->label('من سنة')->numeric(),
                TextInput::make('to_year')->label('إلى سنة')->numeric(),
                Toggle::make('is_current')->label('الحالي')->inline(false),
            ]),
            Translatable::textarea('note', 'نبذة'),
            MediaSlot::make('photo', 'الصورة (PNG مفرّغ إن أمكن)', focal: false),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->reorderable('sort_order')->defaultSort('sort_order')->paginated(false)
            ->columns([
                SpatieMediaLibraryImageColumn::make('photo')->collection('photo')->label('')->circular()->height(36),
                TextColumn::make('name')->label('الاسم'),
                TextColumn::make('kind')->label('القائمة')->formatStateUsing(fn (string $state) => OrganizationLeader::KINDS[$state] ?? $state)->badge(),
                TextColumn::make('from_year')->label('الفترة')->state(fn (OrganizationLeader $r) => trim(($r->from_year ?? '').' – '.($r->to_year ?? ''), ' –')),
                IconColumn::make('is_current')->label('الحالي')->boolean(),
            ])
            ->filters([SelectFilter::make('kind')->label('القائمة')->options(OrganizationLeader::KINDS)])
            ->headerActions([CreateAction::make()])->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
