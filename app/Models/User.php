<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * Kitle atanabilir (mass assignable) alanlar.
     *
     * @var array<int,string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'daily_limit',
        'role',
    ];

    /**
     * Gizlenecek alanlar (toArray/toJson sırasında).
     *
     * @var array<int,string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Type casting (örn. tarih, boolean, vs.)
     *
     * @var array<string,string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Bu kullanıcıya ait operatör sorgularını döndüren ilişki.
     * (Eğer ileride OperatorQuery modelini kullanacaksanız)
     */
    public function operatorQueries()
    {
        return $this->hasMany(OperatorQuery::class);
    }
}
