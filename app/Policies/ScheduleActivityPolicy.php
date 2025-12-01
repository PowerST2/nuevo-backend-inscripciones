<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ScheduleActivity;
use Illuminate\Auth\Access\HandlesAuthorization;

class ScheduleActivityPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ScheduleActivity');
    }

    public function view(AuthUser $authUser, ScheduleActivity $scheduleActivity): bool
    {
        return $authUser->can('View:ScheduleActivity');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ScheduleActivity');
    }

    public function update(AuthUser $authUser, ScheduleActivity $scheduleActivity): bool
    {
        return $authUser->can('Update:ScheduleActivity');
    }

    public function delete(AuthUser $authUser, ScheduleActivity $scheduleActivity): bool
    {
        return $authUser->can('Delete:ScheduleActivity');
    }

    public function restore(AuthUser $authUser, ScheduleActivity $scheduleActivity): bool
    {
        return $authUser->can('Restore:ScheduleActivity');
    }

    public function forceDelete(AuthUser $authUser, ScheduleActivity $scheduleActivity): bool
    {
        return $authUser->can('ForceDelete:ScheduleActivity');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ScheduleActivity');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ScheduleActivity');
    }

    public function replicate(AuthUser $authUser, ScheduleActivity $scheduleActivity): bool
    {
        return $authUser->can('Replicate:ScheduleActivity');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ScheduleActivity');
    }

}