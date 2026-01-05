<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PaymentPortfolio;
use Illuminate\Auth\Access\HandlesAuthorization;

class PaymentPortfolioPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PaymentPortfolio');
    }

    public function view(AuthUser $authUser, PaymentPortfolio $paymentPortfolio): bool
    {
        return $authUser->can('View:PaymentPortfolio');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PaymentPortfolio');
    }

    public function update(AuthUser $authUser, PaymentPortfolio $paymentPortfolio): bool
    {
        return $authUser->can('Update:PaymentPortfolio');
    }

    public function delete(AuthUser $authUser, PaymentPortfolio $paymentPortfolio): bool
    {
        return $authUser->can('Delete:PaymentPortfolio');
    }

    public function restore(AuthUser $authUser, PaymentPortfolio $paymentPortfolio): bool
    {
        return $authUser->can('Restore:PaymentPortfolio');
    }

    public function forceDelete(AuthUser $authUser, PaymentPortfolio $paymentPortfolio): bool
    {
        return $authUser->can('ForceDelete:PaymentPortfolio');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PaymentPortfolio');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PaymentPortfolio');
    }

    public function replicate(AuthUser $authUser, PaymentPortfolio $paymentPortfolio): bool
    {
        return $authUser->can('Replicate:PaymentPortfolio');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PaymentPortfolio');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PaymentPortfolio');
    }
}
