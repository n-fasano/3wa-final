class User
{
    static logged = false;

    static async isLogged() {
        return await fetch('/api/logged')
            .then(response => response.json())
            .then(json => json.logged);
    }

    static async logout() {
        return await fetch('/api/logout')
            .then(response => true)
            .catch(error => false);
    }
}