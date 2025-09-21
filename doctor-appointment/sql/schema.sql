-- Create database
CREATE DATABASE IF NOT EXISTS doctor_app;
USE doctor_app;

-- Users table (patients)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Doctors table
CREATE TABLE IF NOT EXISTS doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    specialization VARCHAR(100) NOT NULL,
    available_days VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Appointments table
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    doctor_id INT NOT NULL,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    status ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
);

-- Insert dummy users
INSERT INTO users (name, email, password) VALUES
('Alice Johnson', 'alice@example.com', 'password123'),
('Bob Smith', 'bob@example.com', 'password456'),
('Charlie Brown', 'charlie@example.com', 'password789');

-- Insert dummy doctors
INSERT INTO doctors (name, specialization, available_days) VALUES
('Dr. Emily Davis', 'Cardiologist', 'Mon, Wed, Fri'),
('Dr. Rajesh Kumar', 'Dermatologist', 'Tue, Thu, Sat'),
('Dr. Sarah Lee', 'Pediatrician', 'Mon, Tue, Thu');

-- Insert dummy appointments
INSERT INTO appointments (user_id, doctor_id, appointment_date, appointment_time, status) VALUES
(1, 1, '2025-09-15', '10:00:00', 'confirmed'),
(2, 2, '2025-09-16', '14:30:00', 'pending'),
(3, 3, '2025-09-17', '09:00:00', 'cancelled');
