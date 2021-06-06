class Link extends HTMLAnchorElement {
    constructor() {
        super();

        this.addEventListener('click', function (e) {
            e.preventDefault();
            
            if (window.location.href !== this.href) {
                history.pushState({
                    path: window.location.pathname
                }, this.title, this.href);
            }
        });
    }
}

customElements.define('app-link', Link, { extends: 'a' });