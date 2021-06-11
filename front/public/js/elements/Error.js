class Error extends HTMLDivElement {
    constructor() {
        super();
    }
}

customElements.define('app-error', Link, { extends: 'div' });