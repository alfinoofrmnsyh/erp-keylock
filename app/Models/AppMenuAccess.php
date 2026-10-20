<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppMenuAccess extends Model
{
    protected $table = 'appmenuaccess';
    public $timestamps = false;

    protected $fillable = [
        'usergrpid',
        'menu_id',
        'createdtime',
    ];

    public function menu()
    {
        return $this->belongsTo(AppMenu::class, 'menu_id', 'menu_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'usergrpid', 'id');
    }
}
