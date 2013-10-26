<?php namespace Curlymoustache\Sanction;

use Zend\Permissions\Acl\Acl as Acl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Curlymoustache\Sanction\Cache\SanctionCacheProviderInterface as SanctionCacheProviderInterface;
use Curlymoustache\Sanction\RoleLookup\SanctionRoleLookupProviderInterface as SanctionRoleLookupProviderInterface;

class Sanction {

    /**
     * Stores a reference to the Zend ACL Object
     * @var Zend\Permissions\Acl\Acl
     */
    protected $zendAcl;

    /**
     * Roles (usually from a config file)
     * @var array
     */
    protected $roles;

    /**
     * A cache provider that allows, get, put and delete methods.
     * @var Curlymoustache\Sanction\Cache\SanctionCacheProviderInterface
     */
    protected $cacheProvider;

    /**
     * A role lookup provider that allows looking up role names for user IDs.
     * @var Curlymoustache\Sanction\RoleLookup\SanctionRoleLookupProviderInterface
     */
    protected $roleLookupProvider;

    public function __construct(array $roles, $cacheProvider = null, $roleLookupProvider = null)
    {
        $this->roles = $roles;
        $this->cacheProvider = $cacheProvider;
        $this->roleLookupProvider = $roleLookupProvider;
        $this->zendAcl = $this->resolve();
    }

    public function getDisplayNameForRole($role)
    {
        if (!empty($this->roles[$role])) {
            return $this->roles[$role];
        } else {
            throw new Exception('RoleNotFoundException');
        }
    }

    /**
     * Validate roles and permissions.
     * Ensure that the roles file doesn't declare any permissions that:
     * a) Inherit from themeselves.
     * b) Inherit from roles without permissions.
     * c) Are all valid arrays with the right keys.
     * d) No circular dependencies!
     *
     * @return bool
     */
    protected function validateAll()
    {

        foreach ($this->roles as $role_name => $data) {

            if (is_numeric($role_name)) {
                throw new \Exception('MalformedRoleArrayStructureException');
            }

            if (!is_array($data)) {
                throw new \Exception('MalformedRoleArrayStructureException');
            }

            if (
                !empty($data['inherits_from']) &&
                is_array($data['inherits_from']) &&
                in_array($role_name, $data['inherits_from'])
            ) {
                throw new \Exception('RoleInheritsFromItselfException');
            }

            if (
                !empty($data['inherits_from']) &&
                is_array($data['inherits_from'])
            ) {
                foreach($data['inherits_from'] as $i) {
                    if (
                        !empty($this->roles[$i]['inherits_from']) &&
                        is_array($this->roles[$i]['inherits_from']) &&
                        in_array($role_name, $this->roles[$i]['inherits_from'])
                    ) {
                        throw new \Exception("RoleCircularDependencyException");
                    }
                }
            }
        }
        return true;

    }

    /**
     * RECURSIVE WARNING!
     * This function recursively adds dependent roles
     * based on the configuration.
     *
     * @param [type] $name [description]
     * @param [type] $data [description]
     */
    public function registerRole(&$acl, $name, $data)
    {
        $this->roles[$name]['instance'] = new Role($name);
            $parents = [];

         if (
            !empty($data['inherits_from']) &&
            is_array($data['inherits_from'])
        ) {
            foreach ($data['inherits_from'] as $irole) {
                if (!in_array($irole, $acl->getRoles())) {
                    $this->registerRole($acl, $irole, $this->roles[$irole]);
                }
                $parents[] = $this->roles[$irole]['instance'];
            }
        }

        $acl->addRole(
            $this->roles[$name]['instance'],
            isset($parents) ? $parents : null
        );
    }

    /**
     * Turn our array into a Zend ACL Instance.
     * @return Zend\Permissions\Acl\Acl
     */
    protected function resolve()
    {
        $cache = $this->cacheProvider instanceof SanctionCacheProviderInterface;

        if ($cache && $this->cacheProvider->cacheExists()) {

            return $this->cacheProvider->get();

        } else {

            $acl = new Acl;

            // First register the roles.
            if ($this->validateAll()) {

                foreach ($this->roles as $role_name => $data) {
                    if (!in_array($role_name, $acl->getRoles())) {
                        $this->registerRole($acl, $role_name, $data);
                    }
                    foreach ($data['permissions'] as $permission) {
                        $acl->allow($role_name, null, $permission);
                    }
                }

            }



            // If Cache is enabled, store the info.
            if ($cache) {
                $this->cacheProvider->put($acl);
            }

            return $acl;
        }
    }


    /**
     * Get a list of permissions for a particular user ID.
     * @param  int $user_id
     * @return array
     */
    public function getPermissionsForUserId($user_id) {

        $roles = $this->roleLookupProvider->getRolesForUserId($user_id);

        $permissions = [];

        foreach ($roles as $role) {
            $permissions = array_merge($permissions, $this->roles[$role]['permissions']);
        }

        return $permissions;
    }

    /**
     * Set cache provider
     */
    public function setCacheProvider(SanctionCacheProviderInterface $provider)
    {
        $this->cacheProvider = $provider;
    }

    public function getRoleLookupProvider()
    {
        if ($this->roleLookupProvider instanceof SanctionRoleLookupProviderInterface) {
            return $this->roleLookupProvider;
        }
    }

    public function setRoleLookupProvider(SanctionRoleLookupProviderInterface $provider) {
        $this->roleLookupProvider = $provider;
    }

    /**
     * Get cache provider
     */
    public function getCacheProvider()
    {
        if ($this->cacheProvider instanceof SanctionCacheProviderInterface) {
            return $this->cacheProvider;
        } else {
            return false;
        }
    }

    public function clearCache()
    {
        if ($this->cacheProvider instanceof SanctionCacheProviderInterface) {
            return $this->cacheProvider->delete();
        } else {
            return false;
        }
    }

    /**
     * Can a user with the ID of $id access the $permission?
     * @param  int $user_id
     * @param  string $permission
     * @return bool
     */
    public function userHasPermission($user_id = null, $permission)
    {
        // Implement role lookup here.
        $roles = $this->getRolesForUserId((int)$user_id);
        foreach ($roles as $role) {
            if ( $this->roleHasPermission($role, $permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Can a user with the ID of $id access ALL the $permissions?
     * @param  int $user_id
     * @param  array $permissions
     * @return bool
     */
    public function userHasPermissions($user_id, array $permissions)
    {

        // Implement role lookup here.
        $roles = $this->getRolesForUserId((int)$user_id);

        $count = 0;

        foreach($permissions as $permission) {
            if ($this->userHasPermission($user_id, (string)$permission)) {
                $count++;
            }
        }
        return $count == count($permissions) ? true : false;
    }

    /**
     * Use this to check that role exists.
     * @return bool
     */
    public function roleExists($role_id)
    {
        return (bool)$this->zendAcl->getRole($role_id);
    }

    /**
     * Can a particular role access a permission?
     * @param  string $role
     * @param  string $permission
     * @return bool
     */
    public function roleHasPermission($role, $permission)
    {
        return $this->zendAcl->isAllowed($role, null, $permission);
    }

    /**
     * Get a list of roles for a particular user ID
     * @param  int $user_id
     * @return array
     */
    public function getRolesForUserId($user_id)
    {
        return $this->roleLookupProvider->getRolesForUserId($user_id);
    }

    public function getUsersForRoleId($role_id)
    {
        return $this->roleLookupProvider->getUsersForRoleId($role_id);
    }

    /**
     * Return the instance of Zend\Permissions\Acl\Acl we're using.
     * @return Zend\Permissions\Acl\Acl
     */
    public function getZendAcl()
    {
        return isset($this->zendAcl) ? $this->zendAcl : false;
    }

}
