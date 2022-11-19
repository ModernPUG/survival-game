'use strict';

const html = /*html*/`
    <style>
    :host {
        box-sizing: border-box;
        display: block;
        padding: 1em 1em;
        margin: .5em;
        border-radius: 20px;
        overflow: hidden;
        background-size: 300% 100%;
        background-image: linear-gradient(to right, #29323c, #485563, #2b5876, #4e4376);
        box-shadow: 0 4px 15px 0 rgba(45, 54, 65, 0.75);
        position: relative;
    }
    :host(.--death) {
        filter: grayscale(100%);
    }

    @keyframes blinker {
        50% {
            opacity: .5;
        }
    }

    .overlay {
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
        background-color: red;
        opacity: 0;
        border-radius: 20px;
    }
    .overlay--damage {
        animation: blinker .3s linear 2;
    }

    .player {
        display: flex;
    }

    .player__img {
        width: 40px;
        height: 40px;
        margin-right: 10px;
    }

    .player__name {
        font-family: 'CookieRunBold';
        color: #fff;
    }
    </style>

    <div class="overlay js-overlay"></div>
    <div class="player">
        <img class="player__img js-img" src="" />
        <div>
            <div class="player__name js-name"></div>
            <div class="player__hp js-hp"></div>
        </div>
    </div>
`;

class PlayerInfo extends HTMLElement {
    static get observedAttributes() {
        return ['name', 'img', 'hp'];
    }

    #shadow;

    constructor() {
        super();

        const shadow = this.attachShadow({mode: 'open'});
        shadow.innerHTML += html;
        this.#shadow = shadow;
    }

    attributeChangedCallback(name, oldValue, newValue) {
        const shadow = this.#shadow;

        switch (name) {
            case 'name':
                shadow.querySelector('.js-name').textContent = newValue;
                break;

            case 'img':
                shadow.querySelector('.js-img').src = newValue;
                break;

            case 'hp':
                shadow.querySelector('.js-hp').textContent = '❤️'.repeat(newValue);

                if (oldValue > newValue) {
                    const elOverlay = shadow.querySelector('.overlay');
                    elOverlay.classList.remove('overlay--damage');
                    setTimeout(() => {
                        elOverlay.classList.add('overlay--damage');
                        if (newValue == 0) {
                            this.classList.add('--death');
                        }
                    }, 1);
                }
                break;
        }
    }
}

customElements.define('x-player-info', PlayerInfo);
