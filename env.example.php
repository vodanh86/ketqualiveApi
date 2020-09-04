<?php 
 $env['database'] = array(
        'host'      => 'localhost',
        'user'      => 'ketqualive',
        'pass'      => 'tNkw0PEjj9FG0wl5',
        'name'      => 'xs',
        'prefix'    => 'mv_'
    );
    $env['domain'] = 'http://35.198.229.69';
    $env['static_domain'] = 'http://35.198.229.69';

    $env['cookie'] = array(
        'path'          => '/',
        'domain'        => '.',
        'prefix'        => 'xs_'
    );

    $env['memcache'] = array(
        'prefix'                => 'xs',
        'server'                => '127.0.0.1',
        'port'                  => 11211,
        'pconnect'              => 1,
        'timeout'               => 1
    );

