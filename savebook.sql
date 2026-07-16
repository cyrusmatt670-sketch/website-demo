CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- This line must be OUTSIDE and AFTER the closing bracket
ALTER TABLE books ADD category VARCHAR(100) NOT NULL DEFAULT 'Science';