<?php 
 $env['database'] = array(
        'host'      => 'localhost',
        'user'      => 'root',
        'pass'      => '',
        'name'      => 'xs',
        'prefix'    => 'mv_'
    );
    $env['domain'] = 'http://192.168.10.4/KetquaLiveAPI';
    $env['static_domain'] = 'http://192.168.10.4/KetquaLiveAPI';

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

