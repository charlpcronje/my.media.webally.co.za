CREATE TABLE analytics (
  id INT PRIMARY KEY AUTO_INCREMENT,
  media_id INT NOT NULL,
  user_id INT NOT NULL,
  session_id VARCHAR(100) NOT NULL,
  device_type VARCHAR(50),
  browser VARCHAR(50),
  os VARCHAR(50),
  start_time TIMESTAMP NOT NULL,
  end_time TIMESTAMP NULL,
  duration INT,
  completed BOOLEAN DEFAULT FALSE,
  skipped BOOLEAN DEFAULT FALSE,
  skip_position INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (media_id) REFERENCES media(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
