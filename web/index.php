<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$game = \App\Game::createOnce(10, 10);
$game->play();
?>
<!DOCTYPE html>
<html>
<head>
    <title>2022 MPUG 송년회 게임</title>
    <meta charset="utf-8" />
    <style>
    @font-face {
        font-family: 'CookieRunBold';
        src: url('/fonts/CookieRunBold.otf') format("opentype");
    }
    html, body {
        font-family: 'CookieRunBold';
        height: 100%;
        margin: 0;
        padding: 0;
    }
    body {
        background-color: #4158D0;
        background-image: linear-gradient(43deg, #4158D0 0%, #C850C0 46%, #FFCC70 100%);
    }

    .l-container {
        padding: 2em;
    }

    .c-play {
        font-family: 'CookieRunBold';
        font-size: 1rem;
        color: #fff;
        appearance: none;
        border: none;
        padding: .5em 1em;
        border-radius: 5px;
        background-size: 300% 100%;
        background-image: linear-gradient(to right, #29323c, #485563, #2b5876, #4e4376);
        box-shadow: 0 4px 15px 0 rgba(45, 54, 65, 0.75);
        cursor: pointer;
    }

    .l-outline {
        margin-top: 1em;
        display: flex;
    }

    .l-screen {
        padding: 1em;
        border-radius: 8px;
        background-color: #29323c;
        box-shadow: 0 4px 15px 0 rgba(45, 54, 65, 0.75);
    }

    .l-dashboard {
        width: 400px;
    }
    </style>
</head>
<body>

<div class="l-container">
    <button class="c-play js-play">Play</button>

    <div class="l-outline">
        <div class="l-screen js-screen"></div>
        <div class="l-dashboard js-dashboard"></div>
    </div>
</div>

<script type="module">
import {Game} from '/js/Game.js';

const colNum = <?=$game->col_num?>;
const rowNum = <?=$game->row_num?>;
const playDataListLog = JSON.parse('<?=json_encode($game->getPlayDataListLog())?>');

const game = new Game(
    '.js-screen',
    '.js-dashboard',
    colNum,
    rowNum,
    playDataListLog
);

document.querySelector('.js-play').addEventListener('click', () => {
    game.play();
});
</script>

</body>
</html>