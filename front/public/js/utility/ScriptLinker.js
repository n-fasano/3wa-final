class ScriptLinker
{
    static replaceScriptNode(node) {
        node.parentNode.replaceChild(
            ScriptLinker.cloneScriptNode(node),
            node
        );

        return node;
    }

    static cloneScriptNode(node, safe = true) {
        const script  = document.createElement("script");
        script.text = node.innerHTML;

        let i = -1, attrs = node.attributes, attr;
        while (++i < attrs.length) {     
            attr = attrs[i];

            // Added some checking to be safe
            let value = attr.value;
            if (safe && 'src' === attr.name && !value.includes(location.hostname)) {
                value = '';
            }

            script.setAttribute(attr.name, attr.value);
        }

        return script;
    }
}

// https://stackoverflow.com/questions/1197575/can-scripts-be-inserted-with-innerhtml