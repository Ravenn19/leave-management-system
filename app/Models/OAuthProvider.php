<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OAuthProvider extends Model
{
    protected $fillable = ['user_id', 'provider', 'provider_id'];

    protected $table = 'oauth_providers';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
