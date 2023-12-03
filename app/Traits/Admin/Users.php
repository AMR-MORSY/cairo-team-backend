<?php

namespace App\Traits\Admin;

use App\Models\Users\User;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


trait Users
{

    public function retrievAllUsersTable()
    {
        $users = User::paginate(2);

        return $users;
    }

    public function retrievUserData($id)
    {
        $user = User::find($id);
        $userRoles=$user->roles;
        $allRoles=$this->retrieveAllRoles();
        $data = [];
        if ($user) {
            $data["user"] = $user->toArray();
           
            $data["user_roles"] = $userRoles;
            $data["roles_diff"]=$allRoles->diff($userRoles);

            return $data;
        }
        return $data;
    }
    public function retrieveAllRoles()
    {
        $roles = Role::all();
        return $roles;
    }
    public function retrieveAllPermissions()
    {
        $permissions = Permission::all();
        return $permissions;
    }
    public function retrieveRoleAndAssignedPermissions($role_id)
    {
        $role = Role::find($role_id);
        $permissions=[];
        if ($role) {
            $permissions=$role->permissions;
            $data["role"]=$role;
            $data["permissions"]=$permissions;
           return $data;
        }
        $data["role"]=$role;
        $data["permissions"]=$permissions;
        return $data;
    }
}
