<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OperatorQuery extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone_number',
        'operator_name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Bugün için verilen kullanıcıya ait yapılan sorgu sayısını döndürür.
     */
    public static function countTodayByUser(int $userId): int
    {
        $today = Carbon::today();

        return self::where('user_id', $userId)
            ->whereDate('created_at', $today)
            ->count();
    }
}
