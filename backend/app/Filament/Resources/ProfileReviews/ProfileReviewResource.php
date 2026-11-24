<?php

namespace App\Filament\Resources\ProfileReviews;

use App\Filament\Resources\ProfileReviews\Pages\ListProfileReviews;
use App\Filament\Resources\ProfileReviews\Pages\ReviewFieldAchievers;
use App\Filament\Resources\ProfileReviews\Pages\ReviewFields;
use App\Filament\Resources\ProfileReviews\Pages\ReviewProfileReview;
use App\Filament\Resources\ProfileReviews\Pages\ReviewsHub;
use App\Models\ProfileReview;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

/**
 * مراجعات المنجزين: the private links sent to achievers to review their profile, and their answers waiting for approval.
 * The section opens on two tiles (المنجزون، المؤسسات), then the fields, then the achievers of a field with the stage of their
 * review. A link is also created from the achiever's page («رابط مراجعة الصفحة»).
 */
class ProfileReviewResource extends Resource
{
    protected static ?string $model = ProfileReview::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static string|UnitEnum|null $navigationGroup = 'المحتوى';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'مراجعة منجز';

    protected static ?string $pluralModelLabel = 'مراجعات المنجزين';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $n = ProfileReview::query()->pending()->count();

        return $n ? (string) $n : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('achiever.name')->label('المنجز')
                    ->state(fn (ProfileReview $r) => $r->achiever?->getTranslation('name', 'ar', false))->weight('bold'),
                TextColumn::make('status')->label('الحالة')->badge()
                    ->formatStateUsing(fn (string $state) => ProfileReview::STATUSES[$state] ?? $state)
                    ->color(fn (string $state) => ProfileReview::STATUS_COLORS[$state] ?? 'gray'),
                TextColumn::make('progress')->label('التغييرات')
                    ->state(function (ProfileReview $r) {
                        $p = $r->progress();

                        return collect([$p['edited'] ? "{$p['edited']} تعديل" : null, $p['lists'] ? "{$p['lists']} قسم" : null, $p['photos'] ? "{$p['photos']} صورة" : null])
                            ->filter()->implode(' · ') ?: '—';
                    }),
                TextColumn::make('additions')->label('إنجازات مضافة')->state(fn (ProfileReview $r) => count($r->additions ?? []) ?: '—'),
                TextColumn::make('submitted_at')->label('أُرسلت')->dateTime('Y-m-d H:i')->placeholder('—')->sortable(),
                TextColumn::make('expires_at')->label('صلاحية الرابط')->date('Y-m-d')->placeholder('—')
                    ->color(fn (ProfileReview $r) => $r->isExpired() ? 'danger' : null),
            ])
            ->filters([
                SelectFilter::make('status')->label('الحالة')->options(ProfileReview::STATUSES),
            ])
            ->recordUrl(fn (ProfileReview $r) => static::getUrl('review', ['record' => $r]))
            ->recordActions([
                Action::make('open')->label('فتح')->icon('heroicon-o-eye')
                    ->url(fn (ProfileReview $r) => static::getUrl('review', ['record' => $r])),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ReviewsHub::route('/'),
            'all' => ListProfileReviews::route('/all'),
            'fields' => ReviewFields::route('/achievers'),
            'field' => ReviewFieldAchievers::route('/achievers/{field}'),
            'review' => ReviewProfileReview::route('/r/{record}'),
        ];
    }
}
