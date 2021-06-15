(async function() {
    Router.body.setState({
        threads: await fetch('/api/threads')
            .then(response => response.json())
            .then(json => json.map(item => {
                item.title = item.users.map(user => user.username).join(', ');
                return item;
            }))
    });
})();