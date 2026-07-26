<?php
namespace Leaderhub\Core\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser {
    use Notifiable, HasRoles;
    protected $guarded = [];
    protected $hidden = ['password', 'remember_token'];
    protected function casts(): array {
        return ['email_verified_at' => 'datetime', 'password' => 'hashed'];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active ?? true;
    }
}
