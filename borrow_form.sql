DROP TABLE IF EXISTS borrow_form;
CREATE TABLE borrow_form (
    id INT AUTO_INCREMENT PRIMARY KEY,
    studentName VARCHAR(100),
    bookTitle VARCHAR(255),
    email VARCHAR(100),
    gradeSection VARCHAR(50),
    dateBorrowed DATE,
    returnDate DATE,
    phonenumber VARCHAR(20),
    id_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);