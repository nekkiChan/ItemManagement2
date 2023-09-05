<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * 管理者のみアクセス可能
     */
    public function view(User $user)
    {
        return $user->role_id === 1;
    }

    /**
     * 管理者のみアクセス可能
     */
    public function edit(User $user)
    {
        return $user->role_id === 1;
    }

    /**
     * 管理者のみアクセス可能
     */
    public function admin(User $user)
    {
        return $user->role_id === 1;
    }

}
