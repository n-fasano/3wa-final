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
        this.template = template;
        this.regex = new RegExp(`{{ *?([^{} ]*) *?}}`, "g");
        this.state = {};
        this.root = {
            element: root,
            children: this.buildTree(Component.strToHTML(this.template))
        };

        for (const child of this.root.children) {
            this.root.element.append(child.element);
        }

        this.initState(state);
    }

    initState(state) {
        for (const key in this.state) {
            const value = state[key];
            if (value) {
                this.state[key].value = value;
            }
            
            this.state[key].listeners.forEach(l => {
                l.callback(key, value);
            });
        }
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
        this.root.element.show();
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
            const parent = element.parentElement;
            const loopId = element.dataset.loopId;

            const anchor = document.createElement('div');
            anchor.style.display = "none";
            anchor.setAttribute('data-loop-anchor', loopId);
            parent.insertBefore(anchor, element);

            element.remove();

            variables[name] ??= [];
            variables[name].push({
                element: element,
                callback: (variable, array) => {
                    Array.from(
                        parent.querySelectorAll(`[data-loop-id=${loopId}]`)
                    ).forEach(e => e.remove());

                    if (!array) {
                        return;
                    }

                    for (let i = 0; i < array.length; i++) {
                        const item = array[i];
                        let newElement = element.cloneNode(true);
                        newElement.setAttribute('data-loop-id', loopId);
                        let templateString = element.innerHTMLTemplate;
                        for (const key in item) {
                            templateString = templateString.replace(
                                new RegExp(`{{ *?${key} *?}}`, "g"),
                                item[key]
                            );
                        }
                        templateString = templateString.replace(
                            new RegExp(`{{ *?index *?}}`, "g"),
                            i
                        );
                        newElement.innerHTML = templateString;
                        parent.insertBefore(newElement, anchor);
                    }
                }
            });
        }

        return variables;
    }

    findVariables(expression) {
        const variables = [];

        const forbiddenMatches = [...expression.matchAll(new RegExp('(function)|(=>)|([^= ]+? *(=) *[^= ]+?)', 'gm'))];
        if (forbiddenMatches.length > 0) {
            throw new Error('Forbidden operations found in expression: '+forbiddenMatches.join(', '));
        }

        const operands = expression.matchAll(new RegExp('([^ +\-*\/]+)', 'g'));
        for (const operand of operands) {
            const isNumber = !isNaN(operand);
            const isString = '\'"`'.includes(operand[0]);

            if (isNumber || isString) {
                continue;
            }

            const variable = operand.split(new RegExp('[\.\[]', 'g')).shift();
            variables.push(variable);
        }

        return variables;
    }

    findListeners(element) {
        const listeners = {};

        const textNode = element.childNodes[0];
        if (undefined !== textNode &&
            null !== textNode.nodeValue) 
        {
            const expressions = textNode.nodeValue.matchAll(new RegExp(`{{(.*)}}`, "gm"));
            const variables = expressions.map(expression => this.findVariables(expression)).flat();

            for (const varName of variables) {
                listeners[varName] ??= [];
                listeners[varName].push({
                    element: element,
                    callback: (state) => {
                        const expressions = element.textContentTemplate.matchAll(new RegExp(`{{(.*)}}`, "gm"));

                        let content = element.textContentTemplate;
                        for (const expression of expressions) {
                            content = content.replace(
                                new RegExp(`{{${expression}}}`, "gm"),
                                eval.bind(state)(expression)
                            );
                        }

                        element.innerText = content;
                    }
                });
            }

            element.setTextContentTemplate(element.innerText);
        }

        if (element.hasAttribute("if")) {
            const name = element.getAttribute("if");
            listeners[name] ??= [];
            listeners[name].push({
                element: element,
                callback: (state) => element.toggle(state[name])
            });
        }
        
        if (element.hasAttribute("for")) {
            const name = element.getAttribute("for");
            listeners[name] ??= [];
            listeners[name].push({
                element: element,
                callback: (state) => {
                    const array = state[name];
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

        return listeners;
    }
}