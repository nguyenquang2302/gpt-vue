<?php

namespace App\Models\Users;

use App\Models\Users\Traits\Attribute\UserAttribute;
use App\Models\Users\Traits\Method\UserMethod;
use App\Models\Users\Traits\Relationship\UserRelationship;
use App\Models\Users\Traits\Scope\UserScope;
use App\Domains\Auth\Notifications\Frontend\ResetPasswordNotification;
use App\Domains\Auth\Notifications\Frontend\VerifyEmail;
use DarkGhostHunter\Laraguard\Contracts\TwoFactorAuthenticatable;
use DarkGhostHunter\Laraguard\TwoFactorAuthentication;
use Database\Factories\UserFactory;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Lab404\Impersonate\Models\Impersonate;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class User.
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens,
        HasFactory,
        HasRoles,
        MustVerifyEmailTrait,
        Notifiable,
        SoftDeletes,
        UserAttribute,
        UserMethod,
        UserRelationship,
        UserScope;

    public const TYPE_ADMIN = 'admin';
    public const TYPE_MANAGER = 'manager';
    public const TYPE_MANAGER_VIP = 'manager_vip';
    public const TYPE_MANAGER_VIP_2 = 'manager_vip_2';
    public const TYPE_MOD = 'mod';
    public const TYPE_STAFF = 'staff';
    public const TYPE_USER = 'user';
    public const TYPE_BANK = 'bank';
    public const TYPE_POS = 'pos';
    public const TYPE_PARTNER = 'partner';
    public const TYPE_TELE_SALES = 'tele_sales';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'name',
        'email',
        'email_verified_at',
        'password',
        'password_changed_at',
        'active',
        'timezone',
        'last_login_at',
        'last_login_ip',
        'to_be_logged_out',
        'provider',
        'provider_id',
        'bank_id',
        'accountName',
        'accountNo',
        'benAccountNo',
        'activeBank',
        'passBank',
        'autoPosBack',
        'birth_day',
        'facebook_link',
        'twitter_link',
        'youtube_link',
        'skype_link',
        'instagram_link',
        'description',
        'content',
        'phone',
        'avatar',
        'branch_id',
        'posName',
        'time_partner',
        'fee_partner'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'last_login_at',
        'email_verified_at',
        'password_changed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
        'last_login_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'to_be_logged_out' => 'boolean',
        'autoPosBack' => 'boolean',
        'activeBank' => 'boolean',
    ];

    /**
     * @var array
     */
    protected $appends = [
        'avatar',
        'money_last',
        'branch_name'
    ];

    /**
     * @var string[]
     */
    protected $with = [
        'permissions',
        'roles',
    ];

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Send the registration verification email.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmail);
    }

    /**
     * Return true or false if the user can impersonate an other user.
     *
     * @param void
     * @return bool
     */
    public function canImpersonate(): bool
    {
        return $this->can('admin.access.user.impersonate');
    }

    /**
     * Return true or false if the user can be impersonate.
     *
     * @param void
     * @return bool
     */
    public function canBeImpersonated(): bool
    {
        return ! $this->isMasterAdmin();
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return UserFactory::new();
    }
}
