<?php

namespace App\Filament\Resources\Organizations\RelationManagers;

use App\Filament\Support\Translatable;
use App\Models\OrganizationVideo;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** YouTube videos of the library / gallery. Paste a YouTube link or id. */
class VideosRelationManager extends RelationManager
{
    protected static string $relationship = 'videos';

    protected static ?string $title = 'الفيديوهات';

    protected static ?string $modelLabel = 'فيديو';

    public function isReadOnly(): bool
    {
        return false;
    }

    public static function youtubeId(?string $value): string
    {
        $value = trim((string) $value);
        preg_match('~(?:youtu\.be/|v=|embed/|shorts/)([A-Za-z0-9_-]{11})~', $value, $m);

        return $m[1] ?? $value;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('youtube_id')->label('رابط يوتيوب أو المعرّف')->required()
                ->dehydrateStateUsing(fn (?string $state) => self::youtubeId($state))
                ->rule('regex:~^(https?://\S+|[A-Za-z0-9_-]{11})$~')->extraInputAttributes(['dir' => 'ltr']),
            Translatable::text('title', 'العنوان'),
            Grid::make(3)->schema([
                TextInput::make('year')->label('السنة'),
                TextInput::make('duration')->label('المدة (مثل 3:24)'),
                TextInput::make('era')->label('الحقبة (للاتحادات)'),
            ]),
            Translatable::text('note', 'ملاحظة', required: false),
            Toggle::make('is_official')->label('من القناة الرسمية'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->reorderable('sort_order')->defaultSort('sort_order')->paginated(false)
            ->columns([
                ImageColumn::make('thumb')->label('')->state(fn (OrganizationVideo $r) => $r->thumbnail())->height(40),
                TextColumn::make('title')->label('العنوان')->wrap(),
                TextColumn::make('year')->label('السنة'),
                IconColumn::make('is_official')->label('رسمي')->boolean(),
            ])
            ->headerActions([CreateAction::make()])->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
