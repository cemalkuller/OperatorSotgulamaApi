<?php

namespace App\Filament\Resources\AppVersionResource\Pages;

use App\Filament\Resources\AppVersionResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Log;
use Livewire\TemporaryUploadedFile;

class CreateAppVersion extends CreateRecord
{
    protected static string $resource = AppVersionResource::class;

    protected TemporaryUploadedFile|null $tempUploadedFile = null;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if ($data['setup_file'] instanceof TemporaryUploadedFile) {
            $this->tempUploadedFile = $data['setup_file'];
        }

        unset($data['setup_file']);

        return $data;
    }

    protected function afterCreate(): void
    {
        if ($this->tempUploadedFile) {
            $this->record
                ->addMedia($this->tempUploadedFile->getRealPath())
                ->usingFileName($this->tempUploadedFile->getClientOriginalName())
                ->preservingOriginal()
                ->toMediaCollection('setup_file');
        }
    }
}
