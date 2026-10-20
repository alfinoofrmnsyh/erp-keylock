<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'usergrpid',
        'is_active',
        'password',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function roleModel()
    {
        return $this->belongsTo(Role::class, 'usergrpid', 'id');
    }

    public function leads()
    {
        return $this->hasMany(Lead::class, 'sales_id');
    }

    public function isAdmin(): bool
    {
        $role = strtolower($this->role ?? '');
        return in_array($role, ['admin', 'owner', 'administrator', 'superadmin']) 
            || (int)($this->usergrpid ?? 0) === 1;
    }

    public function isSales(): bool
    {
        return $this->isAdmin() || in_array(strtolower($this->role ?? ''), ['sales', 'marketing']);
    }

    public function isFinance(): bool
    {
        return $this->isAdmin() || in_array(strtolower($this->role ?? ''), ['finance', 'keuangan', 'accounting']);
    }

    public function isOperational(): bool
    {
        return $this->isAdmin() || in_array(strtolower($this->role ?? ''), ['operasional', 'operational', 'logistik', 'procurement', 'admin_op']);
    }

    public function hasRole(string|array $roles): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $userRole = strtolower($this->role ?? '');

        if (is_array($roles)) {
            $roles = array_map('strtolower', $roles);
            return in_array($userRole, $roles);
        }

        return $userRole === strtolower($roles);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Determine if the user can access the Filament admin panel (A01:2021).
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin();
    }
}
