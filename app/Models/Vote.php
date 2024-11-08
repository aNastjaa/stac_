<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Vote extends Model
{
    use HasFactory;

    // Disable auto-incrementing
    public $incrementing = false;
    // Set UUID as the key type
    protected $keyType = 'string';
    // Mass assignable fields
    protected $fillable = [
        'user_id',
        'submission_id'
    ];

    /**
     * Boot method to generate UUID for primary key.
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            // Generate UUID for the primary key 'id'
            $model->id = (string) Str::uuid();
        });
    }

    /**
     * Relationship with User model.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with SponsorSubmission model.
     */
    public function submission()
    {
        return $this->belongsTo(SponsorSubmission::class, 'submission_id');
    }
}
