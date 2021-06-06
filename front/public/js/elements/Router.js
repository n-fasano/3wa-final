// https://stackoverflow.com/a/25673911
var _wr = function(type, eventClass = 'Event') {
    var orig = history[type];
    const eventName = type.toLowerCase();
    return function() {
        var rv = orig.apply(this, arguments);
        var e = new (eventClass)(eventName);
        e.arguments = arguments;
        window.dispatchEvent(e);
        return rv;
    };
};

class PushStateEvent extends Event {}
class ReplaceStateEvent extends Event {}
history.pushState = _wr('pushState', PushStateEvent);
history.replaceState = _wr('replaceState', ReplaceStateEvent);


class Router extends HTMLElement {
    constructor() {
        super();
    
        window.addEventListener('pushstate', this.handleNavigation);
        window.addEventListener('popstate', this.handleNavigation);

        Router.self = this;
    }

    static self;
    static routes = {};
    static components = {};

    async handleNavigation(event) {
        const currentPath = window.location.pathname;
        let route = Router.routes[currentPath];

        if (null !== route.redirect) {
            route = Router.routes[route.redirect];
        }

        if (!route.public && !user.logged) {
            route = Router.routes['/login'];
        }

        const html = await Router.getHtml(route.component);
        Router.self.innerHTML = html;
    }

    static initialize() {
        Router.self.handleNavigation(null);
    }

    static async getHtml(component) {
        if (undefined === Router.components[component]) {
            Router.components[component] = await fetch(`/tpl/${component}.html`)
                .then(function (response) {
                    return response.text();
                });
        }

        return Router.components[component];
    }
}

customElements.define('app-router', Router);