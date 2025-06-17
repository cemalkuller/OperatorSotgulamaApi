<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppVersionResource\Pages;
use App\Filament\Resources\AppVersionResource\RelationManagers;
use App\Models\AppVersion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;


class AppVersionResource extends Resource
{
    protected static ?string $model = AppVersion::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function beforeCreate($record): void
    {
        if (request()->hasFile('download_url')) {
            $file = request()->file('download_url');
            \Log::info('Dosya MIME Type: ' . $file->getMimeType());
            \Log::info('Dosya Extension: ' . $file->getClientOriginalExtension());
        }
    }
    public static function getMaxUploadSizeInMb(): int
    {
        $size = ini_get('upload_max_filesize');

        $unit = strtoupper(substr($size, -1));
        $value = (int) $size;

        return match ($unit) {
            'G' => $value * 1024,
            'M' => $value,
            'K' => (int) ceil($value / 1024),
            default => $value,
        };
    }

    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                Forms\Components\TextInput::make('latest_version')
                    ->label('Son Sürüm')
                    ->required()
                    ->maxLength(20),

                Forms\Components\FileUpload::make('setup_file')
                    ->label('Setup Dosyası (zip/exe)')
                    ->required()
                    ->preserveFilenames()
                    ->directory('app_versions') // storage/app/app_versions içine kaydeder
                    ->disk('public') // public disk kullanılır (storage/app/public/app_versions)
                    ->maxSize(200 * 1024) // 200 MB
                    ->storeFileNamesIn('download_url'),
                Forms\Components\Textarea::make('release_notes')
                    ->label('Yayın Notları')
                    ->rows(5),
            ]);


    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('latest_version')->label('Sürüm')->sortable(),
                Tables\Columns\TextColumn::make('download_url')
                    ->label('Dosya Linki')
                    ->url(fn($record) => asset('storage/app_versions/' . $record->download_url))
                    ->openUrlInNewTab()
                    ->formatStateUsing(fn($state) => basename($state)),
                Tables\Columns\TextColumn::make('release_notes')->label('Yayın Notları')->limit(50),
                Tables\Columns\TextColumn::make('created_at')->label('Oluşturulma')->dateTime()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListAppVersions::route('/'),
            'create' => Pages\CreateAppVersion::route('/create'),
            'edit' => Pages\EditAppVersion::route('/{record}/edit'),
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
