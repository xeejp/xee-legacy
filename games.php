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
        'directory' => 'public_goods',
        'name' => 'Public Goods Experiment',
    ],
];

foreach($games as $game){
    if($_pdo->get_count('game', ['directory' => $game['directory']]) !== 1){
        $_pdo->insert('game', $game);
    }
}
