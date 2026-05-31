-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql311.infinityfree.com
-- Generation Time: May 30, 2026 at 09:38 PM
-- Server version: 11.4.11-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_41815346_iac_covenant_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `guest_name` varchar(150) DEFAULT NULL,
  `activity_type` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `guest_name`, `activity_type`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(40, 1, NULL, 'login', 'User logged into the system', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-02 23:53:57'),
(41, 1, NULL, 'login', 'User logged into the system', '124.217.116.156', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 00:00:20'),
(42, 1, NULL, 'login', 'User logged into the system', '124.217.116.156', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-03 06:04:41'),
(43, 1, NULL, 'login', 'User logged into the system', '180.193.207.162', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-04 01:38:39'),
(44, NULL, 'Rhaymart Joe Gella', 'covenant_signed', 'Guest \'Rhaymart Joe Gella\' signed the covenant for \'Davao del Norte State College\'', '203.177.94.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', '2026-05-04 03:12:44'),
(45, NULL, 'test', 'covenant_signed', 'Guest \'test\' signed the covenant for \'test\'', '180.193.207.162', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-04 03:12:52'),
(46, 1, NULL, 'login', 'User logged into the system', '180.193.207.162', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-04 03:13:16'),
(47, 1, NULL, 'submission_management', 'Deleted covenant submission ID 2', '180.193.207.162', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-04 03:13:23'),
(48, NULL, 'LOUIE JAY LOSARIA', 'covenant_signed', 'Guest \'LOUIE JAY LOSARIA\' signed the covenant for \'DEPARTMENT OF MIGRANT WORKERS\'', '175.158.236.13', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.4 Mobile/15E148 Safari/604.1', '2026-05-04 03:13:28'),
(49, NULL, 'Mark Van Buladaco', 'covenant_signed', 'Guest \'Mark Van Buladaco\' signed the covenant for \'Davao del Norte State College\'', '180.193.207.162', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-04 03:15:09'),
(50, NULL, 'Marjohn Robillo', 'covenant_signed', 'Guest \'Marjohn Robillo\' signed the covenant for \'PropulseVA by Zappify.io\'', '103.173.110.34', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-04 03:16:31'),
(51, NULL, 'Jane M. Vitorillo', 'covenant_signed', 'Guest \'Jane M. Vitorillo\' signed the covenant for \'KUSINAUNIVERSITY ONLINE TUTORIAL SERVICES\'', '124.217.116.135', 'Mozilla/5.0 (Linux; U; Android 13; en-us; RMX3191 Build/SP1A.210812.016) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.5970.168 Mobile Safari/537.36 HeyTapBrowser/45.14.0.1', '2026-05-04 03:20:00'),
(52, NULL, 'Mark Mallari', 'covenant_signed', 'Guest \'Mark Mallari\' signed the covenant for \'Nehemiah Solutions Corp\'', '175.158.236.15', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Mobile Safari/537.36', '2026-05-04 03:20:19'),
(53, NULL, 'KEIR JAY SOLIS BALINGAN', 'covenant_signed', 'Guest \'KEIR JAY SOLIS BALINGAN\' signed the covenant for \'PESO PANABO\'', '49.145.215.230', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-04 03:20:42'),
(54, NULL, 'Christine S. Rabanes', 'covenant_signed', 'Guest \'Christine S. Rabanes\' signed the covenant for \'Holy Child College of Davao Del Norte\'', '180.193.207.162', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.4 Mobile/15E148 Safari/604.1', '2026-05-04 03:20:50'),
(55, NULL, 'Ma. Melanie N. Edig', 'covenant_signed', 'Guest \'Ma. Melanie N. Edig\' signed the covenant for \'Davao del Norte State College\'', '180.193.207.162', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Mobile/15E148 Safari/604.1', '2026-05-04 03:20:55'),
(56, NULL, 'Reban Cliff Fajardo', 'covenant_signed', 'Guest \'Reban Cliff Fajardo\' signed the covenant for \'DAVAO DEL NORTE STATE COLLEGE\'', '203.177.94.138', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3 Mobile/15E148 Safari/604.1', '2026-05-04 03:21:05'),
(57, 1, NULL, 'login', 'User logged into the system', '203.177.94.138', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Safari/605.1.15', '2026-05-04 03:21:31'),
(58, NULL, 'Reynante A. Castañares', 'covenant_signed', 'Guest \'Reynante A. Castañares\' signed the covenant for \'Davao Metro Shuttle Corporation\'', '136.239.186.53', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-04 03:23:22'),
(59, NULL, 'Dally Mae Poliquit', 'covenant_signed', 'Guest \'Dally Mae Poliquit\' signed the covenant for \'Davao del Norte State College\'', '203.177.94.138', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_7 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.2 Mobile/15E148 Safari/604.1', '2026-05-04 03:23:40'),
(60, NULL, 'Frency Vars Vallecera', 'covenant_signed', 'Guest \'Frency Vars Vallecera\' signed the covenant for \'Area 51 Information Technology Services\'', '180.193.207.162', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Mobile Safari/537.36', '2026-05-04 03:28:01'),
(61, NULL, 'Joyzel Odi', 'covenant_signed', 'Guest \'Joyzel Odi\' signed the covenant for \'PLGU Davao de Oro\'', '110.54.158.182', 'Mozilla/5.0 (Linux; Android 16; SM-S936B Build/BP2A.250605.031.A3; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/147.0.7727.112 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/559.0.0.49.75;]', '2026-05-04 03:29:29'),
(62, NULL, 'JULIE ANNE L. TADLE', 'covenant_signed', 'Guest \'JULIE ANNE L. TADLE\' signed the covenant for \'DNSC IC\'', '180.193.207.162', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.6 Safari/605.1.15', '2026-05-04 03:30:39'),
(63, NULL, 'Felix J Delfuso', 'covenant_signed', 'Guest \'Felix J Delfuso\' signed the covenant for \'Maryknoll College of Panabo, Inc,\'', '143.44.187.122', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0', '2026-05-04 03:31:58'),
(64, NULL, 'Jumar buladaco', 'covenant_signed', 'Guest \'Jumar buladaco\' signed the covenant for \'Dnsc\'', '203.177.94.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Mobile Safari/537.36', '2026-05-04 03:33:29'),
(65, NULL, 'Jonathan Cabrera', 'covenant_signed', 'Guest \'Jonathan Cabrera\' signed the covenant for \'Davao oriental state University\'', '120.72.25.186', 'Mozilla/5.0 (Linux; Android 16; DNY-NX9 Build/HONORDNY-N39; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/147.0.7727.111 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/559.0.0.49.75;]', '2026-05-04 03:39:20'),
(66, NULL, 'Mary Ann M. Palima', 'covenant_signed', 'Guest \'Mary Ann M. Palima\' signed the covenant for \'Davao Defi Community\'', '203.177.94.138', 'Mozilla/5.0 (Linux; Android 10; K) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Mobile Safari/537.36', '2026-05-04 03:41:44'),
(67, 1, NULL, 'login', 'User logged into the system', '180.193.207.162', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-04 03:53:31'),
(68, 1, NULL, 'login', 'User logged into the system', '203.177.94.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-04 04:09:11'),
(69, NULL, 'Dony Dongiapon', 'covenant_signed', 'Guest \'Dony Dongiapon\' signed the covenant for \'Davao Oriental State University\'', '120.72.25.186', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-04 04:20:54'),
(70, NULL, 'JHANREX PHILIP DELA PENA', 'covenant_signed', 'Guest \'JHANREX PHILIP DELA PENA\' signed the covenant for \'Davao Del Norte State College\'', '203.177.94.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-04 04:42:23'),
(71, NULL, 'JASPER COMELING', 'covenant_signed', 'Guest \'JASPER COMELING\' signed the covenant for \'DAVAO DEL NORTE STATE COLLEGE\'', '203.177.94.138', 'Mozilla/5.0 (Linux; Android 15; Infinix X6873 Build/AP3A.240905.015.A2; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/147.0.7727.111 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/558.0.0.46.77;]', '2026-05-04 04:51:39'),
(72, 1, NULL, 'submission_management', 'Deleted covenant submission ID 23', '203.177.94.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-04 05:02:50'),
(73, NULL, 'JASPER G. COMELING', 'covenant_signed', 'Guest \'JASPER G. COMELING\' signed the covenant for \'DAVAO DEL NORTE STATE COLLEGE\'', '203.177.94.138', 'Mozilla/5.0 (Linux; Android 15; Infinix X6873 Build/AP3A.240905.015.A2; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/147.0.7727.111 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/558.0.0.46.77;]', '2026-05-04 05:03:07'),
(74, NULL, 'HISULA NINO', 'covenant_signed', 'Guest \'HISULA NINO\' signed the covenant for \'Davao Del Norte State College\'', '203.177.94.138', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-04 05:08:08'),
(75, 1, NULL, 'login', 'User logged into the system', '124.217.116.156', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', '2026-05-04 05:57:38'),
(76, NULL, 'ELIZABETH O. BADILLES', 'covenant_signed', 'Guest \'ELIZABETH O. BADILLES\' signed the covenant for \'DICT-XI\'', '136.158.225.121', 'Mozilla/5.0 (Linux; Android 14; SM-A055F Build/UP1A.231005.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/146.0.7680.177 Mobile Safari/537.36 [FB_IAB/FB4A;FBAV/559.0.0.49.75;]', '2026-05-04 09:57:54'),
(77, 1, NULL, 'login', 'User logged into the system', '49.145.208.222', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36', '2026-05-28 10:49:48');

-- --------------------------------------------------------

--
-- Table structure for table `covenant_submissions`
--

CREATE TABLE `covenant_submissions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `organization_name` varchar(255) NOT NULL,
  `institution_type` varchar(100) NOT NULL,
  `represented_by` varchar(255) NOT NULL,
  `position_title` varchar(255) NOT NULL,
  `email_address` varchar(150) NOT NULL,
  `contact_number` varchar(50) DEFAULT NULL,
  `signature_file` varchar(255) NOT NULL,
  `pdf_file` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `signed_at` datetime DEFAULT current_timestamp(),
  `pos_top` float DEFAULT 0,
  `pos_left` float DEFAULT 0,
  `pos_rotation` float DEFAULT 0,
  `pos_scale` float DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `covenant_submissions`
--

INSERT INTO `covenant_submissions` (`id`, `user_id`, `organization_name`, `institution_type`, `represented_by`, `position_title`, `email_address`, `contact_number`, `signature_file`, `pdf_file`, `ip_address`, `signed_at`, `pos_top`, `pos_left`, `pos_rotation`, `pos_scale`) VALUES
(1, NULL, 'Davao del Norte State College', 'Higher Education Institution (HEI)', 'Rhaymart Joe Gella', 'Director for Administrative Services Division', 'rhaymartjoe.gella@dnsc.edu.ph', '09633643537', 'sign_guest_1777864363_RhaymartJoeGella.png', 'covenant_guest_1777864363_RhaymartJoeGella.pdf', '203.177.94.138', '2026-05-04 11:12:44', 25.5184, 50.9954, 11.9753, 0.97225),
(3, NULL, 'DEPARTMENT OF MIGRANT WORKERS', 'Others', 'LOUIE JAY LOSARIA', 'Chief', 'losaria.louiejay@gmail.com', '', 'sign_guest_1777864407_LOUIEJAYLOSARIA.png', 'covenant_guest_1777864407_LOUIEJAYLOSARIA.pdf', '175.158.236.13', '2026-05-04 11:13:28', 21.7037, 57.6825, -8.34365, 1.02982),
(4, NULL, 'Davao del Norte State College', 'Higher Education Institution (HEI)', 'Mark Van Buladaco', 'Dean', 'markvan.buladaco@dnsc.edu.ph', '', 'sign_guest_1777864508_MarkVanBuladaco.png', 'covenant_guest_1777864508_MarkVanBuladaco.pdf', '180.193.207.162', '2026-05-04 11:15:09', 48.704, 33.8484, 2.62551, 1.05724),
(5, NULL, 'PropulseVA by Zappify.io', 'Industry Partner', 'Marjohn Robillo', 'Founder', 'hello@marjohnrobillo.com', '', 'sign_guest_1777864591_MarjohnRobillo.png', 'covenant_guest_1777864591_MarjohnRobillo.pdf', '103.173.110.34', '2026-05-04 11:16:31', 52.9442, 77.4317, 6.95515, 1.14174),
(6, NULL, 'KUSINAUNIVERSITY ONLINE TUTORIAL SERVICES', 'Industry Partner', 'Jane M. Vitorillo', 'Owner', 'janevitorillo01@gmail.com', '09339598080', 'sign_guest_1777864799_JaneMVitorillo.png', 'covenant_guest_1777864799_JaneMVitorillo.pdf', '124.217.116.135', '2026-05-04 11:20:00', 31.6861, 27.8734, -8.66918, 1.10443),
(7, NULL, 'Nehemiah Solutions Corp', 'Others', 'Mark Mallari', 'Vision Keeper', 'nehemiahsolutionsmarketing@gmail.com', '09338765297', 'sign_guest_1777864818_MarkMallari.png', 'covenant_guest_1777864818_MarkMallari.pdf', '175.158.236.15', '2026-05-04 11:20:19', 12.1787, 52.5479, -10.1152, 1.0237),
(8, NULL, 'PESO PANABO', 'Industry Partner', 'KEIR JAY SOLIS BALINGAN', 'LABOR AND EMPLOYMENT OFFICER I', 'keirjaybalingan@gmail.com', '09482719758', 'sign_guest_1777864841_KEIRJAYSOLISBALINGAN.png', 'covenant_guest_1777864841_KEIRJAYSOLISBALINGAN.pdf', '49.145.215.230', '2026-05-04 11:20:42', 57.9515, 61.514, 9.38264, 0.901762),
(9, NULL, 'Holy Child College of Davao Del Norte', 'Industry Partner', 'Christine S. Rabanes', 'Trainer', 'simbulaschristine@gmail.com', '', 'sign_guest_1777864850_ChristineSRabanes.png', 'covenant_guest_1777864850_ChristineSRabanes.pdf', '180.193.207.162', '2026-05-04 11:20:50', 78.2503, 77.0498, -1.20422, 0.862964),
(10, NULL, 'Davao del Norte State College', 'Higher Education Institution (HEI)', 'Ma. Melanie N. Edig', 'Director for Quality Assurance Division', 'mamelanie.edig@dnsc.edu.ph', '09092058828', 'sign_guest_1777864855_MaMelanieNEdig.png', 'covenant_guest_1777864855_MaMelanieNEdig.pdf', '180.193.207.162', '2026-05-04 11:20:55', 49.7368, 50.3123, -7.40173, 1.03889),
(11, NULL, 'DAVAO DEL NORTE STATE COLLEGE', 'Higher Education Institution (HEI)', 'Reban Cliff Fajardo', 'BSIT Program Chairperson', 'rebancliff.fajardo@dnsc.edu.ph', '09632073788', 'sign_guest_1777864865_RebanCliffFajardo.png', 'covenant_guest_1777864865_RebanCliffFajardo.pdf', '203.177.94.138', '2026-05-04 11:21:05', 10.2989, 23.1599, -6.4899, 0.981142),
(12, NULL, 'Davao Metro Shuttle Corporation', 'Industry Partner', 'Reynante A. Castañares', 'IT Superintendent', 'reynantecastanares.dmsc@gmail.com', '09957400973', 'sign_guest_1777865001_ReynanteACastaares.png', 'covenant_guest_1777865001_ReynanteACastaares.pdf', '136.239.186.53', '2026-05-04 11:23:22', 24.6514, 29.4385, 7.46869, 1.10937),
(13, NULL, 'Davao del Norte State College', 'Higher Education Institution (HEI)', 'Dally Mae Poliquit', 'Planning Officer I', 'dallymae.poliquit@dnsc.edu.ph', '09668433370', 'sign_guest_1777865019_DallyMaePoliquit.png', 'covenant_guest_1777865019_DallyMaePoliquit.pdf', '203.177.94.138', '2026-05-04 11:23:40', 21.8136, 70.0602, 0.845035, 0.917556),
(14, NULL, 'Area 51 Information Technology Services', 'Industry Partner', 'Frency Vars Vallecera', 'Technical Head', 'vars.vallecera@area-51.ph', '', 'sign_guest_1777865281_FrencyVarsVallecera.png', 'covenant_guest_1777865281_FrencyVarsVallecera.pdf', '180.193.207.162', '2026-05-04 11:28:01', 80.5793, 50.014, -3.49465, 1.10904),
(15, NULL, 'PLGU Davao de Oro', 'Industry Partner', 'Joyzel Odi', 'Provincial ICT Officer', 'joyzelroxasodi@gmail.com', '09994722718', 'sign_guest_1777865368_JoyzelOdi.png', 'covenant_guest_1777865368_JoyzelOdi.pdf', '110.54.158.182', '2026-05-04 11:29:29', 45.3631, 38.0843, -2.37565, 1.08232),
(16, NULL, 'DNSC IC', 'Higher Education Institution (HEI)', 'JULIE ANNE L. TADLE', 'Instructor I/OJT Coordinator', 'jtadle@dnsc.edu.ph', '', 'sign_guest_1777865439_JULIEANNELTADLE.png', 'covenant_guest_1777865439_JULIEANNELTADLE.pdf', '180.193.207.162', '2026-05-04 11:30:39', 46.9049, 52.002, -10.5662, 0.912915),
(17, NULL, 'Maryknoll College of Panabo, Inc,', 'Industry Partner', 'Felix J Delfuso', 'IT Head', 'delfusofelix0@mcpi.edu.ph', '', 'sign_guest_1777865517_FelixJDelfuso.png', 'covenant_guest_1777865517_FelixJDelfuso.pdf', '143.44.187.122', '2026-05-04 11:31:58', 55.3441, 12.7432, 0.346702, 0.963703),
(18, NULL, 'Dnsc', 'Others', 'Jumar buladaco', 'Part-time teacher', 'Jumaw@dnsc.edu.ph', '09303066091', 'sign_guest_1777865609_Jumarbuladaco.png', 'covenant_guest_1777865609_Jumarbuladaco.pdf', '203.177.94.138', '2026-05-04 11:33:29', 66.8353, 67.618, -2.97836, 1.11092),
(19, NULL, 'Davao oriental state University', 'Higher Education Institution (HEI)', 'Jonathan Cabrera', 'Research Director', 'jonathan.cabrera@dorsu.edu.ph', '09169475352', 'sign_guest_1777865959_JonathanCabrera.png', 'covenant_guest_1777865959_JonathanCabrera.pdf', '120.72.25.186', '2026-05-04 11:39:20', 64.957, 8.69829, 8.73995, 0.93055),
(20, NULL, 'Davao Defi Community', 'Industry Partner', 'Mary Ann M. Palima', 'Community Event Lead', 'info.davaodefi@gmail.com', '', 'sign_guest_1777866103_MaryAnnMPalima.png', 'covenant_guest_1777866103_MaryAnnMPalima.pdf', '203.177.94.138', '2026-05-04 11:41:44', 38.1776, 48.1183, 6.64009, 0.978841),
(21, NULL, 'Davao Oriental State University', 'Higher Education Institution (HEI)', 'Dony Dongiapon', 'Program Head, BSIT', 'dony.dongiapon@dorsu.edu.ph', '09365826823', 'sign_guest_1777868453_DonyDongiapon.png', 'covenant_guest_1777868453_DonyDongiapon.pdf', '120.72.25.186', '2026-05-04 12:20:54', 79.8652, 39.0759, -11.3381, 1.03804),
(22, NULL, 'Davao Del Norte State College', 'Student', 'JHANREX PHILIP DELA PENA', 'OJT', 'delapena.jhanrexphilip@dnsc.edu.ph', '09567340309', 'sign_guest_1777869743_JHANREXPHILIPDELAPENA.png', 'covenant_guest_1777869743_JHANREXPHILIPDELAPENA.pdf', '203.177.94.138', '2026-05-04 12:42:23', 53.6984, 10.0016, 6.84799, 1.14746),
(24, NULL, 'DAVAO DEL NORTE STATE COLLEGE', 'Student', 'JASPER G. COMELING', 'Student', 'comeling.jasper@dnsc.edu.ph', '09569330331', 'sign_guest_1777870987_JASPERGCOMELING.png', 'covenant_guest_1777870987_JASPERGCOMELING.pdf', '203.177.94.138', '2026-05-04 13:03:07', 55.1907, 39.0646, 12.822, 0.925461),
(25, NULL, 'Davao Del Norte State College', 'Student', 'HISULA NINO', 'Student', 'hisula.nino@dnsc.edu.ph', '09559551352', 'sign_guest_1777871288_HISULANINO.png', 'covenant_guest_1777871288_HISULANINO.pdf', '203.177.94.138', '2026-05-04 13:08:08', 25.994, 46.9265, -3.78898, 1.01502),
(26, NULL, 'DICT-XI', 'Others', 'ELIZABETH O. BADILLES', 'ITO I', 'elizabeth.badilles@dict.gov.ph', '09949529239', 'sign_guest_1777888675_ELIZABETHOBADILLES.png', 'covenant_guest_1777888675_ELIZABETHOBADILLES.pdf', '136.158.225.121', '2026-05-04 17:57:54', 34.7476, 49.1351, 10.1068, 0.981768);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `signature_file` varchar(255) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `signature_file`, `role`, `status`, `created_at`) VALUES
(1, 'DNSC IC Admin', 'dnsciac@gmail.com', '$2y$10$oMJLMY5tucdeJPvWoA5OCOZbxfFeTtGby.hqa74HkXW33BLkTQ41u', 'admin_sig_placeholder.png', 'admin', 'active', '2026-05-02 23:52:11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_logs_ibfk_1` (`user_id`);

--
-- Indexes for table `covenant_submissions`
--
ALTER TABLE `covenant_submissions`
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
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `covenant_submissions`
--
ALTER TABLE `covenant_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `covenant_submissions`
--
ALTER TABLE `covenant_submissions`
  ADD CONSTRAINT `covenant_submissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
