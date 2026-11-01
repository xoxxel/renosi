# renosi

WordPress project running via Docker (WordPress + MariaDB).

## Requirements

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (with Docker Compose v2)

## Start

```bash
docker compose up -d
```

Then open:

```
http://localhost:8080
```

## Default configuration

| Service     | Image         | Host             | Container    | Credentials (default)                |
| ----------- | ------------- | ---------------- | ------------ | ----------------------------------- |
| MariaDB     | `mariadb:11`  | internal only   | `db:3306`    | user: `wordpress`, pass: `wordpress` |
| WordPress   | `wordpress`   | `localhost:8080` -> `80` | Apache + PHP | — (set during install)      |
| WP-CLI      | `wordpress:cli` | internal only | —            | —                                   |

## Useful commands

```bash
# View logs
docker compose logs -f

# Stop containers (data is kept)
docker compose down

# Stop and delete all data (fresh start)
docker compose down -v

# Run WP-CLI (e.g. core, plugin, theme, db commands)
docker compose run --rm wpcli wp core update
docker compose run --rm wpcli wp plugin list
docker compose run --rm wpcli wp db export
```

## WordPress installation

After the containers are up, open `http://localhost:8080` and run the famous 5-minute
install (or use WP-CLI):

```bash
docker compose run --rm wpcli wp core install \
  --url=http://localhost:8080 \
  --title="renosi" \
  --admin_user=admin \
  --admin_password=admin \
  --admin_email=admin@example.com \
  --skip-email
```

> The site is already installed on this setup — the database and files were created
> during the first `docker compose up -d` + `wp core install`.

## Site URL

The site is served on port `8080` (mapped to container's Apache port `80`).

To change it, edit `WORDPRESS_PORT` in `.env` and run `docker compose up -d` again.


_________________________

# Get Thems

mkdir themes
docker cp renosi_wordpress:/var/www/html/wp-content/themes/renosi ./themes/renosi

_________________________
