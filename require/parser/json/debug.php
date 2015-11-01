<?php
return $json =
[ 'type' => 'procedure',
  'descriptions' => [
    [ 'type' => 'function',
      'name' => 'function::define',
      'args' => [
        [ 'type' => 'constant',
          'value' => 'func'
        ],
        [ 'type' => 'user_function',
          'descriptions' => [
            [ 'type' => 'function',
              'name' => 'system::echo',
              'args' => [
                [ 'type' => 'variable',
                  'name' => 'counter',
                  'value' => [
                    'type' => 'function',
                    'name' => 'system::calc',
                    'args' => [
                      [ 'type' => 'variable',
                        'name' => 'counter',
                      ],
                      [ 'type' => 'constant',
                        'value' => '+',
                      ],
                      [ 'type' => 'constant',
                        'value' => 1,
                      ],
                    ],
                  ],
                ],
                [ 'type' => 'constant',
                  'value' => '++<br/>',
                ],
              ],
            ],
          ],
        ],
      ],
    ],
    [ 'type' => 'variable',
      'name' => 'counter',
      'value' =>
        [ 'type' => 'function',
          'name' => 'db::get',
          'args' => [
            [ 'type'=>'constant',
              'value'=>'counter'
            ],
            [ 'type'=>'constant',
              'value'=>0
            ],
          ],
        ],
    ],
    [ 'type' => 'function',
      'name' => 'system::loop',
      'args' => [
        [ 'type' => 'constant',
          'value' => 1,
        ],
        [ 'type' => 'user_function',
          'descriptions' => [
            [ 'type' => 'function',
              'name' => 'function::execute',
              'args' => [
                [ 'type' => 'constant',
                  'value' => 'func',
                ],
              ],
            ],
          ],
        ],
      ],
    ],
    [ 'type' => 'function',
      'name' => 'modui::OptionUI',
      'args' => [
        [ 'type' => 'constant',
          'value' => 'counter',
        ],
        [ 'type' => 'constant',
          'value' => 'counter',
        ],
      ],
    ],
    [ 'type' => 'function',
      'name' => 'db::set',
      'args' => [
        [ 'type'=>'constant',
          'value'=>'counter'
        ],
        [ 'type' => 'variable',
          'name' => 'counter',
        ],
      ],
    ],
  ],
];
