<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['type', 'amount', 'category', 'description', 'date'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
