<?php namespace Curlymoustache\Sanction\RoleLookup;

interface SanctionRoleLookupProviderInterface {
    public function getRolesForUserId($user_id);
    public function getUsersForRoleId($role_id);
}
