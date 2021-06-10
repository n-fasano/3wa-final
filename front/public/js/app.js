$_ = document.querySelectorAll;

var header = null;

async function app() {
    User.logged = await User.isLogged();
    
    header = await fetch('/tpl/header.html')
        .then(response => response.text())
        .then(template => {
            const root = document.querySelector('header');
            const state = {
                logged: User.logged,
                notLogged: !User.logged
            };

            return new Component({
                template,
                root,
                state
            });
        });

    Router.initialize();
}

app();