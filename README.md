# Blogy — a framework-free PHP blog

A test-assignment blog built without a PHP framework, on top of plain PDO, Smarty and a small hand-rolled router. Backed by MySQL, containerized with Docker.

## Requirements

- Docker and Docker Compose
- (Optional, for local runs without Docker) PHP 8.1+ with `pdo_mysql`, Composer, and a MySQL 8 server

## Running with Docker

```bash
cp .env.example .env
docker compose up --build
```

This starts three services:

- `db` — MySQL 8.4
- `db-init` — applies `database/migrations/001_initial_schema.sql` on first boot
- `app` — PHP 8.3 + Apache, serving `public/` as the document root

Once it's up, seed the database with demo content:

```bash
docker compose exec app composer seed
```

The site is available at **http://localhost:8080**.

## Running locally without Docker

1. Install dependencies: `composer install`
2. Create a MySQL database and apply `database/migrations/001_initial_schema.sql`
3. Export `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD` (see `.env.example`)
4. Seed it: `php database/seeders/seed.php`
5. Point your web server's document root at `public/`, or run `php -S localhost:8080 -t public`

## Routes

| Route | Description |
|---|---|
| `GET /` | Categories with their 3 latest articles |
| `GET /blogs` | All articles, sortable (`?sort=date\|views`) and paginated (`?page=N`) |
| `GET /blogs/{slug}` | Article detail, increments the view counter, shows related articles |
| `GET /categories` | List of categories |
| `GET /categories/{slug}` | Articles within one category, same sorting/pagination as `/blogs` |

Unknown slugs and unknown routes return a real HTTP 404.

## Architecture

Layered, dependency flowing inward, no framework and no DI container — everything is wired by hand in `public/index.php`:

```text
Router → Controller (Presentation) → Application Service → Repository Interface (Domain)
                                                                    ↓
                                                     PDO Repository (Infrastructure) → MySQL
```

- **Domain** (`app/Domain`) — `Article`/`Category` entities and repository interfaces. No PDO, no Smarty, no HTTP.
- **Application** (`app/Application`) — one service per page (`HomePageService`, `BlogPageService`, `CategoryPageService`, `ArticlePageService`), plus presenters that map entities into view-ready arrays.
- **Infrastructure** (`app/Infrastructure`) — `PdoArticleRepository`, `PdoCategoryRepository`, `ConnectionFactory`, `SmartyViewRenderer`.
- **Presentation** (`app/Presentation`) — the router and thin controllers that call one service and hand the result to the view.

### Related articles

An article is "related" if it shares at least one category with the current one, excluding the article itself, ordered by publish date (newest first), capped at 3.

### Security

- All queries go through PDO prepared statements (`PDO::ATTR_EMULATE_PREPARES` disabled).
- Sorting (`?sort=`) is resolved through a whitelist enum (`ArticleSort`), never interpolated from raw input.
- Smarty's global HTML escaping is on (`setEscapeHtml(true)`).
- HTTP security headers are set on every response: `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Content-Security-Policy`.
- `php.ini` is hardened for production-like behavior: `display_errors=Off`, `expose_php=Off`, strict session cookies.

### Known limitations

Scoped as a test assignment — no admin panel, no auth, no comments, no search, no REST API, and view counts aren't deduplicated per visitor/IP.

## My work & how AI (Claude Code) helped

- I designed the base architecture and layering (`PLAN.md`) up front.
- I worked out the implementation plan together with the AI and edited it afterward.
- The AI wrote the code layer by layer, following that plan; I reviewed everything it wrote before it was committed.
