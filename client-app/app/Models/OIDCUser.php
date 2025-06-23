<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OIDCUser extends Model
{
    protected $table = 'oidc_users';

    protected $fillable = [
        'sub',
        'name',
        'email',
    ];

    public $timestamps = true;
}
