<?php

namespace App\Traits;

use App\Enums\StaffRole;

trait HasRoles
{
    public function getRoleEnum(): ?StaffRole
    {
        return $this->role ? StaffRole::tryFrom($this->role) : null;
    }

    public function hasRole(StaffRole|string $role): bool
    {
        $roleValue = $role instanceof StaffRole ? $role->value : $role;

        return $this->role === $roleValue;
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(StaffRole::SuperAdmin);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(StaffRole::SuperAdmin) || $this->hasRole(StaffRole::Admin);
    }

    public function isFinance(): bool
    {
        return $this->hasRole(StaffRole::Finance);
    }

    public function isBranchManager(): bool
    {
        return $this->hasRole(StaffRole::BranchManager);
    }

    public function isAgent(): bool
    {
        return $this->hasRole(StaffRole::Agent);
    }

    public function isMuthawif(): bool
    {
        return $this->hasRole(StaffRole::Muthawif);
    }
}
