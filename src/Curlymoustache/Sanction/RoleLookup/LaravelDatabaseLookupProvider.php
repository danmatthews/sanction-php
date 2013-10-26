<?php namespace Curlymoustache\Sanction\RoleLookup;

use Illuminate\Support\Facades\DB as DB;

class LaravelDatabaseLookupProvider implements SanctionRoleLookupProviderInterface {

    protected $tableName = 'roles';

    public function getRolesForUserId($user_id)
    {
        $result = DB::table('roles')->where('user_id', $user_id)->lists('role_id');
        if (empty($result)) {
            throw new \Curlymoustache\Sanction\Exceptions\NoRolesForUserIdException("There are no roles associated with User ID {$user_id}");
        }
        return $result;
    }

    public function getUsersForRoleId($role_id)
    {
        $result = DB::table('roles')->where('role_id', $role_id)->lists('user_id');
        if (empty($result)) {
            throw new \Curlymoustache\Sanction\Exceptions\NoUsersForRoleException("There are no users associated with Role ID {$role_id}");
        }
        return $result;
    }

    public function getTableName()
    {
        return $this->tableName;
    }

    public function setTableName($name)
    {
        $this->tableName = $name;
    }
}
