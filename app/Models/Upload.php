<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Upload extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_url',
        'file_type',
    ];

    protected $keyType = 'string';
    public $incrementing = false;


    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($upload) {
            // UUID for the primary key 'id'
            if (empty($upload->id)) {
                $upload->id = (string) Str::uuid();
            }
        });
    }

    // Relationship with the UserProfile if needed
    public function userProfile()
    {
        return $this->belongsTo(UserProfile::class);
    }
}
