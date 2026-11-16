CREATE DATABASE IF NOT EXISTS trinidad_portfolio
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE trinidad_portfolio;

CREATE TABLE IF NOT EXISTS admins (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    failed_login_attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
    locked_until DATETIME NULL,
    last_login_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS site_profile (
    id TINYINT UNSIGNED PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    tagline VARCHAR(190) NOT NULL,
    introduction TEXT NOT NULL,
    biography TEXT NOT NULL,
    email VARCHAR(190) NOT NULL,
    location VARCHAR(190) NOT NULL,
    school VARCHAR(190) NOT NULL,
    profile_image VARCHAR(255) NOT NULL DEFAULT 'img/profile.jpg',
    about_image VARCHAR(255) NOT NULL DEFAULT 'img/nature.jpg',
    github_url VARCHAR(255) NOT NULL DEFAULT '',
    linkedin_url VARCHAR(255) NOT NULL DEFAULT '',
    facebook_url VARCHAR(255) NOT NULL DEFAULT '',
    instagram_url VARCHAR(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS site_settings (
    id TINYINT UNSIGNED PRIMARY KEY,
    site_title VARCHAR(190) NOT NULL,
    logo_text VARCHAR(80) NOT NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS skills (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(100) NOT NULL DEFAULT 'Technical',
    icon_class VARCHAR(100) NOT NULL DEFAULT '',
    level TINYINT UNSIGNED NOT NULL DEFAULT 0,
    sort_order INT NOT NULL DEFAULT 0,
    CONSTRAINT skills_level_range CHECK (level <= 100)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS projects (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(190) NOT NULL,
    description TEXT NOT NULL,
    image_path VARCHAR(255) NOT NULL DEFAULT '',
    technologies VARCHAR(500) NOT NULL DEFAULT '',
    project_url VARCHAR(500) NOT NULL DEFAULT '',
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS education (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    institution VARCHAR(190) NOT NULL,
    qualification VARCHAR(190) NOT NULL,
    dates VARCHAR(100) NOT NULL DEFAULT '',
    description TEXT NOT NULL,
    sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS experience (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(190) NOT NULL,
    organization VARCHAR(190) NOT NULL DEFAULT '',
    dates VARCHAR(100) NOT NULL DEFAULT '',
    description TEXT NOT NULL,
    sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS achievements (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(190) NOT NULL,
    organization VARCHAR(190) NOT NULL DEFAULT '',
    achieved_on VARCHAR(100) NOT NULL DEFAULT '',
    description TEXT NOT NULL,
    image_path VARCHAR(255) NOT NULL DEFAULT '',
    sort_order INT NOT NULL DEFAULT 0
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS contact_messages (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(190) NOT NULL,
    subject VARCHAR(190) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX contact_messages_read_created (is_read, created_at)
) ENGINE=InnoDB;

INSERT INTO site_profile (
    id, name, tagline, introduction, biography, email, location, school,
    profile_image, about_image, github_url, linkedin_url, facebook_url, instagram_url
) VALUES (
    1,
    'Roche Trinidad',
    'BSINT Student | Future IT Professional',
    'I''m a passionate and motivated BS Information Technology student from NORSU-BSC. I love learning new things, building projects, and turning ideas into reality.',
    'I''m Roche Trinidad, a BS Information Technology student at NORSU-BSC. I''m a simple and determined person who enjoys exploring new things, meeting new people, and making the most out of every opportunity.',
    'trinidadroche702@gmail.com',
    'Santa Catalina, Negros Oriental, Philippines',
    'NORSU-BSC',
    'img/profile.jpg',
    'img/nature.jpg',
    'https://github.com/trinidadroche702-creator',
    'https://www.linkedin.com/in/trinidad-roche-70421343b/',
    'https://www.facebook.com/trinidad.roche.3',
    'https://www.instagram.com/cheng_koyyy'
)
ON DUPLICATE KEY UPDATE id = VALUES(id);

INSERT INTO site_settings (id, site_title, logo_text)
VALUES (1, 'Roche Trinidad | Portfolio', 'Roche')
ON DUPLICATE KEY UPDATE id = VALUES(id);

INSERT INTO skills (name, category, icon_class, level, sort_order) VALUES
    ('HTML', 'Technical', 'fa-brands fa-html5', 85, 1),
    ('CSS', 'Technical', 'fa-brands fa-css3-alt', 85, 2),
    ('JavaScript', 'Technical', 'fa-brands fa-js', 70, 3),
    ('C++', 'Technical', '', 75, 4),
    ('Java', 'Technical', 'fa-brands fa-java', 70, 5),
    ('MySQL', 'Technical', 'fa-solid fa-database', 65, 6),
    ('Git & GitHub', 'Technical', 'fa-brands fa-github', 70, 7),
    ('Cisco Packet Tracer', 'Technical', 'fa-solid fa-network-wired', 70, 8),
    ('Problem Solving', 'Soft Skills', '', 0, 1),
    ('Teamwork', 'Soft Skills', '', 0, 2),
    ('Communication', 'Soft Skills', '', 0, 3),
    ('Time Management', 'Soft Skills', '', 0, 4),
    ('Adaptability', 'Soft Skills', '', 0, 5),
    ('Creativity', 'Soft Skills', '', 0, 6);

INSERT INTO projects (title, description, image_path, technologies, sort_order) VALUES
    ('Advertisement Project', 'A 2 minute Advertisement Project', 'img/Nexora.png', 'Animation, Adobe Animate', 1),
    ('Budget Tracker', 'A web-based tracker with parent dashboard for children''s expenses and allowance notifications.', 'img/removed-blur.jpg', 'Java, MySQL, Web', 2),
    ('Campus Lost & Found', 'A system to report and track lost and found items on campus.', 'img/Campus.png', 'Java, PHP, HTML, CSS, MySQL, Web', 3),
    ('Visual Graphics Project', 'A low-poly digital illustration of a penguin created in Adobe Illustrator using geometric shapes and carefully arranged polygons.', 'img/Poly Art.png', 'Visual Graphics & Design, Adobe Illustrator', 4);

INSERT INTO education (institution, qualification, dates, description, sort_order) VALUES
    ('Negros Oriental State University - Bais City Campus', 'Bachelor of Science in Information Technology', 'Current', 'Currently a 3rd-year BS Information Technology student developing skills in programming, web development, databases, networking, and system development.', 1),
    ('Senior High School', 'Senior High School', 'Completed', 'Completed senior high school education and developed an interest in technology, learning, and personal development.', 2);

INSERT INTO experience (title, organization, dates, description, sort_order) VALUES
    ('Restaurant Cashier', '', '3 Years', 'Handled customer transactions and payments, assisted customers, managed busy shifts, and took responsibility when the manager was unavailable.', 1);
