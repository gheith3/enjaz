<?php


namespace App\Providers;

use App\Models\Achievement;
use App\Models\AchievementMainType;
use App\Models\AchievementScope;
use App\Models\Achiever;
use App\Models\PublicFigure;
use App\Models\Organization;
use App\Models\Publication;
use App\Models\Video;
use App\Models\Image;
use App\Models\Album;
use App\Models\ImageMedia;
use App\Models\LandingImage;
use App\Models\OrganizationType;
use App\Models\PublicationMedia;
use App\Models\SubAchievementType;
use App\Models\User;
use App\Models\VideoMedia;
use App\Observers\CacheObserver;
use Illuminate\Support\ServiceProvider;

class ModelObserverServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Achievement::observe(CacheObserver::class);
        AchievementMainType::observe(CacheObserver::class);
        AchievementScope::observe(CacheObserver::class);
        Achiever::observe(CacheObserver::class);
        ImageMedia::observe(CacheObserver::class);
        LandingImage::observe(CacheObserver::class);
        Organization::observe(CacheObserver::class);
        OrganizationType::observe(CacheObserver::class);
        PublicationMedia::observe(CacheObserver::class);
        PublicFigure::observe(CacheObserver::class);
        SubAchievementType::observe(CacheObserver::class);
        User::observe(CacheObserver::class);
        VideoMedia::observe(CacheObserver::class);
        // Add any other models you want to observe
    }
} 