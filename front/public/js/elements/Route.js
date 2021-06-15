class Route extends HTMLElement {
    constructor() {
        super();
    }

    connectedCallback() {
        this.path = this.getAttribute('path');
        this.dynamic = this.path.includes('{');
        this.public = this.hasAttribute('public');
        this.redirect = this.getAttribute('redirect');
        this.component = this.getAttribute('component');

        const routeCollection = this.dynamic ? 'dynamicRoutes' : 'routes';
        Router[routeCollection][this.path] = this;
    }
}

customElements.define('app-route', Route);