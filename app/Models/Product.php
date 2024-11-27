<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
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
