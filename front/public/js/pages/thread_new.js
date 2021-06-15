function usersTransform(users) {
    return users.filter(user => user.id !== User.id);
}