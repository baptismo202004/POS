<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

/**
 * Provides a helper to get the current user's accessible branch IDs.
 * Empty array = no restriction (Superadmin sees all).
 * Non-empty array = restrict queries to those branch IDs.
 */
trait ScopesByBranch
{
    protected function accessibleBranchIds(): array
    {
        $user = Auth::user();
        if (! $user) {
            return [];
        }

        $user->loadMissing('userType');

        return $user->accessibleBranchIds();
    }

    /**
     * Apply branch scope to a query builder.
     * Usage: $this->applyBranchScope($query, 'branch_id')
     */
    protected function applyBranchScope($query, string $column = 'branch_id'): void
    {
        $ids = $this->accessibleBranchIds();
        if (! empty($ids)) {
            $query->whereIn($column, $ids);
        }
    }
}
