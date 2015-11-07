<?php

$_con->{'participant'} = ['id' => 'host', 'name' => 'root'];
// board
$_con->add_component(new StaticUI('<hr/><center><h1 style="color:red">Super User</h1></center><hr/>'));
$_con->add_component(new BoardUI($_con, '掲示板もどき', true));

// systems
$_con->add_component(new StaticUI('<hr/><center>管理メニュー</center><hr/>'));
$_con->add_component(new StaticUI('Experiment ID : '));
$_con->add_component(new OptionUI($_con, 'experiment_pw', $_con->experiment['password']));
$_con->add_component(new ParticipantsList($_con));
$_con->add_component(new ParticipantsManagement($_con));