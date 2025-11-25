# Database Setup

```sql
CREATE DATABASE chatbot_tutor;
USE chatbot_tutor;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    activated BOOLEAN NOT NULL DEFAULT TRUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    nick VARCHAR(255) NOT NULL,
    pass VARCHAR(255) NOT NULL
);

CREATE TABLE verification_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    code INT NOT NULL,
    expires TIMESTAMP NOT NULL
);

CREATE TABLE topics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    desc TEXT NOT NULL,
    clicks INT NOT NULL DEFAULT 0
);

CREATE TABLE feedbacks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    desc TEXT NOT NULL,
    created DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE tutoring_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    topic_id INT NOT NULL,
    pre_score INT NOT NULL,
    post_score INT NOT NULL,
    summary TEXT NOT NULL,
    messages JSON NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (topic_id) REFERENCES topics(id) ON DELETE CASCADE
);
```