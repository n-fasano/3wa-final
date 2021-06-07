$_ = document.querySelectorAll;

async function app() {
    if (false === User.logged) {
        User.logged = await User.isLogged();

        if (true === User.logged) {
            history.replaceState({
                path: window.location.pathname
            }, document.title, '/');
        }
    } else {
        Router.initialize();
    }
    
}

app();