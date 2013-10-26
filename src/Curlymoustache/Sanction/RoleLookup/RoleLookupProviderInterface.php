<?php namespace Curlymoustache\Sanction\RoleLookup;

interface SanctionRoleLookupProvider {
    public function getRolesForUserId($user_id);
    public function getUsersForRoleId($role_id);
}
