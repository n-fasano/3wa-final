async function logout() {
    const success = await User.logout();

    if (success) {
        User.isLogged = false;
        
        history.replaceState({
            path: location.pathname
        }, document.title, '/login');
    } else {
        location.reload();
    }

}

logout();