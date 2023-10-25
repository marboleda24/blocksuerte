<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use LdapRecord\Laravel\Auth\AuthenticatesWithLdap;
use LdapRecord\Laravel\Auth\HasLdapUser;
use LdapRecord\Laravel\Auth\LdapAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements LdapAuthenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasLdapUser;
    use AuthenticatesWithLdap;
    use HasRoles;
    use Compoships;

    /**
     * @var bool
     */
    protected $rememberTokenName = false;

    /**
     * guard_name
     *
     * @var string
     */
    protected string $guard_name = 'sanctum';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'domain',
        'guid',
        'theme',
        'occupation',
        'vendor_code',
        'last_action',
        'work_centers',
        'document'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url', 'shops_names', 'principal_shop', 'work_center_array',
    ];

    /**
     * getLdapDomainColumn
     *
     * @return string
     */
    public function getLdapDomainColumn(): string
    {
        return 'domain';
    }

    /**
     * getLdapGuidColumn
     *
     * @return string
     */
    public function getLdapGuidColumn(): string
    {
        return 'guid';
    }

    /**
     * @return BelongsToMany
     */
    public function shops(): BelongsToMany
    {
        return $this->belongsToMany(Shop::class, 'pvt_shop_user', 'user_id', 'shop_id')
            ->withPivot('user_id');
    }

    /**
     * @return mixed
     */
    public function getPrincipalShopAttribute(): mixed
    {
        return $this->shops ?? $this->shops()->where('default', '=', true)->pluck('name')[0];
    }

    /**
     * @return mixed
     */
    public function getShopsNamesAttribute(): mixed
    {
        return $this->shops->pluck('name');
    }

    public function getWorkCenterArrayAttribute(): array
    {
        return explode(',', $this->work_centers);
    }
}
