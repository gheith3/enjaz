<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PublicFigure extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'image_path',
        'facebook_url',
        'twitter_url',
        'instagram_url',
        'whatsapp_number',
        'linkedin_url',
        'email',
        'phone',
        'story_ar',
        'story_en',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // ... existing casts ...
    ];

    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(Achievement::class,
            'achievement_public_figures')
            ->withTimestamps();
    }

    /**
     * Get the social media links as an associative array.
     */
    public function getSocialMediaLinks(): array
    {
        $links = [
            'facebook' => [
                'account' => $this->facebook_url,
                'url' => "https://www.facebook.com/" . $this->facebook_url,
                'icon' => asset('images/logos/facebook-f-brands.svg'),
            ],
            'twitter' => [
                'account' => $this->twitter_url,
                'url' => "https://www.twitter.com/" . $this->twitter_url,
                'icon' => asset('images/logos/x-twitter-brands.svg'),
            ],
            'instagram' => [
                'account' => $this->instagram_url,
                'url' => "https://www.instagram.com/" . $this->instagram_url,
                'icon' => asset('images/logos/instagram-brands.svg'),
            ],
            'whatsapp' => [
                'account' => $this->whatsapp_number,
                'url' => "https://wa.me/" . $this->whatsapp_number,
                'icon' => asset('images/logos/whatsapp-brands.svg'),
            ],
            'linkedin' => [
                'account' => $this->linkedin_url,
                'url' => "https://www.linkedin.com/in/" . $this->linkedin_url,
                'icon' => asset('images/logos/linkedin-in-brands.svg'),
            ],
            'email' => [
                'account' => $this->email,
                'url' => "mailto:" . $this->email,
                'icon' => asset('images/logos/envelope-regular.svg'),
            ],
            'phone' => [
                'account' => $this->phone,
                'url' => "tel:" . $this->phone,
                'icon' => asset('images/logos/phone-solid.svg'),
            ],
        ];

        return array_filter($links, fn($item) => !empty($item['account']));
    }
}
