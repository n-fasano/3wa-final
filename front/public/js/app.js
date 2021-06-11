$_ = document.querySelectorAll;

var header = null;
var errorPane = null;

function onSuccess(json) {
    errorPane.setState({
        error: ''
    });
}

function onError(error) {
    errorPane.setState({
        error: error
    });
}

async function app() {
    User.logged = await User.isLogged();
    
    errorPane = await fetch('/tpl/error.html')
        .then(response => response.text())
        .then(template => {
            const root = document.querySelector('app-error');
            const state = {
                error: ''
            };

            return new Component({
                template,
                root,
                state
            });
        });
    
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