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

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function ($challenge) {
            // Generate a UUID for the primary key 'id' if it's not already set
            if (!$challenge->id) {
                $challenge->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the submissions associated with the challenge.
     */
    public function submissions()
    {
        return $this->hasMany(SponsorSubmission::class, 'challenge_id');
    }
}
