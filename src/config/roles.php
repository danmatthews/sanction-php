<?php

return array(

    'super_administrator' => array(
        'display_name' => 'Administrator',
        'permissions' => array(
            'news.create'
        ),
        'inherits_from' => array('standard_user', 'administrator'),
    ),
    'standard_user' => array(
        'display_name' => 'Standard user',
        'permissions' => array(
            'user.add',
            'user.create',
        ),
    ),
    'administrator' => array(
        'display_name' => 'Administrator',
        'permissions' => array(
            'user.publish'
        ),
        'inherits_from' => array('standard_user'),
    ),
);
