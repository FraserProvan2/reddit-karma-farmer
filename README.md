## Installation
This repo uses [Laradock](https://laradock.io/) for local development

1. Run `docker_create` to create the docker instance 
2. Run `docker_up` to start the containers

### Creating mysql DB and granting access
1. Attach shell to `laradock-myql` container. (Using `docker-compose exec` or Docker Extension).
2. Run the following 2 commands:
```sql
CREATE DATABASE IF NOT EXISTS `repost_bot` COLLATE ‘utf8_general_ci’ ;
GRANT ALL ON `repost_bot`.* TO ‘default’@‘%’ ;
```

See [Laradock Documentation](https://laradock.io/documentation/#create-multiple-databases-mysql) for further 