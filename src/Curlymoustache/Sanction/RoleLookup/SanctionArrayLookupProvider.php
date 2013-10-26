<?php namespace Curlymoustache\Sanction\RoleLookup;

class SanctionArrayLookupProvider implements SanctionRoleLookupProviderInterface {

    /**
     * The array of user data.
     * @var array
     */
    protected $data;

    /**
     * The primary key of each array item to treat as `user_id`
     * @var string
     */
    protected $primary_key;

    public function __construct(array $data, $primary_key)
    {
        $this->primary_key = $primary_key;
        $this->setData($data);
    }

    protected function validateData(array $data) {

        if (empty($data)) {
            throw new \ErrorException("The data array cannot be empty");
        }

        foreach ($data as $key => $value) {

            if (!is_array($value) || !is_numeric($key)) {
                throw new \ErrorException("Invalid data, must be an array containing multiple associative arrays.");
            }

            if (!array_key_exists($this->primary_key, $value)) {
                throw new \ErrorException("Invalid data, the primary key must exist in all items of the data array.");
            }

        }

        return true;

    }

    public function setData(array $data)
    {
        if ($this->validateData($data)) {
            $this->data = $data;
        }
    }

    public function getRolesForUserId($user_id)
    {
        foreach($this->data as $user)
        {
            if (array_key_exists($this->primary_key, $user)) {

                if ($user[$this->primary_key] == $user_id) {
                    return $user['roles'];
                }

            } else {
                continue;
            }
        }
    }

    public function getUsersForRoleId($role_id)
    {

    }

}
