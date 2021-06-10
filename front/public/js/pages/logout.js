async function logout() {
    const success = await User.logout();

    if (success) {
        User.isLogged = false;
        header.setState({
            logged: User.logged,
            notLogged: !User.logged
        });
        
        history.replaceState({
            path: location.pathname
        }, document.title, '/login');
    } else {
        location.reload();
    }
}

logout();