<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SponsorSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'challenge_id',
        'image_url',
        'description',
        'status',
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function ($submission) {
            // Generate a UUID for the primary key 'id' if it's not already set
            if (!$submission->id) {
                $submission->id = (string) Str::uuid();
            }

            // Set the default status to 'pending' if not already set
            if (!$submission->status) {
                $submission->status = 'pending';
            }
        });
    }

    /**
     * Get the user associated with the submission.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the sponsor challenge associated with the submission.
     */
    public function sponsorChallenge()
    {
        return $this->belongsTo(SponsorChallenge::class, 'challenge_id');
    }
}
