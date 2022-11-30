import * as PIXI from '/node_modules/pixi.js/dist/pixi.min.mjs';

export class Shield {
    #REAL_WIDTH = 192;
    #REAL_HEIGHT = 192;
    #FRAME_NUM = 10;
    #FRAME_DELAY = 20;

    #game;
    #id;
    #width;
    #height;
    #displayObject;
    #isPlaying = false;

    #frameIndex = 0;
    #frameDelayCount = 0;

    constructor(game, objectData) {
        this.#game = game;
        this.#id = objectData.id;

        const sizeRatio = this.#game.tileSize / this.#REAL_WIDTH;
        this.#width = this.#REAL_WIDTH * sizeRatio;
        this.#height = this.#REAL_HEIGHT * sizeRatio;

        const texture = PIXI.Texture.from('/img/shield.png');
        const sprite = PIXI.TilingSprite.from(texture, {width: this.#width, height: this.#height});

        sprite.tileScale.x = sizeRatio;
        sprite.tileScale.y = sizeRatio;

        sprite.x = objectData.x * this.#game.tileSize;
        sprite.y = objectData.y * this.#game.tileSize + 4;

        sprite.alpha = 0.7;

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

        ++this.#frameIndex;
        if (this.#frameIndex >= this.#FRAME_NUM) {
            this.#frameIndex = 0;
        }

        this.#displayObject.tilePosition.x = -this.#width * this.#frameIndex;
    }

    destroy() {
        this.#isPlaying = false;
        this.#game.pixiApp.stage.removeChild(this.#displayObject);
        this.#displayObject.destroy();
    }
}
