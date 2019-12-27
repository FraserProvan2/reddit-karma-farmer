# Reddit karma farmer

Simple algorithm that searches for old posts, modifies to avoid duplication detection, and reposts for that sweet karma. Starting/Stopping can be operated by a simple UI. The algorithm will run once every 20-35 minutes. Built with Laravel 6/Vue.js.

### Installation
1. Clone repository.
2. run `make build`.
3. Add missing ENV values in `.env`:
    * REDDIT_CLIENT_ID
    * REDDIT_SECRET
    * REDDIT_USER_AGENT
    * REDDIT_USERNAME
    * REDDIT_PASSWORD
    
NOTE: [Reddit API Docs](https://github.com/reddit-archive/reddit/wiki/oauth2) for help setting up an App, Client ID and Secret.

##### Docker
This project uses [Laradock](https://laradock.io/) for local development/running locally.

1. Run `make docker_create` to create the docker instance. This may take a while...
2. Run `make docker_up` to start the containers.
<br />NOTE: Run `make docker_down` to stop the containers. 

##### Creating MySQL database and granting access
1. Attach shell to `laradock_myql` container. (Using `docker-compose exec` or Docker Extension).
2. Run `mysql -uroot -proot` inside the `laradock_myql` container for root access.
3. Finally run the following commands:

```sql
CREATE DATABASE IF NOT EXISTS `reddit_bot` COLLATE ‘utf8_general_ci’;
GRANT ALL ON `reddit_bot`.* TO 'default'@'%';
```

To test run `php artisan migrate:fresh --seed` inside the `laradock_workspace` container, if migration success the DB connection has been established.

See [Laradock Documentation](https://laradock.io/documentation/#create-multiple-databases-mysql) for furthur help. 

##### Finally
In your browser of choice go to `127.0.0.1` and login.

Deafult login details are (Can be edited in `database/seeds/UsersTableSeeder.php`):
    * Email `user@email.com`
    * Password `getmesomekarma`
   
### Testing
Run `make test` to run PHPUnit tests.
