class Form extends HTMLFormElement {
    constructor() {
        super();

        this.addEventListener('submit', function (e) {
            e.preventDefault();
            
            this.checkValidity();

            console.log(`/api${this.actionPath}`);

            fetch(`/api${this.actionPath}`, {
                method: 'POST',
                body: new FormData(this)
            })
                .then(function(response) {
                    console.log(response);
                    return response.json();
                })
                .then(function (json) {
                    console.log('ici: ' + json);
                })
                .catch(function (error) {
                    console.log('là: ' + error);
                });
        });
    }

    connectedCallback() {
        this.actionPath = this.action.replace(location.origin, '');
        this.redirect = this.getAttribute('redirect');
    }
}

customElements.define('app-form', Form, { extends: 'form' });