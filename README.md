# 3wa-final

This ties together [Jay](https://github.com/n-fasano/Jay-Framework) and [Stork](https://github.com/n-fasano/Stork-Framework) into a simple chat app, heavily modifying them and adding some Symfony components as dependencies.

It adds:
- User registration, authentication, sessions;
- DTOs, automatic deserialization, constraint validation;
- ORM+DBAL with association mapping using attributes à la [Doctrine](https://www.doctrine-project.org/);
- Front-end routing, templating, and more.


## Running the project

```
git clone https://github.com/NFLorD/3wa-final
cd 3wa-final
docker-compose up -d
```
