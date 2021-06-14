const debounce = (func, wait) => {
    let timeout;

    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };

        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
};

class Autocomplete extends HTMLInputElement {
    constructor() {
        super();
    }

    connectedCallback() {
        this.dataSource = '/api' + this.getAttribute('data-source');
        this.itemsPropName = this.getAttribute('data-items');
        this.selectedItemsPropName = 'selected' + this.itemsPropName[0].toUpperCase() + this.itemsPropName.substr(1);
        this.cache = {};
        this.currentResults = {};
        this.selectedItems = {};
        this.currentSearch = '';

        const self = this;
        this.addEventListener('keyup', debounce(async function (e) {
            const search = self.value;
            if (search.length < 4 || self.currentSearch === search) {
                return;
            }

            if (!self.cache[search]) {
                self.cache[search] = await fetch(self.dataSource + `?${self.dataset.field}=${search}`)
                    .then(response => response.json());
            }

            self.currentSearch = search;
            self.currentResults = {};
            for (const item of self.cache[search]) {
                self.currentResults[item.id] = item;
            }

            self.setState();
            self.results.show();
            self.selected.show();
        }, 300));
    }

    setState() {
        const state = {};
        
        state[this.itemsPropName] = Object.values(this.currentResults);
        state[this.selectedItemsPropName] = Object.values(this.selectedItems);

        Router.body.setState(state);
    }

    setResults(results) {
        this.results = results;
    }

    setSelected(selected) {
        this.selected = selected;
    }

    addSelected(id) {
        const item = this.currentResults[id];
        this.selectedItems[id] = item;
        delete this.currentResults[id];
        this.setState();
    }

    removeSelected(id) {
        const item = this.selectedItems[id];
        this.currentResults[id] = item;
        delete this.selectedItems[id];
        this.setState();
    }
}

customElements.define('app-autocomplete', Autocomplete, { extends: 'input' });