<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Modality;
use Illuminate\Auth\Access\HandlesAuthorization;

class ModalityPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Modality');
    }

    public function view(AuthUser $authUser, Modality $modality): bool
    {
        return $authUser->can('View:Modality');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Modality');
    }

    public function update(AuthUser $authUser, Modality $modality): bool
    {
        return $authUser->can('Update:Modality');
    }

    public function delete(AuthUser $authUser, Modality $modality): bool
    {
        return $authUser->can('Delete:Modality');
    }

    public function restore(AuthUser $authUser, Modality $modality): bool
    {
        return $authUser->can('Restore:Modality');
    }

    public function forceDelete(AuthUser $authUser, Modality $modality): bool
    {
        return $authUser->can('ForceDelete:Modality');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Modality');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Modality');
    }

    public function replicate(AuthUser $authUser, Modality $modality): bool
    {
        return $authUser->can('Replicate:Modality');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Modality');
    }

}