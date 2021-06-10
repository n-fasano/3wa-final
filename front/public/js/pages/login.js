function onLoginSuccess(json) {
    User.logged = true;
    header.setState({
        logged: User.logged,
        notLogged: !User.logged
    });
    console.log('login');
}