<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory;

    public $incrementing = true;
    public $timestamps = true;

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($query) {
            $query->uuid = Str::uuid()->toString();
        });
    }
}
