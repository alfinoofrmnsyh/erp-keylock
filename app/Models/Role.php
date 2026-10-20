<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $fillable = [
        'name',
        'label',
        'description',
        'is_system',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    /**
     * Users associated with this role / usergrpid
     */
    public function users()
    {
        return $this->hasMany(User::class, 'usergrpid', 'id');
    }

    /**
     * Menu permissions associated with this role / usergrpid
     */
    public function menuAccesses()
    {
        return $this->hasMany(AppMenuAccess::class, 'usergrpid', 'id');
    }
}
