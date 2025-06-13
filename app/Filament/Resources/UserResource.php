<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\ViewUser;
use App\Models\User;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
// ↓ Doğru Form sınıfını import ediyoruz:
use Filament\Forms\Form;
use Filament\Resources\Resource;
// ↓ Doğru Table sınıfını import ediyoruz:
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'Kullanıcılar';
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 1;

    /**
     * Filament’in Resource base sınıfındaki imzayla birebir eşleşiyor:
     * public static function form(\Filament\Forms\Form $form): \Filament\Forms\Form
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    TextInput::make('name')
                        ->label('Adı Soyadı')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->label('E-posta')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true),

                    TextInput::make('password')
                        ->label('Şifre')
                        ->password()
                        // Sadece Create sayfasında zorunlu, Edit sayfasında boş geçilebilir
                        ->required(fn($livewire) => $livewire instanceof CreateUser)
                        ->minLength(8)
                        ->maxLength(255)
                        // Eğer şifre alanı boşsa, veritabanına gizle
                        ->dehydrated(fn($state) => filled($state))
                        // Kaydedilirken bcrypt ile hash’le
                        ->dehydrateStateUsing(fn($state) => Hash::make($state)),

                    TextInput::make('password_confirmation')
                        ->label('Şifre (Tekrar)')
                        ->password()
                        ->required(fn($livewire) => $livewire instanceof CreateUser)
                        ->same('password'),

                    Select::make('role')
                        ->label('Rol')
                        ->options([
                            'admin' => 'Admin',
                            'editor' => 'Editor',
                            'user' => 'Kullanıcı',
                        ])
                        ->required()
                        ->default('user'),

                    TextInput::make('daily_limit')
                        ->label('Günlük Limit')
                        ->numeric()
                        ->required()
                        ->default(1000)
                        ->minValue(0),
                    TextInput::make('batch_size')
                        ->label('Batch Boyutu')
                        ->numeric()
                        ->default(1000)
                        ->minValue(1)
                        ->required(),
                ]),
            ]);
    }

    /**
     * Filament’in Resource base sınıfındaki imzayla eşleşiyor:
     * public static function table(\Filament\Tables\Table $table): \Filament\Tables\Table
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Adı Soyadı')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('E-posta')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('role')
                    ->label('Rol')
                    ->sortable(),

                TextColumn::make('daily_limit')
                    ->label('Günlük Limit')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Oluşturulma Tarihi')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role')
                    ->label('Rol Göre')
                    ->options([
                        'admin' => 'Admin',
                        'editor' => 'Editor',
                        'user' => 'Kullanıcı',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
            'view' => ViewUser::route('/{record}'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public static function canCreate(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }
}
