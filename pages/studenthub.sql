-- ====================================================================
-- CHAROTAR UNIVERSITY OF SCIENCE AND TECHNOLOGY (CHARUSAT)
-- FACULTY OF TECHNOLOGY AND ENGINEERING (FTE)
-- SUBJECT: WEB DEVELOPMENT FRAMEWORKS (ITUE203)
-- STUDENT: Harshil Prajapati | ID: 25DCE093 | B.Tech (CE) - SEM 3
-- DATABASE SCHEMA: StudentHub Portal (studenthub)
-- ====================================================================

CREATE DATABASE IF NOT EXISTS studenthub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE studenthub;

-- --------------------------------------------------------------------
-- TABLE 1: users (Authentication & Role-Based Access Control)
-- --------------------------------------------------------------------
DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS registrations;
DROP TABLE IF EXISTS events;
DROP TABLE IF EXISTS students;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'student') NOT NULL DEFAULT 'student',
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    remember_token VARCHAR(255) DEFAULT NULL,
    last_login TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------------------
-- TABLE 2: students (Student Profile & Academic Records)
-- --------------------------------------------------------------------
CREATE TABLE students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mobile VARCHAR(15) NOT NULL,
    course VARCHAR(60) NOT NULL,
    year INT NOT NULL CHECK (year BETWEEN 1 AND 4),
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    status ENUM('active', 'inactive', 'deleted') NOT NULL DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------------------
-- TABLE 3: events (Campus Events & Workshops with Poster Images)
-- --------------------------------------------------------------------
CREATE TABLE events (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    event_date DATE NOT NULL,
    venue VARCHAR(100) NOT NULL,
    category ENUM('Technical', 'Cultural', 'Workshop', 'Sports', 'Seminar', 'Competition') NOT NULL DEFAULT 'Technical',
    poster VARCHAR(255) DEFAULT 'default_event.jpg',
    status ENUM('Upcoming', 'Ongoing', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Upcoming',
    created_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------------------
-- TABLE 4: registrations (Event Enrollments Linking Students & Events)
-- --------------------------------------------------------------------
CREATE TABLE registrations (
    registration_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    event_id INT NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Confirmed', 'Pending', 'Cancelled') NOT NULL DEFAULT 'Confirmed',
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE,
    UNIQUE KEY unique_student_event (student_id, event_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------------------
-- TABLE 5: audit_logs (Administrative Traceability & Security Auditing)
-- --------------------------------------------------------------------
CREATE TABLE audit_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    username VARCHAR(50) NOT NULL,
    action_type VARCHAR(50) NOT NULL,
    target_entity VARCHAR(50) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45) DEFAULT '127.0.0.1',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ====================================================================
-- SEED DATA INSERTIONS
-- ====================================================================

-- 1. Insert Initial Users (Password: Admin@123 for admin, Student@123 for students)
-- BCRYPT hash for 'Admin@123' = $2y$10$wE1h0E7vL2n7O3Nf/qNlVu8Wf4Hk1D.K9bZp7.F3Q7R6s5T4U3V2W
-- BCRYPT hash for 'Student@123' = $2y$10$yG9rZ5mB6dO4L1N8kU.Jt.X9pQ2eA8zC4fE3vB6nT8mK7yP1qO9re
INSERT INTO users (user_id, username, email, password_hash, role, status) VALUES
(1, 'admin', 'admin@charusat.edu.in', '$2y$10$wE1h0E7vL2n7O3Nf/qNlVu8Wf4Hk1D.K9bZp7.F3Q7R6s5T4U3V2W', 'admin', 'active'),
(2, 'harshil', 'hp.prj.23@gmail.com', '$2y$10$yG9rZ5mB6dO4L1N8kU.Jt.X9pQ2eA8zC4fE3vB6nT8mK7yP1qO9re', 'student', 'active'),
(3, 'natasha', 'natasha.romanoff@charusat.edu.in', '$2y$10$yG9rZ5mB6dO4L1N8kU.Jt.X9pQ2eA8zC4fE3vB6nT8mK7yP1qO9re', 'student', 'active'),
(4, 'aarav', 'aarav.patel@charusat.edu.in', '$2y$10$yG9rZ5mB6dO4L1N8kU.Jt.X9pQ2eA8zC4fE3vB6nT8mK7yP1qO9re', 'student', 'active');

-- 2. Insert 22 Seeded Students (Required minimum 20 for Practical 11)
INSERT INTO students (student_id, user_id, name, email, mobile, course, year, gender, status) VALUES
(1, 2, 'Harshil Prajapati', 'hp.prj.23@gmail.com', '9876543210', 'Computer Engineering', 2, 'Male', 'active'),
(2, 3, 'Natasha Romanoff', 'natasha.romanoff@charusat.edu.in', '9123456780', 'Computer Science', 3, 'Female', 'active'),
(3, 4, 'Aarav Patel', 'aarav.patel@charusat.edu.in', '9898989898', 'Information Technology', 3, 'Male', 'active'),
(4, NULL, 'Diya Sharma', 'diya.sharma@charusat.edu.in', '9765432109', 'Computer Engineering', 2, 'Female', 'active'),
(5, NULL, 'Rohan Mehta', 'rohan.mehta@charusat.edu.in', '9654321098', 'Mechanical Engineering', 4, 'Male', 'active'),
(6, NULL, 'Ananya Iyer', 'ananya.iyer@charusat.edu.in', '9823456712', 'Computer Engineering', 1, 'Female', 'active'),
(7, NULL, 'Devansh Shah', 'devansh.shah@charusat.edu.in', '9812345678', 'Information Technology', 2, 'Male', 'active'),
(8, NULL, 'Ishita Joshi', 'ishita.joshi@charusat.edu.in', '9723456781', 'Electronics & Communication', 3, 'Female', 'active'),
(9, NULL, 'Kabir Verma', 'kabir.verma@charusat.edu.in', '9634567812', 'Computer Science', 2, 'Male', 'active'),
(10, NULL, 'Meera Nair', 'meera.nair@charusat.edu.in', '9545678123', 'Computer Engineering', 4, 'Female', 'active'),
(11, NULL, 'Pranav Desai', 'pranav.desai@charusat.edu.in', '9456781234', 'Information Technology', 1, 'Male', 'active'),
(12, NULL, 'Riya Kapoor', 'riya.kapoor@charusat.edu.in', '9367812345', 'Computer Engineering', 3, 'Female', 'active'),
(13, NULL, 'Siddharth Rao', 'siddharth.rao@charusat.edu.in', '9278912345', 'Mechanical Engineering', 2, 'Male', 'active'),
(14, NULL, 'Tanvi Trivedi', 'tanvi.trivedi@charusat.edu.in', '9189123456', 'Computer Science', 2, 'Female', 'active'),
(15, NULL, 'Utkarsh Bhatt', 'utkarsh.bhatt@charusat.edu.in', '9091234567', 'Electronics & Communication', 4, 'Male', 'active'),
(16, NULL, 'Vaidehi Dave', 'vaidehi.dave@charusat.edu.in', '9982345670', 'Computer Engineering', 1, 'Female', 'active'),
(17, NULL, 'Yash Pandya', 'yash.pandya@charusat.edu.in', '9873456701', 'Information Technology', 3, 'Male', 'active'),
(18, NULL, 'Zoya Khan', 'zoya.khan@charusat.edu.in', '9764567012', 'Computer Science', 2, 'Female', 'active'),
(19, NULL, 'Aditya Choudhary', 'aditya.c@charusat.edu.in', '9655670123', 'Mechanical Engineering', 3, 'Male', 'active'),
(20, NULL, 'Bhavya Parikh', 'bhavya.p@charusat.edu.in', '9546780124', 'Computer Engineering', 2, 'Female', 'active'),
(21, NULL, 'Chirag Solanki', 'chirag.s@charusat.edu.in', '9437890125', 'Information Technology', 4, 'Male', 'active'),
(22, NULL, 'Drashti Vyas', 'drashti.v@charusat.edu.in', '9328901236', 'Electronics & Communication', 1, 'Female', 'active');

-- 3. Insert Events
INSERT INTO events (event_id, title, description, event_date, venue, category, poster, status, created_by) VALUES
(1, 'Hackathon 2026', '24-hour campus coding competition solving real-world challenges.', '2026-11-15', 'Innovation Hall', 'Competition', 'hackathon2026.png', 'Upcoming', 1),
(2, 'Cultural Fest: Spandan', 'Annual cultural celebration featuring musical concerts, drama, dance, and fashion show.', '2026-11-28', 'University Open Ground', 'Cultural', 'spandan.png', 'Upcoming', 1),
(3, 'Robotics & AI Hands-on Workshop', 'Learn ROS and computer vision algorithms with hands-on bot building.', '2026-12-05', 'Lab 302, CE Dept', 'Workshop', 'robotics.png', 'Upcoming', 1),
(4, 'Cyber Security & Ethical Hacking', 'Industry keynote on zero-day exploits, OWASP top 10, and modern defense architecture.', '2026-12-18', 'Seminar Hall 1', 'Seminar', 'cybersec.png', 'Upcoming', 1),
(5, 'National Tech Symposium', 'Paper presentations, code sprint, and project showcase.', '2027-01-10', 'Auditorium A', 'Technical', 'techsymp.png', 'Upcoming', 1),
(6, 'Campus Cricket League', 'Inter-department cricket tournament with exciting trophies.', '2027-01-25', 'Sports Complex', 'Sports', 'cricket.png', 'Upcoming', 1);

-- 4. Insert Registrations
INSERT INTO registrations (student_id, event_id, status) VALUES
(1, 1, 'Confirmed'),
(1, 3, 'Confirmed'),
(2, 1, 'Confirmed'),
(2, 2, 'Confirmed'),
(3, 4, 'Confirmed'),
(4, 1, 'Confirmed'),
(5, 6, 'Confirmed'),
(6, 2, 'Confirmed'),
(7, 3, 'Confirmed'),
(8, 5, 'Confirmed');

-- 5. Insert Sample Audit Logs
INSERT INTO audit_logs (user_id, username, action_type, target_entity, details, ip_address) VALUES
(1, 'admin', 'INSERT', 'events', 'Created new event: Hackathon 2026', '127.0.0.1'),
(1, 'admin', 'UPDATE', 'students', 'Updated enrollment status for student ID 2', '127.0.0.1'),
(1, 'admin', 'LOGIN', 'auth', 'Admin successfully logged in', '127.0.0.1'),
(2, 'harshil', 'REGISTER', 'events', 'Registered for Robotics & AI Workshop', '127.0.0.1');
