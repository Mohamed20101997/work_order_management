<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;

class EditProfile extends \Filament\Pages\Auth\EditProfile
{
    public static ?string $slug = 'profile';

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getAvatarFormComponent(),
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPhoneFormComponent(),
                        $this->getLocaleFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])
                    ->columns(2)
                    ->statePath('data'),
            ),
        ];
    }

    protected function getAvatarFormComponent(): Component
    {
        return FileUpload::make('avatar_path')
            ->label(__('Avatar'))
            ->image()
            ->imageEditor()
            ->avatar()
            ->circleCropper()
            ->directory('avatars')
            ->maxSize(2048)
            ->columnSpanFull();
    }

    protected function getPhoneFormComponent(): Component
    {
        return TextInput::make('phone')
            ->label(__('Phone'))
            ->tel()
            ->maxLength(30);
    }

    protected function getLocaleFormComponent(): Component
    {
        return Select::make('locale')
            ->label(__('Language'))
            ->options([
                'en' => 'English',
                'ar' => 'العربية',
            ])
            ->selectablePlaceholder(false);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record = parent::handleRecordUpdate($record, $data);

        if (filled($record->locale)) {
            session()->put('locale', $record->locale);
        }

        return $record;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['locale'] ??= config('app.locale', 'en');

        return $data;
    }
}
