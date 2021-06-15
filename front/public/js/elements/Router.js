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
    
        window.addEventListener('replacestate', this.handleNavigation);
        window.addEventListener('pushstate', this.handleNavigation);
        window.addEventListener('popstate', this.handleNavigation);

        Router.self = this;
    }

    static self;
    static body;
    static routes = {};
    static dynamicRoutes = {};
    static components = {};

    async handleNavigation(event) {
        const currentPath = window.location.pathname;
        let route = Router.routes[currentPath] ?? null;
        let state = {};

        if (null === route) {
            [route, state] = Router.match(currentPath);
        }

        if (null === route) {
            route = Router.routes['/404'];
        }

        if (null !== route.redirect) {
            route = Router.routes[route.redirect];
        }

        if (!route.public && !User.logged) {
            route = Router.routes['/login'];
        }

        const template = await Router.getTemplate(route.component);
        Router.self.innerHTML = '';
        Router.body = new Component({
            template,
            root: Router.self,
            state: {}
        });
        Router.body.setState(state);

        const scripts = Router.self.querySelectorAll('script');
        scripts.forEach(ScriptLinker.replaceScriptNode);
    }

    static match(path) {
        const parts = path.split('/');

        nextRoute:
        for (const routePath in Router.dynamicRoutes) {

            const routeParts = routePath.split('/');
            if (parts.length !== routeParts.length) {
                continue;
            }

            const parameters = {};
            for (let i = 0; i < parts.length; i++) {
                const part = parts[i];
                const routePart = routeParts[i];

                if (routePart.includes('{')) {
                    const variable = routePart.replace(/\{|\}/g, '');
                    parameters[variable] = part;
                    continue;
                }

                if (part !== routePart) {
                    continue nextRoute;
                }
            }

            return [Router.dynamicRoutes[routePath], parameters];
        }

        return [null, null];
    }

    static initialize() {
        Router.self.handleNavigation(null);
    }

    static async getTemplate(component) {
        if (undefined === Router.components[component]) {
            Router.components[component] = await fetch(`/tpl/pages/${component}.html`)
                .then(function (response) {
                    return response.text();
                });
        }

        return Router.components[component];
    }
}

customElements.define('app-router', Router);