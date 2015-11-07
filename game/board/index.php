<?php

$_con->add_component(new redirectUI(_URL, $_con->get_personal('active', false) != true));
// pages
$pages[false] = new StaticUI('');
$pages[true] = new BoardUI($_con, '掲示板もどき', false);

// add pages
$_con->add_component($page_con = new PageContainer(
    function () use ($_con) {
        return $_con->get_personal('active', false);
    }
));
foreach($pages as $name => $page)
    $page_con->add_page($name, $page);
