<?php

declare(strict_types=1);

namespace App\Policies\Simulation;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Simulation\SimulationProcess;
use Illuminate\Auth\Access\HandlesAuthorization;

class SimulationProcessPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SimulationProcess');
    }

    public function view(AuthUser $authUser, SimulationProcess $simulationProcess): bool
    {
        return $authUser->can('View:SimulationProcess');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SimulationProcess');
    }

    public function update(AuthUser $authUser, SimulationProcess $simulationProcess): bool
    {
        return $authUser->can('Update:SimulationProcess');
    }

    public function delete(AuthUser $authUser, SimulationProcess $simulationProcess): bool
    {
        return $authUser->can('Delete:SimulationProcess');
    }

    public function restore(AuthUser $authUser, SimulationProcess $simulationProcess): bool
    {
        return $authUser->can('Restore:SimulationProcess');
    }

    public function forceDelete(AuthUser $authUser, SimulationProcess $simulationProcess): bool
    {
        return $authUser->can('ForceDelete:SimulationProcess');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SimulationProcess');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SimulationProcess');
    }

    public function replicate(AuthUser $authUser, SimulationProcess $simulationProcess): bool
    {
        return $authUser->can('Replicate:SimulationProcess');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SimulationProcess');
    }

}