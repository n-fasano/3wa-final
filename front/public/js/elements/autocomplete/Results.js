class Results extends HTMLUListElement {
    constructor() {
        super();
    }

    connectedCallback() {
        this.style.display = 'none';

        const autocompleteId = this.dataset.autocompleteResults;
        this.autocomplete = document.getElementById(autocompleteId);
        this.autocomplete.setResults(this);

        const self = this;
        this.addEventListener('click', function(e) {
            if (e.target.dataset.id) {
                self.autocomplete.addSelected(e.target.dataset.id);
            }
        })
    }
}

customElements.define('app-results', Results, { extends: 'ul' });