-- This migration is intentionally idempotent: db-init runs it on every
-- `docker compose up`, while existing tables and data are preserved.

CREATE TABLE IF NOT EXISTS categories (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(120) NOT NULL,
    slug VARCHAR(190) NOT NULL,
    description TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_categories_slug (slug),
    CONSTRAINT chk_categories_name_not_empty CHECK (CHAR_LENGTH(TRIM(name)) > 0),
    CONSTRAINT chk_categories_slug_not_empty CHECK (CHAR_LENGTH(TRIM(slug)) > 0)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS articles (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(190) NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    body MEDIUMTEXT NOT NULL,
    views BIGINT UNSIGNED NOT NULL DEFAULT 0,
    published_at DATETIME NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_articles_slug (slug),
    KEY idx_articles_published_at_id (published_at DESC, id DESC),
    KEY idx_articles_views_id (views DESC, id DESC),
    CONSTRAINT chk_articles_title_not_empty CHECK (CHAR_LENGTH(TRIM(title)) > 0),
    CONSTRAINT chk_articles_slug_not_empty CHECK (CHAR_LENGTH(TRIM(slug)) > 0),
    CONSTRAINT chk_articles_image_path_not_empty CHECK (CHAR_LENGTH(TRIM(image_path)) > 0)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS article_category (
    article_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (article_id, category_id),
    KEY idx_article_category_category_article (category_id, article_id),
    CONSTRAINT fk_article_category_article
        FOREIGN KEY (article_id) REFERENCES articles (id)
        ON DELETE CASCADE
        ON UPDATE RESTRICT,
    CONSTRAINT fk_article_category_category
        FOREIGN KEY (category_id) REFERENCES categories (id)
        ON DELETE CASCADE
        ON UPDATE RESTRICT
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_0900_ai_ci;
