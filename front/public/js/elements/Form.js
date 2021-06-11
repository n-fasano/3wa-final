class Form extends HTMLFormElement {
    constructor() {
        super();

        this.addEventListener('submit', function (e) {
            e.preventDefault();
            if (!this.checkValidity()) {
                return;
            }

            const self = this;

            fetch(`/api${this.actionPath}`, {
                method: 'POST',
                body: new FormData(this)
            })
                .then(function(response) {
                    return response.json();
                })
                .then(function (json) {
                    if (undefined !== json.error) {
                        if (null !== self['onError']) {
                            window[self['onError']](json.error);
                        }
                        window.onError(json.error);
                    } else {
                        if (null !== self['onSuccess']) {
                            window[self['onSuccess']](json);
                        }
                        window.onSuccess(json);

                        history.pushState({
                            path: window.location.pathname
                        }, self.id, self.redirect);
                    }
                })
                .catch(function (error) {
                    window.onError('Something went wrong.');
                });
        });
    }

    connectedCallback() {
        this.actionPath = this.action.replace(location.origin, '');
        this.redirect = this.getAttribute('redirect');

        this.onSuccess = this.getAttribute('onsuccess');
        this.onError = this.getAttribute('onerror');
    }
}

customElements.define('app-form', Form, { extends: 'form' });