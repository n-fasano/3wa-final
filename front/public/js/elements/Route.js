class Route extends HTMLElement {
    constructor() {
        super();
    
        // this.attachShadow({ mode: 'open' });
        // this.shadowRoot.append(style, wrapper);

        console.log(this.innerHTML);
        window.onpopstate = function (event) {
            console.log(event);
        }
    }
}

customElements.define('app-route', Route);