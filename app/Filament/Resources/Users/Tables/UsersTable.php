<?php

namespace App\Filament\Resources\Users\Tables;

use App\Mail\UserCredentialsMail;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('role')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'super_admin' => 'success',
                        'admin' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'super_admin' => 'Super Admin',
                        'admin' => 'Administrator',
                        default => $state,
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('sendCredentials')
                    ->label('Send Account Info')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (User $record) {
                        Mail::to($record->email)->send(new UserCredentialsMail($record));

                        Notification::make()
                            ->title('Credentials Sent')
                            ->body("Account information has been sent to {$record->email}")
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
