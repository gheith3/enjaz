<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Enums\AppUserRole;
use App\Enums\FileStoragePath;
use App\Enums\UserStatus;
use Filament\Actions\Action as ActionsAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make(__('users.form.sections.personal_information'))
                    ->description(__('users.form.sections.personal_information_description'))
                    ->icon('heroicon-o-user')
                    ->columns(3)
                    ->schema([
                        TextInput::make('name')
                            ->label(__('common.fields.full_name'))
                            ->required()
                            ->maxLength(100)
                            ->placeholder(__('users.form.placeholders.name')),

                        TextInput::make('email')
                            ->label(__('users.form.fields.email_address'))
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->placeholder(__('users.form.placeholders.email')),

                        TextInput::make('country_code')
                            ->label(__('common.fields.country_code'))
                            ->tel()
                            ->maxLength(4)
                            ->default('968')
                            ->placeholder(__('users.form.placeholders.country_code')),

                        TextInput::make('phone_number')
                            ->label(__('common.fields.phone_number'))
                            ->tel()
                            ->maxLength(20)
                            ->placeholder(__('users.form.placeholders.phone')),

                        Select::make('status')
                            ->label(__('users.form.fields.status'))
                            ->options(UserStatus::class)
                            ->default(UserStatus::Active)
                            ->required()
                            ->native(false)
                            ->helperText(__('users.form.helpers.status')),

                        FileUpload::make('avatar_url')
                            ->label(__('users.form.fields.profile_photo'))
                            ->image()
                            ->avatar()
                            ->imageEditor()
                            ->circleCropper()
                            ->directory(FileStoragePath::UserAvatar->value)
                            ->visibility('public')
                            ->columnSpanFull(),
                    ]),

                Section::make(__('users.form.sections.user_type'))
                    ->description(__('users.form.sections.user_type_description'))
                    ->icon('heroicon-o-identification')
                    ->schema([

                        Select::make('roles')
                            ->label(__('users.form.fields.roles'))
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->options(function () {
                                return Role::query()
                                    ->when(! auth()->user()->hasRole(AppUserRole::SuperAdmin->value), function ($query) {
                                        $query->where('name', '!=', AppUserRole::SuperAdmin->value);
                                    })->pluck('name', 'id');
                            })
                            ->native(false),

                    ]),

                Section::make(__('users.form.sections.security'))
                    ->description(__('users.form.sections.security_description'))
                    ->icon('heroicon-o-lock-closed')
                    ->columns(3)
                    ->schema([
                        TextInput::make('password')
                            ->label(__('users.form.fields.password'))
                            ->password()
                            ->revealable()
                            ->dehydrateStateUsing(fn ($state) => $state ? Hash::make($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->minLength(8)
                            ->same('password_confirmation')
                            ->suffixAction(
                                ActionsAction::make('generatePassword')
                                    ->icon('heroicon-o-sparkles')
                                    ->action(function ($set) {
                                        $password = Str::password(12);
                                        $set('password', $password);
                                        $set('password_confirmation', $password);
                                    })
                            ),

                        TextInput::make('password_confirmation')
                            ->label(__('users.form.fields.confirm_password'))
                            ->password()
                            ->revealable()
                            ->dehydrated(false)
                            ->required(fn (string $operation): bool => $operation === 'create'),

                        DateTimePicker::make('email_verified_at')
                            ->label(__('users.form.fields.email_verified_at'))
                            ->native(false)
                            ->displayFormat('M d, Y H:i'),
                    ]),

            ]);
    }
}
