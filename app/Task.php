<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Task extends Model
{
    //
    public function scopeIncomplete($query)
    {
        return $query->where('completed', 0);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers');
    }

    public function scopePublic($query)
    {
    	return $query->where('private', 0);
    }
}
