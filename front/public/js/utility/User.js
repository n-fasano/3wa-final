class User
{
    static id = 0;
    static username = 'Anon';
    static logged = false;

    static async load() {
        const credentials = await fetch('/api/credentials')
            .then(response => response.json());
        
        User.id = credentials.id;
        User.username = credentials.username;
        User.logged = credentials.logged;
    }

    static async logout() {
        const success = await fetch('/api/logout')
            .then(() => true)
            .catch(() => false);

        if (success) {
            User.id = 0;
            User.username = 'Anon';
            User.logged = false;
        }

        return success;
    }
}