<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppMenu extends Model
{
    protected $table = 'appmenu';
    protected $primaryKey = 'menu_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'menu_id',
        'menu_parent',
        'menu_name',
        'menu_page',
        'menu_order',
        'menu_icon',
        'erp',
        'project',
        'mobile',
    ];

    /**
     * Parent menu relationship
     */
    public function parent()
    {
        return $this->belongsTo(AppMenu::class, 'menu_parent', 'menu_id');
    }

    /**
     * Child submenus
     */
    public function children()
    {
        return $this->hasMany(AppMenu::class, 'menu_parent', 'menu_id')->orderBy('menu_order', 'asc');
    }

    /**
     * Access control entries
     */
    public function accesses()
    {
        return $this->hasMany(AppMenuAccess::class, 'menu_id', 'menu_id');
    }

    /**
     * Get accessible menu tree for a user group
     */
    public static function getTreeForUser($userGrpId = 1, $platform = 'erp')
    {
        return static::where('erp', 'Y')
            ->where('menu_parent', 0)
            ->whereHas('accesses', function ($q) use ($userGrpId) {
                $q->where('usergrpid', $userGrpId);
            })
            ->with(['children' => function ($q) use ($userGrpId) {
                $q->where('erp', 'Y')
                    ->whereHas('accesses', function ($sub) use ($userGrpId) {
                        $sub->where('usergrpid', $userGrpId);
                    })
                    ->orderBy('menu_order', 'asc');
            }])
            ->orderBy('menu_order', 'asc')
            ->get();
    }
}
