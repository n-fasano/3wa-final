class Selected extends HTMLUListElement {
    constructor() {
        super();
    }

    connectedCallback() {
        const autocompleteId = this.dataset.autocompleteSelected;
        this.autocomplete = document.getElementById(autocompleteId);
        this.autocomplete.setSelected(this);

        const self = this;
        this.addEventListener('click', function(e) {
            if (e.target.dataset.id) {
                self.autocomplete.removeSelected(e.target.dataset.id);
            }
        })
    }
}

customElements.define('app-selected', Selected, { extends: 'ul' });