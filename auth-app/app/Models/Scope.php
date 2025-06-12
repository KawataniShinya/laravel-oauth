<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Role|null $role
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scope newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scope newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scope query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scope whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scope whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scope whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Scope whereUpdatedAt($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @mixin \Eloquent
 */
class Scope extends Model
{
    protected $fillable = ['name', 'role_id'];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_scope');
    }
}
