import * as PIXI from '/node_modules/pixi.js/dist/pixi.min.mjs';

export class Boom {
    #REAL_WIDTH = 24;
    #REAL_HEIGHT = 32;
    #FRAME_NUM = 8;
    #FRAME_DELAY = 10;

    #game;
    #id;
    #width;
    #height;
    #displayObject;
    #isPlaying = false;

    #frameIndex = 0;
    #frameCount = 0;
    #frameDelayCount = 0;

    constructor(game, objectData) {
        this.#game = game;
        this.#id = `${objectData.x}-${objectData.y}`;

        const sizeRatio = this.#game.tileSize / this.#REAL_HEIGHT;
        this.#width = this.#REAL_WIDTH * sizeRatio;
        this.#height = this.#REAL_HEIGHT * sizeRatio;

        const texture = PIXI.Texture.from('/img/boom.png');
        const sprite = PIXI.TilingSprite.from(texture, {width: this.#width, height: this.#height});

        sprite.tileScale.x = sizeRatio;
        sprite.tileScale.y = sizeRatio;

        sprite.x = objectData.x * this.#game.tileSize + ((this.#game.tileSize - this.#width) / 2);
        sprite.y = objectData.y * this.#game.tileSize - 4;

        sprite.alpha = 0.9;

        this.#frameIndex = Math.floor(Math.random() * this.#FRAME_NUM);
        this.#isPlaying = true;

        this.#displayObject = sprite;
        this.#game.pixiApp.stage.addChild(this.#displayObject);
    }

    get id() {
        return this.#id;
    }

    get isPlaying() {
        return this.#isPlaying;
    }

    onFrame() {
        if (!this.#isPlaying) {
            return;
        }

        ++this.#frameDelayCount;
        if (this.#frameDelayCount == this.#FRAME_DELAY) {
            this.#frameDelayCount = 0;
        } else if (this.#frameDelayCount > 1) {
            return;
        }

        ++this.#frameCount
        if (this.#frameCount >= 15) {
            this.#destroy();
            return;
        }

        ++this.#frameIndex;
        if (this.#frameIndex >= this.#FRAME_NUM) {
            this.#frameIndex = 0;
        }

        this.#displayObject.tilePosition.x = -this.#width * this.#frameIndex;
    }

    #destroy() {
        this.#isPlaying = false;
        this.#game.pixiApp.stage.removeChild(this.#displayObject);
        this.#displayObject.destroy();
    }
}
