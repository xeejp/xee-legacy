<?php

switch($_request->get_uri(0, 1)){
case 'debug':
    require __DIR__ . '/debug.php';
    break;
case 'host':
    require __DIR__ . '/host.php';
    break;
case 'signin':
    require __DIR__ . '/signin.php';
    break;
case 'signup':
    require __DIR__ . '/signup.php';
    break;
case 'signout':
    require __DIR__ . '/signout.php';
    break;
case 'api':
    $_api = true;
case 'game':
    if(!isset($_api) || $_api !== true) $_api = false;
    require __DIR__ . '/game.php';
    break;
default:
    require __DIR__ . '/top.php';
    break;
}
