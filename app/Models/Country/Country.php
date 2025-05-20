<?php

namespace App\Models\Country;

use Illuminate\Database\Eloquent\Model;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Country extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name', 'code'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
