<?php namespace Curlymoustache\Sanction\RoleLookup;

use Illuminate\Support\Facades\DB as DB;

class LaravelDatabaseLookupProvider implements SanctionRoleLookupProvider {
    public function getRolesForUserId($user_id)
    {
        return DB::table('roles')->where('user_id', $user_id)->lists('role_id');
    }
}
