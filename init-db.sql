CREATE TABLE users
(
    id            SERIAL PRIMARY KEY,
    username      VARCHAR(50) UNIQUE         NOT NULL,
    email         VARCHAR(320) UNIQUE        NOT NULL,
    password_hash VARCHAR(255)               NOT NULL,
    role          VARCHAR(50) DEFAULT 'user' NOT NULL,
    is_active     BOOLEAN     DEFAULT false,
    created_at    TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP,
    deleted_at    TIMESTAMPTZ
);

INSERT INTO users (username, email, password_hash, role, is_active)
VALUES ('Ilies', 'iliesrimani.work@gmail.com', '$2y$10$3LsI.FX5.9JwAdEmzb1rqetPlPwM0W.rVgKC.YmhsQ9Ni6KjuNJle', 'admin', true);

CREATE TABLE user_tokens
(
    id         SERIAL PRIMARY KEY,
    user_id    INT REFERENCES users (id) ON DELETE CASCADE,
    token      VARCHAR(255) NOT NULL,
    token_type VARCHAR(50)  NOT NULL,
    expires_at TIMESTAMPTZ  NOT NULL,
    created_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE pages
(
    id           SERIAL PRIMARY KEY,
    title        VARCHAR(255)              NOT NULL,
    slug         VARCHAR(255) UNIQUE       NOT NULL,
    content      TEXT                      NOT NULL,
    is_published BOOLEAN     DEFAULT false NOT NULL,
    created_at   TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP,
    deleted_at   TIMESTAMPTZ
);

CREATE TABLE posts
(
    id           SERIAL PRIMARY KEY,
    page_id      INT REFERENCES pages (id) ON DELETE CASCADE,
    title        VARCHAR(255)              NOT NULL,
    slug         VARCHAR(255) UNIQUE       NOT NULL,
    content      TEXT                      NOT NULL,
    is_published BOOLEAN     DEFAULT false NOT NULL,
    created_at   TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP,
    deleted_at   TIMESTAMPTZ
);

CREATE TABLE comments
(
    id         SERIAL PRIMARY KEY,
    post_id    INT REFERENCES posts (id) ON DELETE CASCADE,
    user_id    INT                           REFERENCES users (id) ON DELETE SET NULL,
    content    TEXT                          NOT NULL,
    status     VARCHAR(50) DEFAULT 'pending' NOT NULL,
    created_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP
);