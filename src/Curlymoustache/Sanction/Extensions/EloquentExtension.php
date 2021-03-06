<?php namespace Curlymoustache\Sanction\Extensions;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

trait EloquentExtension {

    /**
     * Lovely static function that returns an eloquent
     * collection of users with a set of role IDs.
     * @param  mixed $role_id
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function usersWithRole($role_id)
    {
        $sanction = App::make('sanction');
        $ids = $sanction->getUsersForRoleId($role_id);
        return parent::whereIn('id', $ids)->get();
    }

    /**
     * Return all roles for this user.
     * @return array
     */
    public function roles()
    {
        return App::make('sanction')->getRolesForUserId($this->id);
    }

    public function is($role_name) {
        return in_array($role_name, $this->roles());
    }

    /**
     * Add a role to user.
     * @param  string $role_id
     * @return bool
     */
    public function addRole($role_id) {

        $sanction = App::make('sanction');

        $roles = $this->getRoles();

        if (!in_array($role_id, $roles)) {

            // Ensure the role exists.

            if ($sanction->roleExists($role_id)) {
                return DB::table($sanction->getRoleLookupProvider()->getTableName())->insert([
                    [
                        'role_id' => $role_id,
                        'user_id' => $this->id,
                    ]
                ]);
            }
        } else {
            throw new \Curlymoustache\Sanction\Exceptions\UserHasRoleException("The user with ID {$this->id} already has the role '{$role_id}'");
        }
    }

    /**
     * This will allow you to determine if a user can perform an action.
     * @param  string  $permission
     * @return boolean
     */
    protected function hasPermission($permission)
    {
        $sanction = App::make('sanction');
        return $sanction->userHasPermission($this->id, $permission);
    }

    /**
     * Alias of hasPermission and hasPermissions
     * @param  array|string $permission
     * @return bool
     */
    public function can($permission)
    {
        if (is_array($permission)) {
            return $this->hasPermissions($permission);
        } else {
            return $this->hasPermission($permission);
        }
    }

    /**
     * This will allow you to determine if a user can perform an action.
     * @param  string  $permission
     * @return boolean
     */
    protected function hasPermissions(array $permissions)
    {
        $sanction = App::make('sanction');
        return $sanction->userHasPermissions($this->id, $permissions);
    }


    public function getPermissions()
    {
        $sanction = App::make('sanction');
        return $sanction->getPermissionsForUserId($this->id);
    }

    /**
     * Delete a role from a user.
     * @param string $role_id
     * @return bool
     */
    public function deleteRole($role_id)
    {
        $sanction = App::make('sanction');
        $roles = $this->getRoles();

        if (in_array($role_id, $roles)) {
            return DB::table(
                $sanction->getRoleLookupProvider()->getTableName()
            )
            ->where('user_id', $this->id)
            ->where('role_id', $role_id)
            ->delete();
        }
    }
}
