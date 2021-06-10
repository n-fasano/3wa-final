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
                    if (json.error) {
                        (self.onError)(json.error);
                    } else {
                        (self.onSuccess)(json);

                        history.pushState({
                            path: window.location.pathname
                        }, self.id, self.redirect);
                    }
                })
                .catch(function (error) {
                    (self.onError)(error);
                });
        });
    }

    connectedCallback() {
        this.actionPath = this.action.replace(location.origin, '');
        this.redirect = this.getAttribute('redirect');

        const defaultCallback = () => null;
        this.onSuccess = this.getAttribute('onSuccess') ?? defaultCallback;
        this.onError = this.getAttribute('onError') ?? defaultCallback;
    }
}

customElements.define('app-form', Form, { extends: 'form' });