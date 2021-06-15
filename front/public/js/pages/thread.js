(async function() {
    const thread = await fetch('/api/threads/'+Router.body.getState('id'))
        .then(response => response.json());

    thread.title = thread.users.map(user => user.username).join(', ');
    
    Router.body.setState({ ...thread });
})();

function onMessageCreated(json, body) {
    const messages = Router.body.getState('messages');
    const message = {
        content: body.get('content'),
        id: json.id,
        userId: User.id,
        userName: User.username
    }
    messages.push(message);

    Router.body.setState({ messages });
}