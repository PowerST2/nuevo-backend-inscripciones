<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ModalityDocument;
use Illuminate\Auth\Access\HandlesAuthorization;

class ModalityDocumentPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ModalityDocument');
    }

    public function view(AuthUser $authUser, ModalityDocument $modalityDocument): bool
    {
        return $authUser->can('View:ModalityDocument');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ModalityDocument');
    }

    public function update(AuthUser $authUser, ModalityDocument $modalityDocument): bool
    {
        return $authUser->can('Update:ModalityDocument');
    }

    public function delete(AuthUser $authUser, ModalityDocument $modalityDocument): bool
    {
        return $authUser->can('Delete:ModalityDocument');
    }

    public function restore(AuthUser $authUser, ModalityDocument $modalityDocument): bool
    {
        return $authUser->can('Restore:ModalityDocument');
    }

    public function forceDelete(AuthUser $authUser, ModalityDocument $modalityDocument): bool
    {
        return $authUser->can('ForceDelete:ModalityDocument');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ModalityDocument');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ModalityDocument');
    }

    public function replicate(AuthUser $authUser, ModalityDocument $modalityDocument): bool
    {
        return $authUser->can('Replicate:ModalityDocument');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ModalityDocument');
    }

}