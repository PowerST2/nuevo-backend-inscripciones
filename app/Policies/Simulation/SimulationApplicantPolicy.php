<?php

declare(strict_types=1);

namespace App\Policies\Simulation;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Simulation\SimulationApplicant;
use Illuminate\Auth\Access\HandlesAuthorization;

class SimulationApplicantPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SimulationApplicant');
    }

    public function view(AuthUser $authUser, SimulationApplicant $simulationApplicant): bool
    {
        return $authUser->can('View:SimulationApplicant');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SimulationApplicant');
    }

    public function update(AuthUser $authUser, SimulationApplicant $simulationApplicant): bool
    {
        return $authUser->can('Update:SimulationApplicant');
    }

    public function delete(AuthUser $authUser, SimulationApplicant $simulationApplicant): bool
    {
        return $authUser->can('Delete:SimulationApplicant');
    }

    public function restore(AuthUser $authUser, SimulationApplicant $simulationApplicant): bool
    {
        return $authUser->can('Restore:SimulationApplicant');
    }

    public function forceDelete(AuthUser $authUser, SimulationApplicant $simulationApplicant): bool
    {
        return $authUser->can('ForceDelete:SimulationApplicant');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SimulationApplicant');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SimulationApplicant');
    }

    public function replicate(AuthUser $authUser, SimulationApplicant $simulationApplicant): bool
    {
        return $authUser->can('Replicate:SimulationApplicant');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SimulationApplicant');
    }

}