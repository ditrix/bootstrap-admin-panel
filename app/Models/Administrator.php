<?php

namespace App\Models;

use App\Notifications\AdminResetPassword;
use Database\Factories\AdministratorFactory;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

/**
 * Authenticated admin-area account (credentials in the `administrators` table).
 */
class Administrator extends Authenticatable
{
    /** @use HasFactory<AdministratorFactory> */
    use CanResetPassword, HasFactory, HasRoles, Notifiable;

    protected $table = 'administrators';

    protected string $guard_name = 'admin';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_active',
        'role_id',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    /**
     * Sends the password reset notification via the {@see AdminResetPassword} channel.
     *
     * @param  string  $token  Plain reset token forwarded to the notification.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new AdminResetPassword($token));
    }

    /**
     * Denormalized FK to {@see Role} (task); kept in sync with Spatie pivots via {@see static::booted()}.
     */
    public function adminRole(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    protected static function booted(): void
    {
        static::saved(function (Administrator $administrator): void {
            if ($administrator->role_id === null) {
                $administrator->syncRoles([]);

                return;
            }

            $role = Role::query()
                ->whereKey($administrator->role_id)
                ->where('guard_name', $administrator->guard_name ?? 'admin')
                ->first();

            if ($role !== null) {
                $administrator->syncRoles($role);
            }
        });
    }
}
