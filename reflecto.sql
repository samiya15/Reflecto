DROP DATABASE IF EXISTS `reflecto` ;
CREATE DATABASE reflecto ;
USE reflecto ;

DROP TABLE IF EXISTS `faculty` ;
CREATE TABLE `faculty`(
faculty_id INT AUTO_INCREMENT PRIMARY KEY,
faculty_name VARCHAR(100),
course_name VARCHAR(100)
);
DROP TABLE IF EXISTS `course` ;
CREATE TABLE `course`(
course_id INT AUTO_INCREMENT PRIMARY KEY,
course_name VARCHAR(100),
faculty_name VARCHAR(100),
faculty_id INT,
FOREIGN KEY(faculty_id) REFERENCES faculty(faculty_id)
);

DROP TABLE IF EXISTS `courseAdmin` ;
CREATE TABLE `courseAdmin`(
course_admin_id INT AUTO_INCREMENT PRIMARY KEY,
course_admin_name VARCHAR(100),
email VARCHAR(100) UNIQUE NOT NULL,
faculty_name VARCHAR(100),
password VARCHAR(255) NOT NULL,
faculty_id INT,
FOREIGN KEY(faculty_id) REFERENCES faculty(faculty_id)
);

DROP TABLE IF EXISTS `users` ;
CREATE TABLE `users`(
user_id INT AUTO_INCREMENT PRIMARY KEY,
email VARCHAR(100) UNIQUE NOT NULL,
password VARCHAR(255) NOT NULL,
role INT NOT NULL -- 1=student, 2=lecturer, 3=systemadmin, 4=courseadmin--
);

DROP TABLE IF EXISTS `students` ;
CREATE TABLE `students`(
student_id INT AUTO_INCREMENT PRIMARY KEY,
student_name VARCHAR(100),
email VARCHAR(100) UNIQUE NOT NULL,
faculty_name VARCHAR(100),
student_course VARCHAR(100),
year_of_study INT NOT NULL,
password VARCHAR(255) NOT NULL,
faculty_id INT,
FOREIGN KEY(faculty_id) REFERENCES faculty(faculty_id)
);

DROP TABLE IF EXISTS `lecturers` ;
CREATE TABLE `lecturers`(
lecturer_id INT AUTO_INCREMENT PRIMARY KEY,
lecturer_name VARCHAR(100),
email VARCHAR(100) UNIQUE NOT NULL,
faculty_name VARCHAR(100),
course_taught VARCHAR(100),
password VARCHAR(255) NOT NULL,
faculty_id INT,
FOREIGN KEY(faculty_id) REFERENCES faculty(faculty_id)
);

DROP TABLE IF EXISTS `systemAdmin` ;
CREATE TABLE `systemAdmin`(
system_admin_id INT AUTO_INCREMENT PRIMARY KEY,
system_admin_name VARCHAR(100),
email VARCHAR(100) UNIQUE NOT NULL,
password VARCHAR(255) NOT NULL
);




DROP TABLE IF EXISTS `form` ;
CREATE TABLE `form`(
form_id INT AUTO_INCREMENT PRIMARY KEY,
is_anonymous BOOLEAN,
message VARCHAR(500),
Times TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
profanity_filter BOOLEAN, --1 if profanity is found, 0 otherwise--
sentiment_score VARCHAR(50), --'POSITIVE', 'NEUTRAL', 'NEGATIVE'--
course_id INT,
student_id INT,
lecturer_id INT,
system_admin_id INT,
FOREIGN KEY (course_id) REFERENCES course(course_id),
FOREIGN KEY (student_id) REFERENCES students(student_id),
FOREIGN KEY (lecturer_id) REFERENCES lecturers(lecturer_id),
FOREIGN KEY (system_admin_id) REFERENCES systemAdmin(admin_id)
);
