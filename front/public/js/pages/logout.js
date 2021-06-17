(async function() {
    const success = await User.logout();

    if (success) {
        header.setState({
            logged: User.logged,
            notLogged: !User.logged,
            username: User.username
        });
    }

    history.replaceState({
        path: location.pathname
    }, document.title, '/');
})();