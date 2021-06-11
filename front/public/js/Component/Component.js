class Component {
    static createElement(tag, attributes) {
        let element = document.createElement(tag);
        for (const attribute in attributes) {
            element.setAttribute(attribute, attributes[attribute]);
        }
        let $e = new $element(element);
        return Object.assign(element, $e);
    }

    static createElementFromHTML(htmlString) {
        var div = document.createElement("div");
        div.innerHTML = htmlString.trim();

        // Change this to div.childNodes to support multiple top-level nodes
        let element = div.firstChild;

        let $e = new $element(element);
        return Object.assign(element, $e);
    }

    static strToHTML(templateString) {
        var div = document.createElement("div");
        div.innerHTML = templateString.trim();
        return div.children;
    }

    constructor({ template, root, state }) {
        this.regex = new RegExp(`{{ *?([^{} ]*) *?}}`, "g");
        this.state = {};
        this.root = {
            element: root,
            children: this.buildTree(Component.strToHTML(template))
        };

        this.setState(state);
        this.template = template;
        this.show();
    }

    setState(state) {
        for (const key in state) {
            this.state[key] ??= {
                value: null,
                listeners: []
            };
            
            const value = state[key];
            this.state[key].value = value;
            
            this.state[key].listeners.forEach(l => {
                l.callback(key, value);
            });
        }
    }

    show() {
        for (const child of this.root.children) {
            this.root.element.append(child.element);
        }
    }

    buildTree(childElements) {
        const childNodes = new Array();

        for (const element of childElements) {
            const node = {
                element: $element.create(element),
                textContent: element.textContent,
                children: this.buildTree(element.children)
            };

            const variables = this.findVariableListeners(node.element);
            for (const varName in variables) {
                if (undefined === this.state[varName]) {
                    this.state[varName] = {
                        value: null,
                        listeners: []
                    };
                }

                const listeners = variables[varName];
                this.state[varName].listeners.push(...listeners);
            }

            childNodes.push(node);
        }
        
        return childNodes;
    }

    parse() {
        let html = this.template;
        let vars = html.matchAll(this.regex);
        for (const arr of vars) {
            let fullMatch = arr[0];
            let match = arr[1];
            if (!this.state[match]) {
                throw new Error("Variable '" + match + "' does not exist");
            }
            html = html.replace(fullMatch, this.state[match].value);
        }
        return this.createElement("<div>" + html + "</div>");
    }

    createElement(html) {
        var div = document.createElement("div");
        div.innerHTML = html.trim();

        // Change this to div.childNodes to support multiple top-level nodes
        let element = div.firstChild;
        return element;
    }

    hook(root) {
        this.root.element.innerHTML = "";
        this.root.element = root;
        this.display();
    }

    display() {
        this.root.element.innerHTML = "";
        this.root.element.append(this.parse());
    }

    findVariableListeners(element) {
        const variables = {};

        if (undefined !== element.childNodes[0] &&
            null !== element.childNodes[0].nodeValue) 
        {
            const matches = element.childNodes[0].nodeValue.matchAll(
                new RegExp(`{{ *?([a-zA-Z0-9_]+) *?}}`, "g")
            );

            element.setTextContentTemplate(element.innerText);

            for (const match of matches) {
                const name = match[1];
                variables[name] ??= [];
                variables[name].push({
                    element: element,
                    callback: (variable, value) => {
                        element.innerText = element.textContentTemplate.replace(
                            new RegExp(`{{ *?${variable} *?}}`, "g"),
                            value
                        );
                    }
                });
            }
        }

        if (element.hasAttribute("if")) {
            const name = element.getAttribute("if");
            variables[name] ??= [];
            variables[name].push({
                element: element,
                callback: (variable, bool) => element.toggle(bool)
            });
        }

        if (element.hasAttribute("for")) {
            const name = element.getAttribute("for");
            variables[name] ??= [];
            variables[name].push({
                element: element,
                callback: (variable, array) => {
                    let parent = element.parentElement;
                    parent.innerHTML = "";
                    for (let i = 0; i < array.length; i++) {
                        const object = array[i];
                        let newElement = element.cloneNode(true);
                        let templateString = element.innerHTMLTemplate;
                        for (const key in object) {
                            templateString = templateString.replace(
                                new RegExp(`{{ *?${key} *?}}`, "g"),
                                object[key]
                            );
                        }
                        newElement.innerHTML = templateString;
                        parent.append(newElement);
                    }
                }
            });
        }

        return variables;
    }
}