CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(100) NOT NULL,
    role VARCHAR(20) DEFAULT 'user'
);

INSERT INTO users (username, password, role) VALUES
('admin', 'formula123', 'admin');

INSERT INTO users (username, password) VALUES
('hawaii', 'aloha'),
('maxie', 'rbr33'),
('joana', '23later'),
('martin', 'jorge88');

CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL
);

INSERT INTO articles (title, content) VALUES 
('Introduction to Vulnerabilities', 'This article explores common web application vulnerabilities such as SQL injection and cross-site scripting (XSS).'),
('History of Wikipedia', 'Wikipedia was launched on January 15, 2001, and has since become the world’s largest online encyclopedia.'),
('Basics of PHP', 'PHP is a popular server-side scripting language designed for web development, but it is also used as a general-purpose programming language.'),
('What is SQL Injection?', 'SQL injection is a code injection technique used to attack data-driven applications by inserting malicious SQL statements.'),
('Understanding JWTs', 'JSON Web Tokens (JWTs) are an open standard used to securely transmit information between parties as a JSON object.'),
('Top 5 Web Security Practices', 'Learn about essential security practices, including input validation, encryption, and regular security audits.'),
('Common Security Misconfigurations', 'Misconfigurations in web servers and applications can lead to security vulnerabilities, allowing attackers to gain unauthorized access.'),
('Introduction to Cybersecurity', 'Cybersecurity involves protecting computer systems and networks from information disclosure, theft, or damage.'),
('The Rise of Open Source', 'Open source software has revolutionized the software industry, fostering collaboration and innovation globally.'),
('Future of Web Development', 'Web development is constantly evolving with new technologies such as Progressive Web Apps (PWAs) and WebAssembly.');

