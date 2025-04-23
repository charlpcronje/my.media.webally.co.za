CREATE TABLE media (
  id INT PRIMARY KEY AUTO_INCREMENT,
  title VARCHAR(100) NOT NULL,
  description TEXT,
  type ENUM('video', 'audio') NOT NULL,
  file_path VARCHAR(255) NOT NULL,
  thumbnail_path VARCHAR(255),
  duration INT,
  tags VARCHAR(255),
  average_rating DECIMAL(3,2) DEFAULT 0,
  ratings_count INT DEFAULT 0,
  play_count INT DEFAULT 0,
  uploaded_by INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (uploaded_by) REFERENCES users(id)
);
