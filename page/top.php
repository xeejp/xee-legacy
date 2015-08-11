<?php

$template = <<<TMPL
<ul>
{each url}
<li><a href="{URL}">{TEXT}</a></li>
{/each}
</ul>
TMPL;

$_tmpl = new Template();
$_tmpl->lwte_add('top', $template);
$_tmpl->lwte_use('#container', 'top', ['url' => [['URL' => _URL . 'top', 'TEXT' => 'top'], ['URL' => _URL . 'game', 'TEXT' => 'game']]]);
echo $_tmpl->display();
