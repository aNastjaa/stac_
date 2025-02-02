<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SponsorSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'challenge_id',
        'image_path', 
        'description',
        'status',
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($submission) {
            if (!$submission->id) {
                $submission->id = (string) Str::uuid();
            }
            if (!$submission->status) {
                $submission->status = 'pending';
            }
        });

        static::retrieved(function ($submission) {
            if ($submission->image_path && !str_starts_with($submission->image_path, 'http')) {
                $submission->image_path = url(Storage::url($submission->image_path));
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
