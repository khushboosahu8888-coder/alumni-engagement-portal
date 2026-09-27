-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Sep 26, 2026 at 11:53 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `alumni_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `alumni_profile`
--

CREATE TABLE `alumni_profile` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `batch_year` varchar(100) DEFAULT NULL,
  `company` varchar(150) DEFAULT NULL,
  `position` varchar(150) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alumni_profile`
--

INSERT INTO `alumni_profile` (`id`, `user_id`, `batch_year`, `company`, `position`, `bio`, `created_at`, `updated_at`) VALUES
(2, 1007, '2019-22', 'Tech Innovations Corp.', 'Software Engineer', 'Passionate Software Engineer with experience in building scalable web applications and modern digital solution.Always eager to learn, innovate, and contribute to impactful tech projects.', '2025-11-20 03:01:56', '2025-12-10 02:24:26'),
(3, 1009, '2020-23', 'Amazon India', 'HR Executive', 'Human Resource Executive at Amazon with experience in hiring, onboarding, and employee engagement.', '2025-12-10 01:34:12', '2025-12-10 01:34:12'),
(4, 1010, '2020-23', 'Infosys', 'System Analyst', 'Working as a System Analyst in Infosys. Skilled in IT support, data analysis, and project coordination. Open to helping students with IT career advice.', '2025-12-10 01:36:00', '2025-12-10 01:36:00'),
(5, 1011, '2021-24', 'HDFC Bank', 'Finance Associate', 'Finance professional with expertise in banking operations, customer service, and portfolio management. Happy to help students explore finance careers.', '2025-12-10 01:38:01', '2025-12-10 01:38:01'),
(6, 1030, '2018', 'Tata Consultancy Services (TCS)', 'Software Engineer', 'A passionate software engineer with 6+ years of experience in application development and system integration. Actively mentors students in programming, career planning, and interview preparation.', '2026-01-02 01:16:56', '2026-01-02 01:16:56'),
(7, 1031, '2017', 'Infosys', 'Senior Systems Analyst', 'Experienced IT professional specializing in enterprise solutions and business analysis. Enjoys guiding students on corporate life, skill development, and transitioning from college to industry.', '2026-01-02 01:18:44', '2026-01-02 01:18:44'),
(8, 1032, '2015', 'Accenture', 'Technology Consultant', 'Technology consultant with expertise in digital transformation, cloud solutions, and client management. Regularly supports students with career advice and emerging technology trends.', '2026-01-02 01:19:53', '2026-01-02 01:19:53'),
(9, 1033, '2020', 'Wipro', 'Project Engineer', 'Project engineer working on enterprise applications and quality assurance. Passionate about mentoring juniors and helping students build strong technical and communication skills.', '2026-01-02 01:22:16', '2026-01-02 01:22:16'),
(10, 1034, '2016', 'HDFC Bank', 'Business Analyst', 'Business analyst with experience in data-driven decision-making and financial systems. Actively participates in alumni events and provides guidance on analytics, finance careers, and professional growth.', '2026-01-02 01:23:45', '2026-01-02 01:23:45'),
(11, 1036, '2018-21', 'InfoTech', 'Software Engineer', '', '2026-09-27 03:16:37', '2026-09-27 03:16:37');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` date NOT NULL,
  `event_time` time NOT NULL,
  `location` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `description`, `event_date`, `event_time`, `location`) VALUES
(27, 'Alumni–Student Networking Meetup 2026', 'A special networking event designed to connect current students with successful alumni working in various industries. Students can seek guidance, career tips, internship referral support, and mentorship opportunities. Light refreshments will be provided', '2026-01-10', '10:00:00', 'College Seminar Hall '),
(29, 'Tech Talk: Future of AI & Automation', 'A guest lecture by distinguished alumni working in top tech companies. This session will cover AI trends, job market expectations, essential tech skills, and project-building tips for students.', '2026-03-08', '11:00:00', 'College Seminar Hall'),
(30, 'Career Guidance Workshop for Final Year Students', 'This workshop will focus on resume building, interview skills, personality development, communication skills, and placement preparation strategies. Alumni from HR and corporate roles will guide students.', '2026-03-21', '08:30:00', 'Room 1204'),
(31, 'Alumni–Student Sports Meetup', 'A friendly cricket & badminton match between alumni and students, aimed at building stronger relationships & promoting teamwork.', '2026-02-10', '07:30:00', 'College Sports Ground'),
(32, 'Alumni Homecoming Day 2025', 'A fun, informal gathering where alumni from different batches reunite, share memories, and celebrate their college journey. Cultural performances, photo sessions, and interaction zones will be arranged.', '2025-12-30', '13:00:00', 'College Central Auditorium'),
(33, 'Alumni–Student Career Networking Meet', 'An interactive networking session where alumni from various industries will share career insights, job opportunities, and professional experiences. Students can ask questions, seek mentorship, and build valuable connections for their future careers.', '2026-04-20', '12:15:00', 'College Seminar Hall'),
(34, 'Alumni Success Stories & Motivation Talk', 'Successful alumni will share their personal journeys, challenges, and achievements after graduation. This session aims to motivate students and provide guidance on career planning, higher studies, and skill development.', '2026-05-30', '08:00:00', 'College Auditorium'),
(35, 'Industry Interaction & Skill Development Workshop', 'A hands-on workshop conducted by industry-expert alumni focusing on in-demand skills, resume building, interview preparation, and current industry trends to help students become job-ready.', '2026-08-18', '09:15:00', 'Computer Lab 3'),
(36, 'Alumni–Student Entrepreneurship Meetup', 'An open discussion forum where alumni entrepreneurs share startup experiences, business ideas, funding strategies, and real-world challenges. Ideal for students interested in startups and innovation.', '2026-02-25', '10:30:00', 'Room 1204'),
(37, 'Alumni-Led Resume Review & Mock Interview Day', 'Students can get their resumes reviewed by alumni professionals and participate in mock interviews to receive real-time feedback and improvement suggestions.', '2026-08-29', '10:00:00', 'Placement Cell, Training Block'),
(38, 'Web Development Workshop', 'Hands-on workshop on HTML, CSS, JavaScript and PHP.', '2026-10-10', '10:30:00', 'Computer Lab'),
(39, 'Data Analytics Workshop', 'Hands on workshop on excel,sql,powerBI', '2027-12-30', '10:00:00', 'Computer Lab');

-- --------------------------------------------------------

--
-- Table structure for table `event_registrations`
--

CREATE TABLE `event_registrations` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('registered','cancelled','attended') NOT NULL DEFAULT 'registered',
  `registered_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_registrations`
--

INSERT INTO `event_registrations` (`id`, `event_id`, `user_id`, `status`, `registered_at`) VALUES
(1, 38, 1013, 'registered', '2026-09-27 02:04:21'),
(2, 38, 1007, 'registered', '2026-09-27 02:46:25'),
(3, 39, 1007, 'registered', '2026-09-27 02:46:37'),
(4, 38, 1011, 'registered', '2026-09-27 02:47:39'),
(5, 39, 1011, 'registered', '2026-09-27 02:47:51'),
(6, 39, 1009, 'registered', '2026-09-27 02:48:20'),
(7, 39, 1023, 'registered', '2026-09-27 02:48:46'),
(8, 38, 1031, 'registered', '2026-09-27 02:49:12'),
(9, 38, 1014, 'registered', '2026-09-27 02:49:55'),
(10, 39, 1014, 'registered', '2026-09-27 02:50:00'),
(11, 39, 1006, 'registered', '2026-09-27 02:50:50'),
(12, 38, 1006, 'registered', '2026-09-27 02:50:53'),
(13, 38, 1012, 'registered', '2026-09-27 02:51:31'),
(14, 39, 1012, 'registered', '2026-09-27 02:51:36'),
(15, 38, 1029, 'registered', '2026-09-27 02:52:10'),
(16, 39, 1029, 'registered', '2026-09-27 02:52:15'),
(17, 38, 1027, 'registered', '2026-09-27 02:53:03'),
(18, 39, 1027, 'registered', '2026-09-27 02:53:07'),
(19, 38, 1026, 'registered', '2026-09-27 02:54:16'),
(20, 39, 1026, 'registered', '2026-09-27 02:54:20'),
(21, 38, 1024, 'registered', '2026-09-27 02:55:03'),
(22, 39, 1024, 'registered', '2026-09-27 02:55:07'),
(23, 38, 1035, 'registered', '2026-09-27 03:21:10');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `sender_id` int(10) UNSIGNED NOT NULL,
  `receiver_id` int(10) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `is_read`, `created_at`) VALUES
(1, 1006, 1007, 'hey', 1, '2025-12-04 17:38:17'),
(2, 1007, 1006, 'what up mate', 1, '2025-12-04 17:40:01'),
(3, 1006, 1007, 'hii sir!', 1, '2025-12-04 17:42:18'),
(4, 1007, 1006, 'hey! what are your queries?', 1, '2025-12-04 17:44:10'),
(5, 1006, 1007, '123', 1, '2025-12-04 17:46:36'),
(6, 1006, 1007, 'ghjhj', 1, '2025-12-04 17:46:44'),
(7, 1006, 1007, '4444444444', 1, '2025-12-04 17:46:53'),
(8, 1013, 1011, 'hey', 0, '2025-12-09 20:18:02'),
(9, 1007, 1026, 'hey student', 0, '2026-02-03 13:32:24'),
(10, 1007, 1013, 'hii', 0, '2026-09-26 19:37:41'),
(11, 1024, 1007, 'hii sir.', 1, '2026-09-26 21:37:05'),
(12, 1024, 1033, 'need help sir with academics', 0, '2026-09-26 21:38:03'),
(13, 1007, 1024, 'hello there!', 1, '2026-09-26 21:38:47'),
(14, 1007, 1013, 'hello', 0, '2026-09-26 21:39:06'),
(15, 1024, 1007, '😊', 1, '2026-09-26 21:40:09'),
(16, 1036, 1035, 'hey..Are you joining the workshop?', 1, '2026-09-26 21:47:33'),
(17, 1035, 1036, 'yeahh!', 0, '2026-09-26 21:48:02');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `post_type` enum('job','news') NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `posted_by` int(11) NOT NULL,
  `posted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `post_type`, `title`, `description`, `posted_by`, `posted_at`) VALUES
(18, 'job', 'Sales & Marketing Executive', '\"We seek motivated Sales Executives to handle digital marketing campaigns, client acquisition, and product promotions. Excellent communication skills and willingness to travel are required.\"\r\n\r\nCompany: GrowBiz Solutions\r\nLocation: Indore, Madhya Pradesh\r\nSalary: ₹2,50,000 – ₹3,20,000 per annum + Incentives\r\nAddress:\r\nGrowBiz Solutions,\r\nNear C21 Mall, Vijay Nagar, Indore – 452010\r\n\r\nContact:\r\n📞 +91 98930 11224\r\n📧 jobs@growbiz.in', 1007, '2025-12-10 02:03:10'),
(20, 'news', 'Free Online Certification Courses for Students', 'Google and Microsoft have announced free certification programs for students in fields like Cloud Computing, Cyber Security, and Digital Marketing. Interested students can apply through the official website.', 1007, '2025-12-10 02:04:38'),
(21, 'news', 'Our Alumni Wins ‘Young Innovator Award 2025’', 'Congratulations to our alumni, Rohit Sharma (Batch 2018-21), for winning the ‘Young Innovator Award 2025’ for his breakthrough work in AI-based healthcare solutions. His achievement inspires current students to pursue innovation and research.', 1007, '2025-12-10 02:04:59'),
(23, 'job', 'Software Developer', '\"We are looking for a Software Developer with strong programming skills in Java or Python. The candidate will work on web-based applications and collaborate with cross-functional teams.\"\r\n\r\nCompany: TechNova Systems\r\n\r\nLocation: Pune, Maharashtra\r\n\r\nSalary: ₹3,50,000 – ₹5,00,000 per annum\r\n\r\nAddress:\r\nTechNova Systems,\r\nHinjewadi Phase 2, Pune – 411057\r\n\r\nContact:\r\n📞 +91 98765 43210\r\n📧 careers@technova.in', 1009, '2026-01-02 01:34:58'),
(24, 'news', 'New Internship Opportunities for Students', 'Alumni working in various organizations have shared multiple internship opportunities for final-year students across IT, management, and analytics domains.', 1009, '2026-01-02 01:37:16'),
(25, 'news', 'Alumni Startup Receives Funding', 'An alumni-founded startup has successfully secured seed funding. The founder will soon share insights on entrepreneurship and startup challenges with students.', 1011, '2026-01-02 01:38:56'),
(26, 'news', 'Alumni Achieves Professional Certification', 'I (alumni) have successfully completed an international professional certification, showcasing continuous learning and career advancement.', 1011, '2026-01-02 01:40:26'),
(27, 'job', 'Web Development Intern', '\"We are hiring Web Development Interns for a paid internship program. Candidates should have basic knowledge of HTML, CSS, JavaScript, and PHP.\"\r\n\r\nCompany: CodeCraft Technologies\r\n\r\nLocation: Bhopal, Madhya Pradesh\r\n\r\nSalary: ₹10,000 – ₹15,000 per month\r\n\r\nAddress:\r\nCodeCraft Technologies,\r\nMP Nagar Zone 2, Bhopal – 462011\r\n\r\nContact:\r\n📞 +91 91234 56789\r\n📧 internships@codecrafttech.in', 1011, '2026-01-02 01:41:12'),
(28, 'job', 'Digital Marketing Executive', '\"We are looking for creative Digital Marketing Executives to manage social media campaigns, SEO, and online brand promotions.\"\r\n\r\nCompany: BrandSpark Media\r\n\r\nLocation: Mumbai, Maharashtra\r\n\r\nSalary: ₹3,00,000 – ₹4,20,000 per annum\r\n\r\nAddress:\r\nBrandSpark Media,\r\nAndheri East, Mumbai – 400069\r\n\r\nContact:\r\n📞 +91 97654 32109\r\n📧 hr@brandsparkmedia.in', 1010, '2026-01-02 01:42:01'),
(29, 'job', 'Network Support Engineer', '\"We seek Network Support Engineers to manage LAN/WAN networks, troubleshoot system issues, and ensure network security.\"\r\n\r\nCompany: NetSecure Solutions\r\n\r\nLocation: Hyderabad, Telangana\r\n\r\nSalary: ₹2,80,000 – ₹4,00,000 per annum\r\n\r\nAddress:\r\nNetSecure Solutions,\r\nMadhapur, Hyderabad – 500081\r\n\r\nContact:\r\n📞 +91 90123 45678\r\n📧 supportjobs@netsecure.in', 1031, '2026-01-02 01:43:00'),
(30, 'job', 'Business Analyst Trainee', '\"We are hiring Business Analyst Trainees to assist in requirement gathering, documentation, and data analysis. Fresh graduates are welcome.\"\r\n\r\nCompany: Axis Consulting Group\r\n\r\nLocation: Noida, Uttar Pradesh\r\n\r\nSalary: ₹3,20,000 – ₹4,50,000 per annum\r\n\r\nAddress:\r\nAxis Consulting Group,\r\nSector 62, Noida – 201309\r\n\r\nContact:\r\n📞 +91 93456 78901\r\n📧 jobs@axisconsulting.in', 1031, '2026-01-02 01:43:22'),
(31, 'job', 'UI/UX Designer', '\"We are seeking a creative UI/UX Designer to design user-friendly interfaces for web and mobile applications. Knowledge of Figma or Adobe XD is required.\"\r\n\r\nCompany: PixelWave Studio\r\n\r\nLocation: Ahmedabad, Gujarat\r\n\r\nSalary: ₹4,00,000 – ₹6,50,000 per annum\r\n\r\nAddress:\r\nPixelWave Studio,\r\nNavrangpura, Ahmedabad – 380009\r\n\r\nContact:\r\n📞 +91 95567 12345\r\n📧 design@pixelwavestudio.in', 1034, '2026-01-02 01:45:19'),
(32, 'news', 'Alumni Donate Books & Learning Resources', 'I (Alumni) have donated books, journals, and online learning resources to the college library to support academic growth of students.', 1034, '2026-01-02 01:46:39'),
(33, 'news', 'Cyber Security Roles See Rapid Growth', 'With increasing digital threats, demand for cyber security professionals is rising. Students should focus on network security, ethical hacking fundamentals, and relevant certifications.', 1033, '2026-01-02 01:49:44'),
(34, 'news', 'Resume Shortlisting Now Skill-Based', 'Recruiters are focusing more on skills and project work rather than academic scores alone. Students are encouraged to showcase internships, certifications, and hands-on projects on their resumes.', 1033, '2026-01-02 01:50:17'),
(35, 'news', 'Soft Skills Crucial for Career Growth', 'Along with technical skills, employers are giving importance to communication, teamwork, and problem-solving abilities. Students should actively participate in group discussions and presentations.', 1030, '2026-01-02 01:52:35'),
(36, 'news', 'Campus Placements Expected to Rise in 2025', 'Several companies have confirmed increased campus recruitment for 2025 graduates. Students should prepare early by strengthening technical knowledge, aptitude skills, and interview readiness.', 1032, '2026-01-02 01:53:50');

-- --------------------------------------------------------

--
-- Table structure for table `student_profile`
--

CREATE TABLE `student_profile` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `study_year` varchar(50) DEFAULT NULL,
  `course` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_profile`
--

INSERT INTO `student_profile` (`id`, `user_id`, `study_year`, `course`, `created_at`, `updated_at`) VALUES
(4, 1006, 'Third year', 'BCA', '2025-11-20 02:54:14', '2025-12-10 01:25:52'),
(5, 1012, 'Second year', 'Bcom', '2025-12-10 01:40:34', '2025-12-10 01:40:34'),
(6, 1013, 'Third year', 'BCA', '2025-12-10 01:41:24', '2025-12-10 01:41:24'),
(7, 1014, 'Third year', 'BCA', '2025-12-10 01:42:18', '2025-12-10 01:42:18'),
(8, 1015, 'Third semester', 'BBA', '2025-12-10 01:44:32', '2025-12-10 01:44:32'),
(9, 1020, 'Fourth year', 'B.Tech', '2026-01-02 01:04:28', '2026-01-02 01:04:28'),
(10, 1021, 'First Year', 'B.Sc (Information Technology)', '2026-01-02 01:06:07', '2026-01-02 01:06:07'),
(11, 1022, 'Final Year', 'BCA', '2026-01-02 01:07:27', '2026-01-02 01:07:27'),
(12, 1023, 'Second year', 'BBA', '2026-01-02 01:08:07', '2026-01-02 01:08:07'),
(13, 1024, 'Second year', 'B.COM', '2026-01-02 01:08:47', '2026-01-02 01:08:47'),
(14, 1025, 'Third year', 'B.Tech (Electronics & Communication)', '2026-01-02 01:09:33', '2026-01-02 01:09:33'),
(15, 1026, 'Second year', 'B.Sc (Data Science)', '2026-01-02 01:10:42', '2026-01-02 01:10:42'),
(16, 1027, 'First Year', 'BA', '2026-01-02 01:11:36', '2026-01-02 01:11:36'),
(17, 1028, 'Third year', 'B.Tech (Mechanical Engineering)', '2026-01-02 01:12:29', '2026-01-02 01:12:29'),
(18, 1029, 'Final Year', 'BCA', '2026-01-02 01:13:18', '2026-01-02 01:13:18'),
(19, 1035, 'Third year', 'BBA', '2026-09-27 03:15:36', '2026-09-27 03:15:36');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `role` enum('admin','student','alumni') NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `user_password`, `role`, `created_at`, `updated_at`) VALUES
(1001, 'Admin1', 'admin1@gmail.com', '$2y$10$0N5nBn/pTJxum1dtf94QYut6Kp68hZbQlRhg5KtLMcnRuKBirCsE6', 'admin', '2025-11-20 01:20:29', '2025-12-31 22:41:47'),
(1006, 'Rahul Sharma', 'rahul@gmail.com', '$2y$10$CRyDGq8b.07TK8C1jF4K3OZh3HXZ0NESO.el63YnrE.hE0z7sXGr.', 'student', '2025-11-20 02:54:14', '2025-11-20 02:54:14'),
(1007, 'Raj Verma', 'raj@gmail.com', '$2y$10$oG.fjFoJk9Cm52hVnsQSR.JKc0o/FqhT4w8VXuow0P99FOpVl.U86', 'alumni', '2025-11-20 03:01:56', '2025-12-02 18:47:49'),
(1009, 'Pawan Shrivastav', 'pawan@gmail.com', '$2y$10$NGOHn6DmN6K4/naEXzPSDeaRs7Yfce0XpoiBtNB91Bn2U8rofGqfu', 'alumni', '2025-12-10 01:34:12', '2025-12-10 01:34:12'),
(1010, 'Pooja Patel', 'pooja@gmail.com', '$2y$10$jypEhJl7AfkwuhxSV4RqwO26/8156dvWC4r9Kh/OB7RzCPjctnckO', 'alumni', '2025-12-10 01:36:00', '2025-12-10 01:36:00'),
(1011, 'Sohan Sharma', 'sohan@gmail.com', '$2y$10$LXHFzvkTyqOXwdMeSGQ.suCWWM4mz3t2ETk7mhExITrVRHMpseYfS', 'alumni', '2025-12-10 01:38:01', '2025-12-10 01:38:01'),
(1012, 'Anjali Mehra', 'anjali@gmail.com', '$2y$10$qJs//4eYr8l0gGYRxAAzZOYzORjfe/ZQ9lMhzHL55iH5HXlPeJHCS', 'student', '2025-12-10 01:40:34', '2025-12-10 01:40:34'),
(1013, 'Ankita Yadav', 'ankita@gmail.com', '$2y$10$Y7IzHyL2JAP41o2cPP21Y.4sH8loauWzfh6IXgRMIcD/0XlIuJN/K', 'student', '2025-12-10 01:41:24', '2025-12-10 01:41:24'),
(1014, 'Sneha kushwaha', 'sneha@gmail.com', '$2y$10$kX65snFhpFQ18ke6CSLMYurMZIL30GI5.v9dFkkjbpMXcSbdhX8Cq', 'student', '2025-12-10 01:42:18', '2025-12-10 01:42:18'),
(1015, 'Nidhi Chauhan', 'nidhi@gmail.com', '$2y$10$k/rWzM9iBAw/0ywAFGnlnu/rtopw0jZDsdodIicsUqYi4D6wSRbhS', 'student', '2025-12-10 01:44:32', '2025-12-10 01:44:32'),
(1016, 'Admin2', 'admin2@gmail.com', '$2y$10$JjzPFBcu3cHAXNi7WYOKYuFapewwDZfAcAOIIzUF7BmsezSMhhFfe', 'admin', '2025-12-31 22:44:39', '2025-12-31 23:06:43'),
(1017, 'Admin3', 'admin3@gmail.com', '$2y$10$dxr/3.xS8BiQ2Qum6IxarufTUq1x5s/daezzuQfOk2bowOCJCxSGS', 'admin', '2025-12-31 23:38:45', '2025-12-31 23:38:45'),
(1018, 'Admin4', 'admin4@gmail.com', '$2y$10$bWLO5fogMrucfC8BSZ1nK.entgEnZRBbFKQWTr3VVm8KgeBYqPuce', 'admin', '2025-12-31 23:39:55', '2025-12-31 23:39:55'),
(1019, 'Admin5', 'admin5@gmail.com', '$2y$10$qCRElX0lWUV5Sf0H6xfDS.zxFViueB32v5nsMosmJ4qXphbxlcSC2', 'admin', '2025-12-31 23:40:43', '2025-12-31 23:40:43'),
(1020, 'Aarav Sharma', 'aarav@gmail.com', '$2y$10$0Qd.YMDyRs5IAjW3Vigs.uNocI.jC/X1hd3GEyfZkkuqCsQKHNYgu', 'student', '2026-01-02 01:04:28', '2026-01-02 01:04:28'),
(1021, 'Ishita Verma', 'ishita@gmail.com', '$2y$10$5H3Quk6F1LPS8YkzNUHFheJkK8o00x5yDs1jwOwQREvVtFjArlanu', 'student', '2026-01-02 01:06:07', '2026-01-02 01:06:07'),
(1022, 'Rohan Malhotra', 'rohan@gmail.com', '$2y$10$vpiJf4O.h/zzUP6OAmDbpetc/8tda5DJGp9a5HjG3uwoyb3e1YobC', 'student', '2026-01-02 01:07:27', '2026-01-02 01:07:27'),
(1023, 'Meera Joshi', 'meera@gmail.com', '$2y$10$6uXJ8EZutRsTHwrnaJDCG.tWjvPSMhyOUaQNtFJ65t95cO2KeQEQe', 'student', '2026-01-02 01:08:07', '2026-01-02 01:08:07'),
(1024, 'Kunal Mehta', 'kunal@gmail.com', '$2y$10$n5JRUq8j7Biwmcse3tDOeee/T7VLz26hK7hv4KVfegQaI9Kx9N6lO', 'student', '2026-01-02 01:08:47', '2026-01-02 01:08:47'),
(1025, 'Sakshi Kulkarni', 'sakshi@gmail.com', '$2y$10$g3z1Xv7R07lzNB4.hgcPTOB5WOXstDsgOatIE4Vepkf2qWiXOAFZO', 'student', '2026-01-02 01:09:33', '2026-01-02 01:09:33'),
(1026, 'Aditya Nair', 'aditya@gmail.com', '$2y$10$slM9rYgstzVMm41fdzN7VOqJttIoAZ9Ks7RN1Ql/6nnkgNGMD7vIq', 'student', '2026-01-02 01:10:42', '2026-01-02 01:10:42'),
(1027, 'Pallavi Deshpande', 'pallavi@gmail.com', '$2y$10$182y9nTR./ecdayYnLncjO6tB8sn4e/mVdN54Ru55QmUoUIJ4kwia', 'student', '2026-01-02 01:11:36', '2026-01-02 01:11:36'),
(1028, 'Devansh Singh', 'devesh@gmail.com', '$2y$10$u6fOKxPXttSqGBTAc6Fevu0QumXMn58iAWBCPBssR4rqU7Lp8FjQu', 'student', '2026-01-02 01:12:29', '2026-01-02 01:12:29'),
(1029, 'Ritika Choudhary', 'ritika@gmail.com', '$2y$10$l6fX9UUMYnsBnx1il/30NOZ2/jEQSOoVzsJI0CygEl.7zep2Yxecm', 'student', '2026-01-02 01:13:18', '2026-01-02 01:13:18'),
(1030, 'Amit Kulkarni', 'amit@gmail.com', '$2y$10$9q/OVYpG5uRbbU0ova5m8upKns0/qtePQLcrpA0qeJwPLIz1846KK', 'alumni', '2026-01-02 01:16:56', '2026-01-02 01:16:56'),
(1031, 'Neha Iyer', 'neha@gmail.com', '$2y$10$h7mY1NOkI.0UGTiar8tqBue4G2I.PvtWAnX9D9ET0O2JgV6egel2m', 'alumni', '2026-01-02 01:18:44', '2026-01-02 01:18:44'),
(1032, 'Saurabh Mishra', 'saurabh@gmail.com', '$2y$10$vw8L3cSujGiV0nM8y0wCjOCSxUvh5L2eGfaqmmwXjnr83gmhnG4U.', 'alumni', '2026-01-02 01:19:53', '2026-01-02 01:19:53'),
(1033, 'Meera Sahu', 'meerasahu@gmail.com', '$2y$10$ZYf/Odq57/ps4oJxK3qvYePU0YCk5ewoBPDoPbv69E3H4dJ2Pasp6', 'alumni', '2026-01-02 01:22:16', '2026-01-02 01:22:16'),
(1034, 'Ritesh Desai', 'ritesh@gmail.com', '$2y$10$cecsdrkFVaqNU6t2sBpz8O442QTn0RAlLXZ9Hu8wj6HSDTvKVk8Iq', 'alumni', '2026-01-02 01:23:45', '2026-01-02 01:23:45'),
(1035, 'Devya Sahu', 'devya@gmail.com', '$2y$10$AHT.q3ZjojkkM3sgQHlNf.IsEkxQD4sgaLb/Tx99hH0ulKniJP6oO', 'student', '2026-09-27 03:15:36', '2026-09-27 03:15:36'),
(1036, 'Vedanta Sahu', 'vedanta@gmail.com', '$2y$10$1i4aXgHfAetII7wEJ7gOuemyjt5mq1p5QBUCEx2m2vo/4TVQfh3kG', 'alumni', '2026-09-27 03:16:37', '2026-09-27 03:16:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alumni_profile`
--
ALTER TABLE `alumni_profile`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_registrations`
--
ALTER TABLE `event_registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_event_user` (`event_id`,`user_id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `posted_by` (`posted_by`);

--
-- Indexes for table `student_profile`
--
ALTER TABLE `student_profile`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alumni_profile`
--
ALTER TABLE `alumni_profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `event_registrations`
--
ALTER TABLE `event_registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `student_profile`
--
ALTER TABLE `student_profile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1037;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `alumni_profile`
--
ALTER TABLE `alumni_profile`
  ADD CONSTRAINT `alumni_profile_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `event_registrations`
--
ALTER TABLE `event_registrations`
  ADD CONSTRAINT `event_registrations_event_fk` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `event_registrations_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_profile`
--
ALTER TABLE `student_profile`
  ADD CONSTRAINT `student_profile_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
