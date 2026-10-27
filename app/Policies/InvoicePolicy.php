<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\Pilgrim;
use App\Models\User;

class InvoicePolicy
{
    public function viewPilgrim(Pilgrim $pilgrim, Invoice $invoice): bool
    {
        return $invoice->pilgrim_id === $pilgrim->id;
    }

    public function viewUser(User $user, Invoice $invoice): bool
    {
        if (in_array($user->role, ['super_admin', 'admin', 'finance', 'branch_manager'], true)) {
            return true;
        }
        return $invoice->booking?->agent_id === $user->id;
    }
}
