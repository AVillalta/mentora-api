<?php

namespace App\Models\Country;

use Illuminate\Database\Eloquent\Model;
use App\Models\User\User;

class Country extends Model
{
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
