<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'avatar', 'author', 'user_id', 'total_chapter', 'type', 'content'];

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function chapters()
    {
        return $this->hasMany(Chapter::class, 'story_id');
    }
}
