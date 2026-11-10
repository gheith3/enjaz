<?php

namespace App\Filament\Support\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Images of a gallery collection with a caption (ar/en), focal point and credit each, sortable.
 * Subclasses set $relationship to a morphMany(Media) relation filtered on the collection, e.g.
 * `public function galleryMedia() { return $this->media()->where('collection_name', 'gallery'); }`.
 */
abstract class GalleryRelationManager extends RelationManager
{
    protected static ?string $title = 'معرض الصور';

    protected static ?string $modelLabel = 'صورة';

    protected static string $collection = 'gallery';

    public function isReadOnly(): bool
    {
        return false;
    }

    /** Custom properties edited per image (besides caption, focal and credit). Override to add more. */
    protected function extraFields(): array
    {
        return [];
    }

    /** @return list<string> */
    private function propertyKeys(): array
    {
        return ['caption', 'focal', 'credit', 'credit_url', ...array_map(fn ($field) => $field->getName(), $this->extraFields())];
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(2)->schema([
                TextInput::make('caption.ar')->label('التعليق'),
                TextInput::make('caption.en')->label('التعليق (English)')->extraInputAttributes(['dir' => 'ltr']),
                TextInput::make('focal')->label('نقطة التركيز')->placeholder('50% 50%')->rule('nullable|regex:/^\d{1,3}%\s+\d{1,3}%$/')->extraInputAttributes(['dir' => 'ltr']),
                TextInput::make('credit')->label('المصدر / الحقوق'),
                TextInput::make('credit_url')->label('رابط المصدر')->url()->extraInputAttributes(['dir' => 'ltr']),
                ...$this->extraFields(),
            ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->reorderable('order_column')
            ->defaultSort('order_column')
            ->paginated(false)
            ->columns([
                ImageColumn::make('preview')->label('')->state(fn (Media $record) => $record->getUrl())->height(64),
                TextColumn::make('caption')->label('التعليق')->state(fn (Media $record) => $record->getCustomProperty('caption.ar')),
                TextColumn::make('focal')->label('التركيز')->state(fn (Media $record) => $record->getCustomProperty('focal')),
            ])
            ->headerActions([
                Action::make('upload')->label('إضافة صور')->icon('heroicon-o-photo')
                    ->schema([FileUpload::make('files')->label('الصور')->image()->multiple()->required()->disk('local')->directory('tmp/gallery')->maxSize(10240)])
                    ->action(function (array $data) {
                        foreach ($data['files'] as $path) {
                            $this->getOwnerRecord()->addMedia(Storage::disk('local')->path($path))->toMediaCollection(static::$collection);
                        }
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->fillForm(fn (Media $record) => collect($this->propertyKeys())->mapWithKeys(fn ($key) => [$key => $record->getCustomProperty($key)])->all())
                    ->using(function (Media $record, array $data): Model {
                        foreach ($this->propertyKeys() as $key) {
                            filled($data[$key] ?? null) ? $record->setCustomProperty($key, $data[$key]) : $record->forgetCustomProperty($key);
                        }
                        $record->save();

                        return $record;
                    }),
                DeleteAction::make(),
            ]);
    }
}
