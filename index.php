<?php

define('_TOKEN', '_token');

$DATETIMEZONE = new DateTimeZone('UTC');
date_default_timezone_set('UTC');

$_microtime = (int) (microtime(true) * 1000);
$_datetime = new DateTime('now', $DATETIMEZONE);
mb_internal_encoding('UTF-8');
mb_http_input('auto');
mb_http_output('UTF-8');
require __DIR__ . '/Setting.php';

require __DIR__ . '/require/rplib/ClassLoader.php';
require __DIR__ . '/require/rplib/functions.php';
require __DIR__ . '/require/exceptions.php';

define('DIR_ROOT', __DIR__ . '/');

$_loader = new ClassLoader();
$_loader->register_directory(__DIR__ . '/require');
$_loader->register_directory(__DIR__ . '/require/model');
$_loader->register_directory(__DIR__ . '/require/rplib');
$_loader->register_directory(__DIR__ . '/require/diffdb');
$_loader->register_directory(__DIR__ . '/require/vardb');
$_loader->register_directory(__DIR__ . '/require/modui');
$_loader->register_directory(__DIR__ . '/modui_components');
$_loader->register();

$_pdo = new EasySql(Setting::$_database_dsn, Setting::$_database_user, Setting::$_database_password);
//$_pdo->debug(true);
Setting::$_database_dsn = Setting::$_database_user = Setting::$_database_password = null;
$_vdb = new VarDB($_pdo, null);

require __DIR__ . '/tables.php';

$_request = new Request(_URL);
$_session = new Session();
$_cookie = new Cookie();
$_host_model = new HostModel($_pdo);
$_participant_model = new ParticipantModel($_pdo);
$_game_model = new GameModel($_pdo);
$_experiment_model = new ExperimentModel($_pdo);
//host
$_host_session = new User($_session, $_cookie, 'host',
    function($a)use($_host_model){
        return $_host_model->exist_id($a);
    },
    function($id, $key)use($_host_model){
        return $_host_model->check_auto_login($id, $key);
    },
    function($id)use($_host_model){
        $key = $_host_model->create_auto_login_key();
        $_host_model->update_auto_login_key($id, $key);
        return $key;
    }
);
$_participant_session = new User($_session, $_cookie, 'participant',
    function($a){
        return true;
    },
    function($a){
        return false;
    },
    function(){
        return null;
    }
);

require __DIR__ . '/page/switch.php';
