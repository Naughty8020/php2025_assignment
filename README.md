# php2025_assignment

C:\xampp\htdocs\php2025_assignment\
http://localhost/php2025_assignment/




CREATE DATABASE libraty;
USE libraty;

-- booksテーブルの作成
CREATE TABLE books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    status VARCHAR(20) NOT NULL
);

-- borrow_recordsテーブルの作成
CREATE TABLE borrow_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT,
    borrower_name VARCHAR(100) NOT NULL,
    borrow_date DATE NOT NULL,
    return_date DATE,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
);

