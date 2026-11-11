<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/**
 * «الملف الشخصي» (user menu): the user's own name, email and password. Changing the email or the password asks for
 * the current password (Filament's checks), shown in two cards like the other dashboard forms.
 */
class EditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('بيانات الحساب')
                ->icon(Heroicon::OutlinedUserCircle)
                ->columns(2)->inlineLabel(false)
                ->schema([$this->getNameFormComponent(), $this->getEmailFormComponent()]),
            Section::make('كلمة المرور')
                ->description('اتركها فارغة إن لم ترد تغييرها.')
                ->icon(Heroicon::OutlinedLockClosed)
                ->columns(2)->inlineLabel(false)
                ->schema([
                    $this->getPasswordFormComponent(),
                    $this->getPasswordConfirmationFormComponent(),
                    $this->getCurrentPasswordFormComponent()->columnSpanFull(),
                ]),
        ]);
    }
}
