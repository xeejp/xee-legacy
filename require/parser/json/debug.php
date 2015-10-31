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
                [ 'type' => 'constant',
                  'value' => 'this is value.<br/>'
                ],
              ],
            ],
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
          'value' => 'static',
        ],
      ],
    ],
  ],
];
