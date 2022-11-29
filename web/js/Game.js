import * as PIXI from '/node_modules/pixi.js/dist/pixi.min.mjs';
import {Player} from '/js/Player.js';
import {Boom} from '/js/Boom.js';
import {Shield} from '/js/Shield.js';

export class Game
{
    #TILE_SIZE = 50;

    #elDashboard;
    #colNum = 0;
    #rowNum = 0;
    #playDataListLog;
    #soundBgm;
    #soundBoom;

    #pixiApp;
    #playerList = {};
    #boomList = {};
    #shieldList = {};

    #logIndex = -1;
    #isStop = false;
    #oncePlay = false;

    constructor(
        screenSelector,
        dashboardSelector,
        colNum,
        rowNum,
        playDataListLog
    ) {
        this.#elDashboard = document.querySelector(dashboardSelector);
        this.#colNum = colNum;
        this.#rowNum = rowNum;
        this.#playDataListLog = playDataListLog;
        this.#soundBgm = new Audio('/resources/bgm.mp3');
        this.#soundBgm.loop = true;
        this.#soundBgm.volume = 0.2;
        this.#soundBoom = new Audio('/resources/boom.wav');
        this.#soundBoom.volume = 0.2;

        this.#pixiApp = new PIXI.Application({
            width: this.#colNum * this.#TILE_SIZE,
            height: this.#rowNum * this.#TILE_SIZE
        });

        document.querySelector(screenSelector).append(this.#pixiApp.view);

        // background
        {
            const texture = PIXI.Texture.from('/img/ground.png');
            const sprite = PIXI.TilingSprite.from(texture, {width: 32, height: 32});
            sprite.width = this.#pixiApp.screen.width;
            sprite.height = this.#pixiApp.screen.height;
            this.#pixiApp.stage.addChild(sprite);
        }
    }

    play() {
        if (this.#oncePlay) {
            return;
        }

        this.#soundBgm.play();

        this.#oncePlay = true;
        const playDataListLog = this.#playDataListLog;

        this.#pixiApp.ticker.add(delta => {
            if (this.#isStop) {
                return;
            }

            let isContinue = false;

            for (const id in this.#playerList) {
                const player = this.#playerList[id];
                if (player.isPlaying) {
                    isContinue = true;
                    player.onFrame();
                }
            }

            for (const id in this.#shieldList) {
                const shield = this.#shieldList[id];
                if (shield.isPlaying) {
                    shield.onFrame();
                }
            }

            for (const id in this.#boomList) {
                const boom = this.#boomList[id];
                if (boom.isPlaying) {
                    isContinue = true;
                    boom.onFrame();
                } else {
                    delete this.#boomList[id];
                }
            }

            if (isContinue) {
                return;
            }

            ++this.#logIndex;

            if (this.#logIndex >= playDataListLog.length) {
                this.#stop();
                return;
            }

            let playSoundBoom = false;
            const objectDataList = playDataListLog[this.#logIndex];
            objectDataList.forEach(playData => {
                switch (playData.type) {
                    case 'player':
                        let player = this.#playerList[playData.id];

                        if (player) {
                            player.updateData(playData);
                        } else {
                            player = new Player(this, playData);
                            this.#playerList[player.id] = player;
                            this.#elDashboard.append(player.elPlayerInfo);
                        }
                        break;

                    case 'shield':
                        const shield = new Shield(this, playData);
                        this.#shieldList[shield.id] = shield;
                        break;

                    case 'remove_shield':
                        const id = playData.shield_id;
                        this.#shieldList[id].destroy();
                        delete this.#shieldList[id];
                        break;

                    case 'boom':
                        const boom = new Boom(this, playData);
                        this.#boomList[boom.id] = boom;
                        playSoundBoom = true;
                        break;
                }
            });

            if (playSoundBoom) {
                this.#soundBoom.play();
            }
        });
    }

    get tileSize() {
        return this.#TILE_SIZE;
    }

    get pixiApp() {
        return this.#pixiApp;
    }

    #stop() {
        this.#isStop = true;
        this.#showGameOver();
        this.#soundBgm.pause();
        setTimeout(() => {
            this.#pixiApp.ticker.stop();
            console.log('-STOP-');
        }, 1000);
    }

    #showGameOver() {
        const stage = this.#pixiApp.stage;
        const text = new PIXI.Text(' GAME OVER ', {
            fontFamily: 'CookieRunBold',
            fontSize: this.#TILE_SIZE,
            fill: 0xff1010,
            dropShadow: true,
            dropShadowBlur: 8,
            dropShadowColor: 0x000000,
            dropShadowDistance: 5,
            align: 'center',
        });

        text.anchor.set(0.5);
        text.position.x = stage.width / 2;
        text.position.y = stage.height / 2;

        stage.addChild(text);
    }
}