<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class AppVersion extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'latest_version',
        'release_notes',
        'download_url',
    ];

    public function getSetupFileUrlAttribute(): ?string
    {
        return $this->getFirstMediaUrl('setup_file');
    }
}
