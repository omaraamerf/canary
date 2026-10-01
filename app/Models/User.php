<?php

namespace App\Models;

use App\Enums\Permission;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use Database\Factories\UserFactory;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'password',
        'role',
        'status',
        'country_id',
        'region_id',
        'bio',
        'avatar_url',
        'avatar_public_id',
    ];

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
            'country_id' => 'integer',
            'region_id' => 'integer',
        ];
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function getInitialAttribute(): string
    {
        return mb_strtoupper(mb_substr($this->public_name, 0, 1));
    }

    public function getLocationLabelAttribute(): ?string
    {
        return collect([$this->region?->name, $this->country?->localized_name])->filter()->implode('، ') ?: null;
    }

    public function birds()
    {
        return $this->hasMany(Bird::class, 'seller_id');
    }

    public function sellerProfile()
    {
        return $this->hasOne(SellerProfile::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /** Reservations made while signed in (guest orders stay unlinked). */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function isSeller(): bool
    {
        return $this->role === UserRole::Seller->value;
    }

    /** The panel this user works in after signing in, if any; admins before sellers. */
    public function panelUrl(): ?string
    {
        foreach (['admin', 'seller'] as $id) {
            $panel = Filament::getPanel($id);

            if ($this->canAccessPanel($panel)) {
                return $panel->getUrl();
            }
        }

        return null;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin->value;
    }

    public function getPublicNameAttribute(): string
    {
        return $this->isSeller() ? ($this->sellerProfile?->display_name ?: $this->name) : $this->name;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($this->status !== UserStatus::Active->value) {
            return false;
        }

        return match ($panel->getId()) {
            'admin' => $this->can(Permission::AccessAdminPanel->value),
            'seller' => $this->can(Permission::AccessSellerPanel->value),
            default => false,
        };
    }
}
