<?php

namespace App\Models\Users;

use App\Models\Users\Traits\Relationship\PermissionRelationship;
use App\Models\Users\Traits\Scope\PermissionScope;
use Spatie\Permission\Models\Permission as SpatiePermission;

/**
 * Class Permission.
 */
class Permission extends SpatiePermission
{
    use PermissionRelationship,
        PermissionScope;
}
