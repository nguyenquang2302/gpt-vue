<?php

namespace App\Models\Users\Traits\Attribute;

use App\Models\Branch\Branch;
use Illuminate\Support\Facades\Hash;

/**
 * Trait UserAttribute.
 */
trait UserAttribute
{
    /**
     * @param $password
     */
    public function setPasswordAttribute($password): void
    {
        // If password was accidentally passed in already hashed, try not to double hash it
        // Note: Password Histories are logged from the \App\Domains\Auth\Observer\UserObserver class
        $this->attributes['password'] =
            (strlen($password) === 60 && preg_match('/^\$2y\$/', $password)) ||
            (strlen($password) === 95 && preg_match('/^\$argon2i\$/', $password)) ?
                $password :
                Hash::make($password);
    }

    /**
     * @return mixed
     */
    public function getAvatarAttribute()
    {
        return $this->getAvatar();
    }

    /**
     * @return string
     */
    public function getPermissionsLabelAttribute()
    {
        if ($this->hasAllAccess()) {
            return 'All';
        }

        if (! $this->permissions->count()) {
            return 'None';
        }

        return collect($this->getPermissionDescriptions())
            ->implode('<br/>');
    }

    /**
     * @return string
     */
    public function getRolesLabelAttribute()
    {
        if ($this->hasAllAccess()) {
            return 'All';
        }

        if (! $this->roles->count()) {
            return 'None';
        }

        return collect($this->getRoleNames())
            ->each(function ($role) {
                return ucwords($role);
            })
            ->implode('<br/>');
    }

    public function getMoneyLastAttribute()
    {
        return ($this->fundTransaction()->first()?$this->fundTransaction()->orderBy('id','desc')->first()->money_after : '0'); 
    }

    public function getBranchNameAttribute() {
        $branch = Branch::where('id',$this->branch_id)->first();
        return $branch?$branch->name:'Chưa chọn Chi nhánh';
    }
}
