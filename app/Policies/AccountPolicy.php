<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccountPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Account $account)
    {
        return $user->id === $account->user_id;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Account $account)
    {
        return $user->id === $account->user_id;
    }

    public function delete(User $user, Account $account)
    {
        return $user->id === $account->user_id;
    }
}