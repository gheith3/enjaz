<?php

namespace App\Filament\Resources\ContactMessages;

use App\Filament\Resources\ContactMessages\Pages\ListContactMessages;
use App\Filament\Resources\ContactMessages\Pages\ViewContactMessage;
use App\Models\ContactMessage;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use UnitEnum;

/** رسائل التواصل: messages of the contact form (read only; opening one marks it read). */
class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedInbox;

    protected static string|UnitEnum|null $navigationGroup = 'المحتوى';

    protected static ?int $navigationSort = 20;

    protected static ?string $modelLabel = 'رسالة';

    protected static ?string $pluralModelLabel = 'رسائل التواصل';

    public static function getNavigationBadge(): ?string
    {
        $n = ContactMessage::query()->whereNull('read_at')->count();

        return $n ? (string) $n : null;
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->columnSpanFull()->schema([
                TextEntry::make('topic')->label('الموضوع'),
                TextEntry::make('created_at')->label('التاريخ')->dateTime('Y-m-d H:i'),
                TextEntry::make('name')->label('الاسم'),
                TextEntry::make('email')->label('البريد')->url(fn (ContactMessage $r) => 'mailto:'.$r->email)->extraAttributes(['dir' => 'ltr']),
                TextEntry::make('phone')->label('الهاتف')->placeholder('—')->extraAttributes(['dir' => 'ltr']),
                TextEntry::make('attachment_name')->label('المرفق')->placeholder('—'),
                TextEntry::make('message')->label('الرسالة')->columnSpanFull()->prose(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                IconColumn::make('read_at')->label('')->state(fn (ContactMessage $r) => ! $r->isRead())->boolean()
                    ->trueIcon('heroicon-s-envelope')->falseIcon('heroicon-o-envelope-open')->trueColor('primary')->falseColor('gray'),
                TextColumn::make('name')->label('الاسم')->searchable()->weight(fn (ContactMessage $r) => $r->isRead() ? null : 'bold'),
                TextColumn::make('topic')->label('الموضوع')->badge(),
                TextColumn::make('message')->label('الرسالة')->limit(70)->searchable(),
                TextColumn::make('created_at')->label('التاريخ')->dateTime('Y-m-d H:i')->sortable(),
            ])
            ->filters([TernaryFilter::make('read_at')->label('الحالة')->nullable()->trueLabel('مقروءة')->falseLabel('غير مقروءة')])
            ->recordActions([ViewAction::make(), self::downloadAction(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }

    public static function downloadAction(): Action
    {
        return Action::make('download')->label('المرفق')->icon('heroicon-o-paper-clip')->color('gray')
            ->visible(fn (ContactMessage $record) => (bool) $record->attachment_path)
            ->action(fn (ContactMessage $record) => Storage::disk(ContactMessage::DISK)->download($record->attachment_path, $record->attachment_name));
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContactMessages::route('/'),
            'view' => ViewContactMessage::route('/{record}'),
        ];
    }
}
