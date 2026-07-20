<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class AuthorLevel extends Model
{
    use GlobalStatus;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
