<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
    ];

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    protected static function booted()
    {
        static::deleting(function ($user) {
            $user->addresses()->delete();
        });
    }
}