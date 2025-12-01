<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SystemDocument;
use Illuminate\Auth\Access\HandlesAuthorization;

class SystemDocumentPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SystemDocument');
    }

    public function view(AuthUser $authUser, SystemDocument $systemDocument): bool
    {
        return $authUser->can('View:SystemDocument');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SystemDocument');
    }

    public function update(AuthUser $authUser, SystemDocument $systemDocument): bool
    {
        return $authUser->can('Update:SystemDocument');
    }

    public function delete(AuthUser $authUser, SystemDocument $systemDocument): bool
    {
        return $authUser->can('Delete:SystemDocument');
    }

    public function restore(AuthUser $authUser, SystemDocument $systemDocument): bool
    {
        return $authUser->can('Restore:SystemDocument');
    }

    public function forceDelete(AuthUser $authUser, SystemDocument $systemDocument): bool
    {
        return $authUser->can('ForceDelete:SystemDocument');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SystemDocument');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SystemDocument');
    }

    public function replicate(AuthUser $authUser, SystemDocument $systemDocument): bool
    {
        return $authUser->can('Replicate:SystemDocument');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SystemDocument');
    }

}