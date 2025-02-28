<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NavigationGroupPolicy
{
    use HandlesAuthorization;

    public function viewSystemManagement(User $user): bool
    {
        return $user->hasRole('admin');
    }
} 