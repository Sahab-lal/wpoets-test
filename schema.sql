CREATE TABLE IF NOT EXISTS slides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tab_key VARCHAR(50) NOT NULL,
    tab_label VARCHAR(100) NOT NULL,
    slide_title VARCHAR(150) NOT NULL,
    slide_body TEXT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    icon_path VARCHAR(255) NOT NULL,
    position INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO slides (tab_key, tab_label, slide_title, slide_body, image_path, icon_path, position) VALUES
('communication', 'Communication', 'Clear project touchpoints', 'Keep everyone aligned with short, predictable updates that reduce rework and confusion.', 'files/images/DL-Communication.jpg', 'files/images/DL-communication.svg', 1),
('communication', 'Communication', 'Feedback loops that stick', 'Capture feedback, turn it into decisions, and communicate the outcome quickly.', 'files/images/DL-Communication.jpg', 'files/images/DL-communication.svg', 2),
('learning', 'Learning', 'Teams learn faster together', 'Share insights in small, reusable lessons that improve every next sprint.', 'files/images/DL-Learning-1.jpg', 'files/images/DL-learning.svg', 1),
('learning', 'Learning', 'Skills that scale', 'Build a library of short training modules and track progress in one place.', 'files/images/DL-Learning-1.jpg', 'files/images/DL-learning.svg', 2),
('technology', 'Technology', 'A stack that stays simple', 'Choose tools that are easy to maintain and keep shipping without friction.', 'files/images/DL-Technology.jpg', 'files/images/DL-technology.svg', 1),
('technology', 'Technology', 'Automation with purpose', 'Automate the repeatable so your team can focus on creative work.', 'files/images/DL-Technology.jpg', 'files/images/DL-technology.svg', 2);
