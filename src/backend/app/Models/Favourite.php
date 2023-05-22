<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favourite extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'story_id', 'chapter_id', 'type'];

    /**
     * Story - Lesstion One to One relationship.
     */
    public function story()
    {
        return $this->belongsTo(Story::class, 'story_id');
    }
}
