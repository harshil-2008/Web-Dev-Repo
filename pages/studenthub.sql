-- ====================================================================
-- STUDENTHUB DATABASE SCHEMA
-- Designed for StudentHub Portal (Students, Events, Registrations)
-- Beginner-friendly SQL statements for MySQL / phpMyAdmin / MariaDB
-- ====================================================================

-- 1. Create the Database if it doesn't already exist
CREATE DATABASE IF NOT EXISTS studenthub;
USE studenthub;

-- ====================================================================
-- TABLE 1: students
-- Stores registered student profile details
-- ====================================================================
CREATE TABLE IF NOT EXISTS students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mobile VARCHAR(15) NOT NULL,
    course VARCHAR(50) NOT NULL,
    year INT NOT NULL,
    gender VARCHAR(10) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ====================================================================
-- TABLE 2: events
-- Stores campus events, seminars, workshops, and fests
-- ====================================================================
CREATE TABLE IF NOT EXISTS events (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    event_date DATE NOT NULL,
    venue VARCHAR(100) NOT NULL,
    category VARCHAR(50) DEFAULT 'Campus',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ====================================================================
-- TABLE 3: registrations
-- Relational table linking which student registered for which event
-- Uses FOREIGN KEY referencing students(student_id) and events(event_id)
-- ====================================================================
CREATE TABLE IF NOT EXISTS registrations (
    registration_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    event_id INT NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(20) DEFAULT 'Confirmed',
    FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE
);

-- ====================================================================
-- SAMPLE DATA INSERTIONS
-- ====================================================================

-- Insert Sample Students
INSERT INTO students (name, email, mobile, course, year, gender, password) VALUES
('Harshil Prajapati', 'hp.prj.23@gmail.com', '9876543210', 'Computer Engineering', 2, 'Male', 'Password@123'),
('Natasha Romanoff', 'natasha.romanof@gmail.com', '9123456780', 'Computer Science', 1, 'Female', 'Secret@2026'),
('Aarav Patel', 'aarav.patel@charusat.edu.in', '9898989898', 'Information Technology', 3, 'Male', 'Aarav#2026'),
('Diya Sharma', 'diya.sharma@charusat.edu.in', '9765432109', 'Computer Engineering', 2, 'Female', 'Diya@Pass1'),
('Rohan Mehta', 'rohan.mehta@charusat.edu.in', '9654321098', 'Mechanical Engineering', 4, 'Male', 'Rohan#2026');

-- Insert Sample Events
INSERT INTO events (title, description, event_date, venue, category) VALUES
('💻 Hackathon 2026', '24-hour campus coding competition solving real-world challenges.', '2026-09-15', 'Innovation Hall', 'Competition'),
('🎭 Cultural Fest', 'Annual cultural celebration with music, dance, and drama.', '2026-09-28', 'Open Ground', 'Cultural'),
('🤖 Robotics Workshop', 'Hands-on hardware and robotics automation workshop.', '2026-10-05', 'Lab 302', 'Workshop'),
('🔐 Cyber Security Seminar', 'Industry expert seminar on web security and ethical hacking.', '2026-10-18', 'Seminar Hall', 'Seminar'),
('🚀 Startup Pitch Day', 'Pitch your innovative startup ideas to alumni investors.', '2026-11-02', 'Auditorium 1', 'Competition');

-- Insert Sample Event Registrations
INSERT INTO registrations (student_id, event_id, status) VALUES
(1, 1, 'Confirmed'),
(1, 3, 'Confirmed'),
(2, 2, 'Confirmed'),
(3, 4, 'Confirmed'),
(4, 1, 'Confirmed'),
(5, 5, 'Confirmed');

-- ====================================================================
-- HELPFUL SAMPLE QUERIES FOR BEGINNERS:
-- 
-- 1. View all registered students:
--    SELECT * FROM students;
--
-- 2. View all upcoming events:
--    SELECT * FROM events ORDER BY event_date ASC;
--
-- 3. View which student registered for which event (JOIN Query):
--    SELECT 
--        r.registration_id,
--        s.name AS student_name,
--        s.email AS student_email,
--        e.title AS event_title,
--        e.venue AS event_venue,
--        r.registration_date,
--        r.status
--    FROM registrations r
--    JOIN students s ON r.student_id = s.student_id
--    JOIN events e ON r.event_id = e.event_id;
-- ====================================================================
