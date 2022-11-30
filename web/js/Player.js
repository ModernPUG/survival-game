import * as PIXI from '/node_modules/pixi.js/dist/pixi.min.mjs';
import '/js/x-player-info.js';

export class Player {
    #SIZE = 30;
    #PADDING = 4;

    #game;
    #id;
    #name;
    #hp;
    #shield;
    #message;
    #x;
    #y;

    #displayObject;
    #elPlayerInfo;
    #isPlaying = false;

    constructor(game, objectData) {
        this.#game = game;
        this.#SIZE = this.#game.tileSize;

        this.#id = objectData.id;
        this.#name = objectData.name;
        this.#hp = objectData.hp;
        this.#shield = objectData.shield;
        this.#message = objectData.message;
        this.#x = objectData.x;
        this.#y = objectData.y;

        const displayObject = new PIXI.Graphics();
        displayObject.drawRect(0, 0, this.#SIZE, this.#SIZE);

        const imgSrc = `/img/users/${this.#id}.png`;
        const sprite = PIXI.Sprite.from(imgSrc);
        sprite.width = this.#SIZE - (this.#PADDING * 2);
        sprite.height = this.#SIZE - (this.#PADDING * 2);

        displayObject.addChild(sprite);
        sprite.x = this.#PADDING;
        sprite.y = this.#PADDING;

        displayObject.x = this.#x * this.#SIZE;
        displayObject.y = this.#y * this.#SIZE;

        this.#displayObject = displayObject;
        this.#game.pixiApp.stage.addChild(this.#displayObject);

        const elPlayerInfo = document.createElement('x-player-info');
        elPlayerInfo.setAttribute('name', this.#name);
        elPlayerInfo.setAttribute('img', imgSrc);
        elPlayerInfo.setAttribute('hp', this.#hp);
        elPlayerInfo.setAttribute('shield', this.#shield);
        this.#elPlayerInfo = elPlayerInfo;
    }

    get id() {
        return this.#id;
    }

    get name() {
        return this.#name;
    }

    get hp() {
        return this.#hp;
    }

    get shield() {
        return this.#shield;
    }

    get message() {
        return this.#message;
    }

    get displayObject() {
        return this.#displayObject;
    }

    get coordX() {
        return this.#x * this.#SIZE;
    }

    get coordY() {
        return this.#y * this.#SIZE;
    }

    get elPlayerInfo() {
        return this.#elPlayerInfo;
    }

    get isPlaying() {
        return this.#isPlaying;
    }

    updateData(objectData) {
        if (this.#hp < 1) {
            return;
        }

        this.#hp = objectData.hp;
        this.#shield = objectData.shield;
        this.#message = objectData.message;
        this.#x = objectData.x;
        this.#y = objectData.y;
        this.#isPlaying = true;

        this.#elPlayerInfo.setAttribute('hp', this.#hp);
        this.#elPlayerInfo.setAttribute('shield', this.#shield);
        this.#elPlayerInfo.setAttribute('message', this.#message);

        if (this.#hp < 1) {
            this.#destroy();
        }
    }

    onFrame() {
        if (!this.#isPlaying) {
            return;
        }

        let moveX = 0;
        let moveY = 0;

        const disObj = this.#displayObject;

        if (disObj.x < this.coordX) {
            moveX = 1;
        } else if (disObj.x > this.coordX) {
            moveX = -1;
        }

        if (disObj.y < this.coordY) {
            moveY = 1;
        } else if (disObj.y > this.coordY) {
            moveY = -1;
        }

        disObj.x += moveX;
        disObj.y += moveY;

        if (
            disObj.x == this.coordX
            && disObj.y == this.coordY
        ) {
            this.#isPlaying = false;
        }
    }

    #destroy() {
        this.#isPlaying = false;
        this.#game.pixiApp.stage.removeChild(this.#displayObject);
        this.#displayObject.destroy();
    }
}