<?php

$component = [ 'name' => 'normal', 'type' => 'normalcontainer',
    'option' => [
        'containment' => [
            // page container
            [ 'name' => 'page', 'type' => 'pagecontainer',
                'option' => [
                    'default' => '{global:page}',
                    'pages' => [
                        'index' => [ 'name' => 'index', 'type' => 'staticui', 'option' => ['value' => 'index'] ],
                        'experiment' => [ 'name' => 'experiment', 'type' => 'staticui', 'option' => ['value' => 'experiment'] ],
                        'result' => [ 'name' => 'result', 'type' => 'staticui', 'option' => ['value' => 'result'] ]
                    ]
                ]
            ],
            // normal container
            [ 'name' => 'normal', 'type' => 'normalcontainer',
                'option' => [
                    'containment' => [
                        [ 'name' => 'static1', 'type' => 'staticui', 'option' => ['value' => 's1'] ],
                        [ 'name' => 'static2', 'type' => 'staticui', 'option' => ['value' => 's2'] ],
                        [ 'name' => 'static3', 'type' => 'staticui', 'option' => ['value' => 's3'] ]
                    ]
                ]
            ],
            // static ui
            [ 'name' => 'static', 'type' => 'staticui',
                'option' => [
                    'value' => '{global:val}',
                ]
            ],
            [ 'name' => 'static', 'type' => 'staticui',
                'option' => [
                    'value' => 'abcdefgfffffffffffffffff',
                ]
            ],
        ]
    ]
];
$_con->set('page', 'result');
$_con->set('val', 'values');
$_con->add_component(parse($_con, $component));

function parse ($_con, $component) {
    $option = replace($_con, $component['option']);
    switch ($component['type']) {
    default:
        $result = new StaticUI('');
        break;
    case 'pagecontainer':
        $result = new PageContainer($option['default']);
        foreach($option['pages'] as $name => $page) {
            $result->add_page($name, parse($_con, $page));
        }
        break;
    case 'normalcontainer':
        $result = new NormalContainer();
        foreach($option['containment'] as $com) {
            $result->add(parse($_con, $com));
        }
        break;
    case 'staticui':
        $result = new StaticUI($option['value']. '<br/>');
        break;
    }
    return $result;
}

function replace ($_con, $option) {
    $result = [];
    foreach ($option as $key => $value) {
        if (is_array($value)) {
            $result[$key] = replace($_con, $value);
        } else {
            preg_match('/\{.+:.+\}/', $value, $targets);
            foreach ($targets as $target) {
                $pair = explode(':', preg_replace('/{(.+)}/', '$1', $target));
                switch ($pair[0]) {
                default:
                    $replacement = $_con->get($target, null);
                    break;
                case 'global':
                    $replacement = $_con->get($pair[1], null);
                    break;
                case 'personal':
                    $replacement = $_con->get_personal($pair[1], null);
                    break;
                }
                $value = preg_replace('/'. $target . '/', $replacement, $value);
            }
            $result[$key] = $value;
        }
    }
    return $result;
}
