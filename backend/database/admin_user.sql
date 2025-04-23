INSERT INTO users (username, email, password, role) 
VALUES ('admin', 'admin@example.com', SHA2('admin_password', 256), 'admin');
