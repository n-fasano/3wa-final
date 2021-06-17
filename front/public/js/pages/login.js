function onLoginSuccess(credentials) {
    User.id = credentials.id;
    User.username = credentials.username;
    User.logged = credentials.logged;

    header.setState({
        logged: User.logged,
        notLogged: !User.logged,
        username: User.username
    });
}