<?php

declare(strict_types=1);

namespace App\Policies\Simulation;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Simulation\ExamSimulation;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExamSimulationPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ExamSimulation');
    }

    public function view(AuthUser $authUser, ExamSimulation $examSimulation): bool
    {
        return $authUser->can('View:ExamSimulation');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ExamSimulation');
    }

    public function update(AuthUser $authUser, ExamSimulation $examSimulation): bool
    {
        return $authUser->can('Update:ExamSimulation');
    }

    public function delete(AuthUser $authUser, ExamSimulation $examSimulation): bool
    {
        return $authUser->can('Delete:ExamSimulation');
    }

    public function restore(AuthUser $authUser, ExamSimulation $examSimulation): bool
    {
        return $authUser->can('Restore:ExamSimulation');
    }

    public function forceDelete(AuthUser $authUser, ExamSimulation $examSimulation): bool
    {
        return $authUser->can('ForceDelete:ExamSimulation');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ExamSimulation');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ExamSimulation');
    }

    public function replicate(AuthUser $authUser, ExamSimulation $examSimulation): bool
    {
        return $authUser->can('Replicate:ExamSimulation');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ExamSimulation');
    }

}