<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    # To get the info of the owner of the post
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    # To get the comments of a post
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    
}
