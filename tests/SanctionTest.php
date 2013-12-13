<?php

// Require the composer dependancies.
require __DIR__.'/../vendor/autoload.php';

class SanctionTest extends PHPUnit_Framework_TestCase {

	protected $lookup_provider;

	public function getTestRules()
	{
		return [
		    'standard_user' => [
			    'display_name' => 'Standard user',
		        'permissions' => [
		            'create_users',
		            'update_users',
		        ],
		    ],
		    'admin' => [
			    'display_name' => 'Administrator',
		        'permissions' => [
		            'delete_users',
		        ],
		        'inherits_from' => ['standard_user']
		    ],
		];
	}

	public function getTestUsers()
	{
		return [
			[
				'uid' => 10,
				'name' => 'Jim Kirk',
				'roles' => ['admin']
			],
			[
				'uid' => 32,
				'name' => 'Bones',
				'roles' => ['standard_user']
			],
		];
	}

	public function testArrayLookupProviderInit()
	{
		$users = $this->getTestUsers();
		$lookup_provider = new Curlymoustache\Sanction\RoleLookup\SanctionArrayLookupProvider($users, 'uid');
		$this->assertTrue($lookup_provider instanceof Curlymoustache\Sanction\RoleLookup\SanctionArrayLookupProvider);
		$implements = class_implements($lookup_provider);
		$this->assertTrue(in_array('Curlymoustache\Sanction\RoleLookup\SanctionRoleLookupProviderInterface', $implements));
		return $lookup_provider;
	}
	/**
	 * @depends testArrayLookupProviderInit
	 */
	public function testSanctionInitWithArrayLookupProvider(Curlymoustache\Sanction\RoleLookup\SanctionRoleLookupProviderInterface $lookup_provider)
	{
		$rules = $this->getTestRules();
		$sanction = new Curlymoustache\Sanction\Sanction($rules, null, $lookup_provider);
		$this->assertTrue($sanction instanceof Curlymoustache\Sanction\Sanction);
		return $sanction;
	}

	/**
	 * @depends testSanctionInitWithArrayLookupProvider
	 */
	public function testGetDisplayNameForRole(Curlymoustache\Sanction\Sanction $sanction)
	{
		$rules = $this->getTestRules();
		$values = array_values($rules);
		$rule = $values[0];
		$keys = array_values(array_keys($rules));
		$rolename = $keys[0];
		$name = $rule['display_name'];

		$this->assertEquals($name, $sanction->getDisplayNameForRole($rolename), 'Assert that the get display name method works');
	}

	public function setUpBaseLookupProvider()
	{
		$users = $this->getTestUsers();
		return new Curlymoustache\Sanction\RoleLookup\SanctionArrayLookupProvider($users, 'uid');
	}

	/**
	 * Pass intentionally bad values into the sanction $rules array
	 * to trigger the alerts - malformed array.
	 *
	 * @return void
	 */
	public function testValidateAll()
	{
		$lp = $this->setUpBaseLookupProvider();
		$rules = [];
		$sanction = new Curlymoustache\Sanction\Sanction($rules, null, $lp);
	}

}
