<?php

declare(strict_types=1);

namespace App\Enums;

enum FileStoragePath: string
{
    case UserAvatar = 'users/avatars';


    public static function getUploadFilesVisibility(): string
    {
        return 'public';
    }
}
