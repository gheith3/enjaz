<?php

declare(strict_types=1);

namespace App\Filament\Resources\Testimonials;

use App\Filament\Resources\Testimonials\Pages\CreateTestimonial;
use App\Filament\Resources\Testimonials\Pages\EditTestimonial;
use App\Filament\Resources\Testimonials\Pages\ListTestimonials;
use App\Filament\Resources\Testimonials\Pages\ViewTestimonial;
use App\Filament\Resources\Testimonials\Schemas\TestimonialForm;
use App\Filament\Resources\Testimonials\Schemas\TestimonialInfolist;
use App\Filament\Resources\Testimonials\Tables\TestimonialsTable;
use App\Models\Testimonial;
use BackedEnum;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

/**
 * Testimonial Resource for managing testimonials (قالوا عنا).
 */
class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;

    public static function getNavigationGroup(): ?string
    {
        return __('common.nav_groups.people');
    }

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'person_name_ar';

    /**
     * Get the navigation badge count.
     */
    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    /**
     * Get the navigation badge color.
     */
    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'primary';
    }

    /**
     * Configure the form schema.
     */
    public static function form(Schema $schema): Schema
    {
        return TestimonialForm::configure($schema);
    }

    /**
     * Configure the infolist schema.
     */
    public static function infolist(Schema $schema): Schema
    {
        return TestimonialInfolist::configure($schema);
    }

    /**
     * Configure the table.
     */
    public static function table(Table $table): Table
    {
        return TestimonialsTable::configure($table);
    }

    /**
     * Get the resource relations.
     *
     * @return array<class-string>
     */
    public static function getRelations(): array
    {
        return [];
    }

    /**
     * Get the resource pages.
     *
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListTestimonials::route('/'),
            'create' => CreateTestimonial::route('/create'),
            'view' => ViewTestimonial::route('/{record}'),
            'edit' => EditTestimonial::route('/{record}/edit'),
        ];
    }

    /**
     * Get the globally searchable attributes.
     *
     * @return array<string>
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['person_name_ar', 'person_name_en', 'position_ar', 'position_en'];
    }

    /**
     * Get the model label.
     */
    public static function getModelLabel(): string
    {
        return __('testimonials.model_label');
    }

    /**
     * Get the plural model label.
     */
    public static function getPluralModelLabel(): string
    {
        return __('testimonials.model_label_plural');
    }

    /**
     * Get the navigation label.
     */
    public static function getNavigationLabel(): string
    {
        return __('testimonials.model_label_plural');
    }
}
