<?php

namespace App\Livewire;

use Filament\Forms;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Facades\Filament;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Filament\Support\Exceptions\Halt;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Validation\Rules\Password;
use Filament\Forms\Concerns\InteractsWithForms;
use Joaopaulolndev\FilamentEditProfile\Concerns\HasSort;
use Joaopaulolndev\FilamentEditProfile\Concerns\HasUser;

class CustomEditPasswordComponent extends Component implements HasForms
{
    use InteractsWithForms;
    use HasSort, HasUser;

    public ?array $data = [];

    protected static int $sort = 20;

    public function mount(): void
    {
        $this->user = $this->getUser();

        // Remplir le formulaire avec les données initiales
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Mettre à jour le mot de passe')
                    ->aside()
                    ->description('Assurez-vous que votre compte utilise un mot de passe long et aléatoire pour rester en sécurité. ')
                    ->schema([
                        Section::make(__('filament-edit-profile::default.update_password'))
                            ->aside()
                            ->description(__('filament-edit-profile::default.ensure_your_password'))
                            ->schema([
                                TextInput::make('current_password')
                                    ->label(__('filament-edit-profile::default.current_password'))
                                    ->password()
                                    ->required()
                                    ->currentPassword()
                                    ->revealable(),
                                TextInput::make('password')
                                    ->label(__('filament-edit-profile::default.new_password'))
                                    ->password()
                                    ->required()
                                    ->autocomplete('new-password')
                                    ->revealable(),
                                TextInput::make('password_confirmation')
                                    ->label(__('filament-edit-profile::default.confirm_password'))
                                    ->password()
                                    ->required()
                                    ->same('password')
                                    ->revealable(),
                            ]),
                    ]),
            ])
            ->model($this->getUser())
            ->statePath('data');
    }

    public function updatePassword(): void
    {
        try {
            $data = $this->form->getState();

            // Hachage du mot de passe avant la mise à jour
            $newData = [
                'password' => Hash::make($data['password']),
            ];

            // Mise à jour de l'utilisateur
            $this->user->update($newData);

            // Rafraîchir l'utilisateur et réinitialiser le formulaire
            $this->user->refresh();
            $this->form->fill($this->user->toArray());

            // Notification de succès
            Notification::make()
                ->success()
                ->title(__('filament-edit-profile::default.saved_successfully'))
                ->send();
        } catch (Halt $exception) {
            Notification::make()
                ->danger()
                ->title(__('Erreur'))
                ->body(__('Une erreur s\'est produite lors de la mise à jour du mot de passe.'))
                ->send();
        }
    }

    public function save(): void
    {
        // Alias pour la méthode `updatePassword`
        $this->updatePassword();
    }

    public function render(): View
    {
        return view('livewire.custom-edit-password-component');
    }
}
