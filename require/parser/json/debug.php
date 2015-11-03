<?php
$json = [
    [ 'type' => 'package',
      'name' => 'function::define',
      'args' => [
        [ 'type' => 'constant',
          'value' => 'func'
        ],
        [ 'type' => 'function',
          'descriptions' => [
            [ 'type' => 'return',
              'value' => [
                'type' => 'constant',
                'value' => 'return!',
              ],
            ],
            [ 'type' => 'package',
              'name' => 'system::echo',
              'args' => [
                [ 'type' => 'variable',
                  'name' => 'counter',
                  'value' => [
                    'type' => 'package',
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
        [ 'type' => 'package',
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
    [ 'type' => 'package',
      'name' => 'system::loop',
      'args' => [
        [ 'type' => 'constant',
          'value' => 10,
        ],
        [ 'type' => 'function',
          'descriptions' => [
            [ 'type' => 'package',
              'name' => 'modui::StaticUI',
              'args' => [
                [ 'type' => 'package',
                  'name' => 'function::execute',
                  'args' => [
                    [ 'type' => 'constant',
                      'value' => 'func',
                    ],
                  ],
                ],
              ],
            ]
          ],
        ],
      ],
    ],
    [ 'type' => 'package',
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
    [ 'type' => 'package',
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
];
return json_encode($json);
