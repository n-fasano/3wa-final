class Route extends HTMLElement {
    constructor() {
        super();
    }

    connectedCallback() {
        this.path = this.getAttribute('path');
        this.public = this.hasAttribute('public');
        this.redirect = this.getAttribute('redirect');
        this.component = this.getAttribute('component');

        Router.routes[this.path] = this;
    }
}

customElements.define('app-route', Route);