<?php

$template = <<<TMPL
<ul>
{each url}
<li><a href="{URL}">{TEXT}</a></li>
{/each}
</ul>
TMPL;

$template = str_replace('"', '\\"', str_replace("\n", "\\n", $template));
$data = json_encode(['url' => [['URL' => _URL . 'top', 'TEXT' => 'top'], ['URL' => _URL . 'game', 'TEXT' => 'game']]], true);
$_tmpl = new Template();
$_tmpl->add_script(<<<JS
lwte.addTemplate("top", "$template");
$("#container").html(lwte.useTemplate("top", $data));
JS
);
echo $_tmpl->display();
