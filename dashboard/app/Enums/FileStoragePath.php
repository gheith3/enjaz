<?php

declare(strict_types=1);

namespace App\Enums;

enum FileStoragePath: string
{
    case UserAvatar = 'users/avatars';
    case AchievementCover = 'achievements/covers';
    case PublicFigureImage = 'public-figures/images';
    case PublicFigureTile = 'public-figures/tiles';
    case PublicFigureCutout = 'public-figures/cutouts';
    case PublicFigureBanner = 'public-figures/banners';
    case OrganizationLogo = 'organizations/logos';
    case OrganizationLogoWhite = 'organizations/logos-white';
    case OrganizationManagerImage = 'organizations/managers';
    case MainTypeIcon = 'achievement-types/icons';
    case NewsCover = 'news/covers';
    case TestimonialPhoto = 'testimonials/photos';
    case TeamMemberPhoto = 'team-members/photos';
    case MediaCover = 'media/covers';
    case MediaAlbum = 'media/albums';
    case SettingsImages = 'settings';

    public static function getUploadFilesVisibility(): string
    {
        return 'public';
    }
}
