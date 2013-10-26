<?php namespace Curlymoustache\Sanction;

use \Illuminate\Support\Facades\Facade;

class SanctionFacade extends Facade {

    protected static function getFacadeAccessor() { return 'sanction'; }

}
