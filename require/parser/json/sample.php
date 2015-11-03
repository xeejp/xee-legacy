<?php
$json = [
// 1から6までのランダムな数値を変数[[rand_name]]に代入するサブルーチンを定義
    [ 'type' => 'package',
      'name' => 'function::define',
      'args' => [
        [ 'type' => 'constant',
          'value' => 'u_get_rand'
        ],
        [ 'type' => 'function',
          'descriptions' => [
            [ 'type' => 'package',
              'name' => 'db::set',
              'args' => [
                [ 'type' => 'package',
                  'name' => 'db::get',
                  'args' => [
                    [ 'type' => 'constant',
                      'value' => 'rand_name',
                    ],
                  ],
                ],
                [ 'type' => 'package',
                  'name' => 'system::mt_rand',
                  'args' => [
                    [ 'type' => 'constant',
                      'value' => 1
                    ],
                    [ 'type' => 'constant',
                      'value' => 6
                    ],
                  ],
                ],
              ],
            ],
          ],
        ],
      ],
    ],
// 変数[[result_name]]を加算
    [ 'type' => 'package',
      'name' => 'function::define',
      'args' => [
        [ 'type' => 'constant',
          'value' => 'u_add_result'
        ],
        [ 'type' => 'function',
          'descriptions' => [
            [ 'type' => 'package',
              'name' => 'db::set',
              'args' => [
                [ 'type' => 'package',
                  'name' => 'db::get',
                  'args' => [
                    [ 'type' => 'constant',
                      'value' => 'result_name',
                    ],
                  ],
                ],
                [ 'type' => 'package',
                  'name' => 'system::calc',
                  'args' => [
                    [ 'type' => 'package',
                      'name' => 'db::get',
                      'args' => [
                        [ 'type' => 'package',
                          'name' => 'db::get',
                          'args' => [
                            [ 'type' => 'constant',
                              'value' => 'result_name',
                            ],
                          ],
                        ],
                      ],
                    ],
                    [ 'type' => 'constant',
                      'value' => '+'
                    ],
                    [ 'type' => 'constant',
                      'value' => '1'
                    ],
                  ],
                ],
              ],
            ],
          ],
        ],
      ],
    ],
// 1ゲーム分のサブルーチン
    [ 'type' => 'package',
      'name' => 'function::define',
      'args' => [
        [ 'type' => 'constant',
          'value' => 'game',
        ],
        [ 'type' => 'function',
          'descriptions' => [
            // サイコロを振る (player)
            [ 'type' => 'package',
              'name' => 'db::set',
              'args' => [
                [ 'type' => 'constant',
                  'value' => 'rand_name',
                ],
                [ 'type' => 'constant',
                  'value' => 'player',
                ],
              ],
            ],
            [ 'type' => 'package',
              'name' => 'function::execute',
              'args' => [
                [ 'type' => 'constant',
                  'value' => 'u_get_rand',
                ],
              ],
            ],
            // サイコロを振る (enemy)
            [ 'type' => 'package',
              'name' => 'db::set',
              'args' => [
                [ 'type' => 'constant',
                  'value' => 'rand_name',
                ],
                [ 'type' => 'constant',
                  'value' => 'enemy',
                ],
              ],
            ],
            [ 'type' => 'package',
              'name' => 'function::execute',
              'args' => [
                [ 'type' => 'constant',
                  'value' => 'u_get_rand',
                ],
              ],
            ],
            // 出目を表示
            [ 'type' => 'package',
              'name' => 'modui::StaticUI',
              'args' => [
                [ 'type' => 'constant',
                  'value' => 'あなたの目は',
                ],
                [ 'type' => 'package',
                  'name' => 'db::get',
                  'args' => [
                    [ 'type' => 'constant',
                      'value' => 'player'
                    ],
                  ],
                ],
                [ 'type' => 'constant',
                  'value' => 'です<br/>',
                ],
                [ 'type' => 'constant',
                  'value' => '相手の目は',
                ],
                [ 'type' => 'package',
                  'name' => 'db::get',
                  'args' => [
                    [ 'type' => 'constant',
                      'value' => 'enemy'
                    ],
                  ],
                ],
                [ 'type' => 'constant',
                  'value' => 'です<br/>',
                ],
              ],
            ],
            // 結果
            [ 'type' => 'package',
              'name' => 'system::if',
              'args' => [
                [ 'type' => 'package',
                  'name' => 'system::bool',
                  'args' => [
                    [ 'type' => 'package',
                      'name' => 'db::get',
                      'args' => [
                        [ 'type' => 'constant',
                          'value' => 'player',
                        ],
                      ],
                    ],
                    [ 'type' => 'constant',
                      'value' => '>'
                    ],
                    [ 'type' => 'package',
                      'name' => 'db::get',
                      'args' => [
                        [ 'type' => 'constant',
                          'value' => 'enemy'
                        ],
                      ],
                    ],
                  ],
                ],
                [ 'type' => 'function',
                  'descriptions' => [
                    [ 'type' => 'package',
                      'name' => 'modui::StaticUI',
                      'args' => [
                        [ 'type' => 'constant',
                          'value' => 'あなたの勝ち<br/>',
                        ],
                      ],
                    ],
                    [ 'type' => 'package',
                      'name' => 'db::set',
                      'args' => [
                        [ 'type' => 'constant',
                          'value' => 'result_name',
                        ],
                        [ 'type' => 'constant',
                          'value' => 'win',
                        ],
                      ],
                    ],
                  ],
                ],
                [ 'type' => 'function',
                  'descriptions' => [
                    [ 'type' => 'package',
                      'name' => 'system::if',
                      'args' => [
                        [ 'type' => 'package',
                          'name' => 'system::bool',
                          'args' => [
                            [ 'type' => 'package',
                              'name' => 'db::get',
                              'args' => [
                                [ 'type' => 'constant',
                                  'value' => 'player',
                                ],
                              ],
                            ],
                            [ 'type' => 'constant',
                              'value' => '<'
                            ],
                            [ 'type' => 'package',
                              'name' => 'db::get',
                              'args' => [
                                [ 'type' => 'constant',
                                  'value' => 'enemy'
                                ],
                              ],
                            ],
                          ],
                        ],
                        [ 'type' => 'function',
                          'descriptions' => [
                            [ 'type' => 'package',
                              'name' => 'modui::StaticUI',
                              'args' => [
                                [ 'type' => 'constant',
                                  'value' => '相手の勝ち<br/>',
                                ],
                              ],
                            ],
                            [ 'type' => 'package',
                              'name' => 'db::set',
                              'args' => [
                                [ 'type' => 'constant',
                                  'value' => 'result_name',
                                ],
                                [ 'type' => 'constant',
                                  'value' => 'lose',
                                ],
                              ],
                            ],
                          ],
                        ],
                      ],
                    ],
                  ],
                ],
              ],
            ],
            // 引分ならもう一回
            [ 'type' => 'package',
              'name' => 'system::if',
              'args' => [
                [ 'type' => 'package',
                  'name' => 'system::bool',
                  'args' => [
                    [ 'type' => 'package',
                      'name' => 'db::get',
                      'args' => [
                        [ 'type' => 'constant',
                          'value' => 'player',
                        ],
                      ],
                    ],
                    [ 'type' => 'constant',
                      'value' => '=='
                    ],
                    [ 'type' => 'package',
                      'name' => 'db::get',
                      'args' => [
                        [ 'type' => 'constant',
                          'value' => 'enemy'
                        ],
                      ],
                    ],
                  ],
                ],
                [ 'type' => 'function',
                  'descriptions' => [
                    [ 'type' => 'package',
                      'name' => 'modui::StaticUI',
                      'args' => [
                        [ 'type' => 'constant',
                          'value' => '引分 - やり直し<br/>',
                        ],
                      ],
                    ],
                    [ 'type' => 'package',
                      'name' => 'function::execute',
                      'args' => [
                        [ 'type' => 'constant',
                          'value' => 'game',
                        ],
                      ],
                    ],
                  ],
                ],
              ],
            ],
          ],
        ],
      ],
    ],
// 説明表示
    [ 'type' => 'package',
      'name' => 'modui::StaticUI',
      'args' => [
        [ 'type' => 'constant',
          'value' => 'サイコロ勝負(出目の大きい方が勝ち)<br/>',
        ],
      ],
    ],
// ゲームを数回行う
    [ 'type' => 'package',
      'name' => 'modui::StaticUI',
      'args' => [
        [ 'type' => 'constant',
          'value' => '--------------------<br/>',
        ],
      ],
    ],
    [ 'type' => 'package',
      'name' => 'system::loop',
      'args' => [
        [ 'type' => 'constant',
          'value' => 5,
        ],
        [ 'type' => 'function',
          'descriptions' => [
            [ 'type' => 'package',
              'name' => 'function::execute',
              'args' => [
                [ 'type' => 'constant',
                  'value' => 'game',
                ],
              ],
            ],
            [ 'type' => 'package',
              'name' => 'modui::StaticUI',
              'args' => [
                [ 'type' => 'constant',
                  'value' => '--------------------<br/>',
                ],
              ],
            ],
          ],
        ],
      ],
    ],
// 結果処理
    [ 'type' => 'package',
      'name' => 'function::execute',
      'args' => [
        [ 'type' => 'constant',
          'value' => 'u_add_result',
        ],
      ],
    ],
// 勝率計算
    [ 'type' => 'package',
      'name' => 'modui::StaticUI',
      'args' => [
        [ 'type' => 'constant',
          'value' => '勝率 : ',
        ],
        [ 'type' => 'package',
          'name' => 'system::calc',
          'args' => [
            [ 'type' => 'package',
              'name' => 'db::get',
              'args' => [
                [ 'type' => 'constant',
                  'value' => 'win',
                ],
              ],
            ],
            [ 'type' => 'constant',
              'value' => '/',
            ],
            [ 'type' => 'package',
              'name' => 'system::calc',
              'args' => [
                [ 'type' => 'package',
                  'name' => 'db::get',
                  'args' => [
                    [ 'type' => 'constant',
                      'value' => 'win',
                    ],
                  ],
                ],
                [ 'type' => 'constant',
                  'value' => '+',
                ],
                [ 'type' => 'package',
                  'name' => 'db::get',
                  'args' => [
                    [ 'type' => 'constant',
                      'value' => 'lose',
                    ],
                  ],
                ],
              ],
            ],
          ],
        ],
      ],
    ],
];
return json_encode($json);