<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SponsorChallenge extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'brief',
        'brand_name',
        'brand_logo_id',
        'submission_deadline',
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    protected static function booted()
    {
        static::creating(function ($challenge) {
            if (empty($challenge->id)) {
                $challenge->id = (string) Str::uuid();
            }
        });
    }
}

