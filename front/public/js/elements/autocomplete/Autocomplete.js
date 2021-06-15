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
        this.selectedItemsPropName = 'selected' + this.itemsPropName.ucFirst();
        this.cache = {};
        this.currentResults = {};
        this.selectedItems = {};
        this.transform = this.getAttribute('transform');

        const self = this;
        let lastSearch = self.value;
        self.addEventListener('keyup', debounce(async function (e) {
            const search = self.value;
            if (search.length < 4 || lastSearch === search) {
                return;
            }
            lastSearch = search;

            await self.search(search);
            self.setState();
        }, 300));
    }

    async search(search) {
        if (!this.cache[search]) {
            let results = await fetch(this.dataSource + `?${this.dataset.field}=${search}`)
                .then(response => response.json());

            if (this.transform && window[this.transform]) {
                results = window[this.transform](results);
            }
            
            this.cache[search] = results;
        }

        this.currentResults = {};
        for (const item of this.cache[search]) {
            this.currentResults[item.id] = item;
        }
    }

    setState() {
        const state = {};
        
        state[this.itemsPropName] = Object.values(this.currentResults);
        state[this.selectedItemsPropName] = Object.values(this.selectedItems);

        Router.body.setState(state);
    }

    setResults(resultsElement) {
        this.results = resultsElement;
    }

    setSelected(selectedElement) {
        this.selected = selectedElement;
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