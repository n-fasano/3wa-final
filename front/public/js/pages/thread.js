(async function() {
    const thread = await fetch('/api/threads/'+Router.body.getState('id'))
        .then(response => response.json());

    thread.title = thread.users.map(user => user.username).join(', ');
    
    Router.body.setState({ ...thread });

    var socket = null;
    try {
        // Connexion vers un serveur HTTP
        // prennant en charge le protocole WebSocket ("ws://").
        socket = new WebSocket("ws://localhost");

        // ----- OU -----

        // Connexion vers un serveur HTTPS
        // prennant en charge le protocole WebSocket over SSL ("wss://").
        socket = new WebSocket("wss://localhost");
    } catch (exception) {
        console.error(exception);
    }

    // Récupération des erreurs.
    // Si la connexion ne s'établie pas,
    // l'erreur sera émise ici.
    socket.onerror = function(error) {
        console.error(error);
    };

    // Lorsque la connexion est établie.
    socket.onopen = function(event) {
        console.log("Connexion établie.");

        // Lorsque la connexion se termine.
        this.onclose = function(event) {
            console.log("Connexion terminé.");
        };

        // Lorsque le serveur envoi un message.
        this.onmessage = function(event) {
            console.log("Message:", event.data);
        };

        // Envoi d'un message vers le serveur.
        this.send("Hello world!");
    };
})();

function onBeforeNewMessage(body) {
    const date = new Date;
    body.set('sentAt', date.getFullYear() +
        '-' + date.getMonth().toString().padStart(2, 0) +
        '-' + date.getDate().toString().padStart(2, 0) +
        ' ' + date.getHours().toString().padStart(2, 0) +
        ':' + date.getMinutes().toString().padStart(2, 0) +
        ':' + date.getSeconds().toString().padStart(2, 0));
}

function onMessageCreated(json, body) {
    const messages = Router.body.getState('messages');
    const message = {
        content: body.get('content'),
        id: json.id,
        userId: User.id,
        userName: User.username,
        sentAt: body.get('sentAt')
    }
    messages.push(message);

    Router.body.setState({ messages });

    document.querySelector('[name=content]').value = '';
}