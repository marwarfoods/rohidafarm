<?php

namespace App\Models;

use App\Traits\HasUuid;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasUuid, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'email',
        'password',
        'phone',
        'role',
        'google_id',
        'avatar',
        'otp_code',
        'otp_expires_at',
        'wallet_balance',
        'last_seen_orders_at',
        'last_seen_customers_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',
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
            'otp_expires_at' => 'datetime',
            'wallet_balance' => 'decimal:2',
            'last_seen_orders_at' => 'datetime',
            'last_seen_customers_at' => 'datetime',
        ];
    }

    /**
     * Check if user is Admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Get user's roles.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Check if user has specific role.
     */
    public function hasRole(string $role): bool
    {
        if ($this->isAdmin() && $role === 'admin') {
            return true;
        }
        return $this->roles->contains('name', $role);
    }

    /**
     * Check if user has specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->isAdmin()) {
            return true;
        }
        foreach ($this->roles as $role) {
            if ($role->permissions->contains('name', $permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Addresses relationship.
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Orders relationship.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Reviews relationship.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    /**
     * Wishlist relationship.
     */
    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * Cart Items relationship.
     */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Cart Items relationship.
     */
    public function logs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Auto-sync address from last order if no address exists.
     */
    public function syncAddressFromLastOrder()
    {
        if ($this->addresses()->count() === 0) {
            $lastOrder = $this->orders()->latest()->first();
            if ($lastOrder) {
                $this->addresses()->create([
                    'type' => 'shipping',
                    'name' => $lastOrder->shipping_name,
                    'phone' => $lastOrder->shipping_phone,
                    'address_line1' => $lastOrder->shipping_address_line1,
                    'address_line2' => $lastOrder->shipping_address_line2,
                    'city' => $lastOrder->shipping_city,
                    'state' => $lastOrder->shipping_state,
                    'postal_code' => $lastOrder->shipping_postal_code,
                    'is_default' => true,
                ]);
            }
        }
    }

    /**
     * Transactions relationship.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
