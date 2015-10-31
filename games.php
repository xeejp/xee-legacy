<?php

$games = [
    [
        'directory' => 'double_auction',
        'name' => 'Double Auction',
    ],
    [
        'directory' => 'test_auction',
        'name' => 'Test Auction',
    ],
    [
        'directory' => 'kage_exp',
        'name' => 'KAGE Experiment',
    ],
];

foreach($games as $game){
    if($_pdo->get_count('game', ['directory' => $game['directory']]) !== 1){
        $_pdo->insert('game', $game);
    }
}
