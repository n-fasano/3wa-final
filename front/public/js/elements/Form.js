class Form extends HTMLFormElement {
    constructor() {
        super();

        this.addEventListener('submit', function (e) {
            e.preventDefault();
            if (!this.checkValidity()) {
                return;
            }

            const self = this;
            const body = new FormData(self);
            if (body.entries().next().done) {
                return;
            }

            window[self['onBeforeSubmit']](body);

            fetch(`/api${self.actionPath()}`, {
                method: 'POST',
                body: body
            })
                .then(function(response) {
                    return response.json();
                })
                .then(function (json) {
                    if (undefined !== json.error) {
                        window[self['onError']](json.error);
                        window.onError(json.error);
                    } else {
                        window[self['onSuccess']](json, body);
                        window.onSuccess(json);

                        if (null !== self.redirect) {
                            history.pushState({
                                path: window.location.pathname
                            }, self.id, self.redirect);
                        }
                    }
                })
                .catch(function (error) {
                    window.onError('Something went wrong.');
                });
        });
    }

    connectedCallback() {
        this.redirect = this.getAttribute('redirect');

        this.onBeforeSubmit = this.getAttribute('onbeforesubmit') ?? 'nullFunc';
        this.onSuccess = this.getAttribute('onsuccess') ?? 'nullFunc';
        this.onError = this.getAttribute('onerror') ?? 'nullFunc';
    }

    actionPath() {
        return this.action.replace(location.origin, '');
    }
}

customElements.define('app-form', Form, { extends: 'form' });