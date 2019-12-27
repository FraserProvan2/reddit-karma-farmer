# Reddit karma farmer

Simple algorithm that searches for old posts, modifies to avoid duplication detection, and reposts for that sweet karma. Starting/Stopping can be operated by a simple UI. The algorithm will run once every 20-35 minutes. Built with Laravel 6/Vue.js.

### Installation
This repo uses [Laradock](https://laradock.io/) for local development.

1. Run `make docker_create` to create the docker instance.
2. Run `make docker_up` to start the containers.
<br />NOTE: Run `make docker_down` to stop the containers. 

##### Creating MySQL database and granting access
1. Attach shell to `laradock_myql` container. (Using `docker-compose exec` or Docker Extension).
2. Run the following 2 commands inside the `laradock_myql` container:

```sql
CREATE DATABASE IF NOT EXISTS `reddit_bot` COLLATE ‘utf8_general_ci’;
GRANT ALL ON `reddit_bot`.* TO 'default'@'%';
```

To test run `php artisan migrate:fresh --seed` inside the `laradock_workspace` container, if migration success the DB connection has been established.

See [Laradock Documentation](https://laradock.io/documentation/#create-multiple-databases-mysql) for furthur help. 
