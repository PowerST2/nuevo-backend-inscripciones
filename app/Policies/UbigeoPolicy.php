<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Ubigeo;
use Illuminate\Auth\Access\HandlesAuthorization;

class UbigeoPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Ubigeo');
    }

    public function view(AuthUser $authUser, Ubigeo $ubigeo): bool
    {
        return $authUser->can('View:Ubigeo');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Ubigeo');
    }

    public function update(AuthUser $authUser, Ubigeo $ubigeo): bool
    {
        return $authUser->can('Update:Ubigeo');
    }

    public function delete(AuthUser $authUser, Ubigeo $ubigeo): bool
    {
        return $authUser->can('Delete:Ubigeo');
    }

    public function restore(AuthUser $authUser, Ubigeo $ubigeo): bool
    {
        return $authUser->can('Restore:Ubigeo');
    }

    public function forceDelete(AuthUser $authUser, Ubigeo $ubigeo): bool
    {
        return $authUser->can('ForceDelete:Ubigeo');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Ubigeo');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Ubigeo');
    }

    public function replicate(AuthUser $authUser, Ubigeo $ubigeo): bool
    {
        return $authUser->can('Replicate:Ubigeo');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Ubigeo');
    }

}