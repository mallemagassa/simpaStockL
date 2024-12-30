<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Spatie\Permission\Models\Role;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-c-user-group';

    protected static ?string $navigationLabel = "Utilisateurs";
    protected static ?int $navigationSort = 5;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label("Nom")
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('lastname')
                    ->label("Prenom")
                    ->required()
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('username')
                    ->label("Nom D'utilisateur")
                    ->required()
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->label("Email")
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->tel()
                    ->required()
                    ->label("Telephone")
                    ->maxLength(255),
                Forms\Components\Select::make('roles')
                    ->label(__('Role'))
                    ->required()
                    ->relationship(
                        name: 'roles',
                        modifyQueryUsing: fn (Builder $query) => $query->orderBy('name'),
                    )
                    ->getOptionLabelFromRecordUsing(fn (Role $record) => "{$record->name}")
                    ->searchable(['name'])
                    ->preload(),
                Forms\Components\TextInput::make('password')
                    ->required()
                    ->maxLength(255)
                    ->label('Mot de passe')
                    ->password()
                    ->revealable()
                    ->visibleOn('create')
                    ->extraInputAttributes(['autocomplete' => 'new-password']) // Pour éviter l'auto-complétion du navigateur
                    ->afterStateUpdated(function (callable $set, $state) {
                        // Vous pouvez ajouter ici une logique pour afficher ou masquer le mot de passe
                    }),
                Forms\Components\TextInput::make('password_confirmation')
                    ->required()
                    ->maxLength(255)
                    ->visibleOn('create')
                    ->label('Confirmer le mot de passe')
                    ->password()
                    ->revealable()
                    ->same('password')  // Validation pour vérifier que le mot de passe et sa confirmation correspondent
                    ->dehydrated(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('lastname')
                ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('username')
                ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label("Telephone")
                    // ->tel()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Action::make('changePassword') // Nom de l'action
                ->label('Changer le mot de passe') // Libellé affiché
                ->icon('heroicon-o-lock-closed') // Icône
                ->form([
                    Forms\Components\TextInput::make('password')
                        ->label('Nouveau mot de passe')
                        ->password()
                        ->required()
                        ->revealable(),
                        //->minLength(8),
                    Forms\Components\TextInput::make('password_confirmation')
                        ->label('Confirmer le mot de passe')
                        ->password()
                        ->required()
                        ->revealable()
                        ->same('password'),
                ])
                ->action(function (array $data, $record) {
                    // Met à jour le mot de passe
                    $record->update([
                        'password' => bcrypt($data['password']),
                    ]);

                    Notification::make()
                    ->title('Mot de passe mis à jour avec succès.')
                    ->success()
                    ->send();

                    // Message de confirmation
                    // Filament::notify('success', 'Mot de passe mis à jour avec succès.');
                }),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
