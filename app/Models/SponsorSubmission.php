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
     'description'];

    protected $keyType = 'string';
    public $incrementing = false;

    protected static function booted()
    {
        static::creating(function ($submission) {
            $submission->id = (string) Str::uuid();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sponsorChallenge()
    {
        return $this->belongsTo(SponsorChallenge::class, 'challenge_id');
    }
}
