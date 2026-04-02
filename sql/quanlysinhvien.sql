-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 02, 2026 lúc 02:12 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `quanlysinhvien`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `message`, `submitted_at`) VALUES
(1, 'Tính Văn', 'tinhlu703@gmail.com', 'alo', '2026-03-31 12:19:07');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `course` varchar(100) DEFAULT NULL COMMENT 'Khóa học',
  `class_name` varchar(50) DEFAULT NULL COMMENT 'Tên lớp',
  `major` varchar(100) DEFAULT NULL COMMENT 'Ngành học'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `students`
--

INSERT INTO `students` (`id`, `name`, `email`, `phone`, `avatar`, `course`, `class_name`, `major`) VALUES
(1, 'Nguyen Van A', 'anv@example.com', '0901234567', NULL, NULL, NULL, NULL),
(2, 'Tran Thi B', 'btt@example.com', '0912345678', NULL, NULL, NULL, NULL),
(3, 'Le Van C', 'clv@example.com', '0987654321', NULL, NULL, NULL, NULL),
(4, 'tinhvan', 'tinhlu703@gmail.com', '0818177533', NULL, '17', 'Công nghệ thông tin 17B', 'Công nghệ thông tin'),
(6, 'Tính Văn', 'tinhlu703@gmail.com', '0913775566', NULL, NULL, NULL, NULL),
(8, 'Tính Văn', 'tinhlu703@tdu.vn', '0818177533', NULL, '17', 'Công nghệ thông tin 17B', 'Công nghệ thông tin'),
(9, 'Tính Văn', 'tinhlu703@tdu.edu.vn', '0818177533', 'avatar_69cbf7855c5da0.39578371.jpg', '17', 'Công nghệ thông tin 17B', 'Công nghệ thông tin');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `password`, `created_at`) VALUES
(1, 'Lữ Văn Tính', 'tinh@gmail.com', '', '$2y$10$P1m.VOlnrsswa2Ii8Q0Mgud39hAxjmM/o.Y/QbLYSDjyrQ8FJzI0i', '2026-03-22 03:25:15'),
(2, 'admin', 'admin@gmail.com', '', '$2y$10$O8ew12//fD7oH4D.nzQG1eCxlwJ6tuQW7E8ZNlYKhrt5R0Rsfgoyi', '2026-03-22 03:28:33'),
(3, 'admin', '123@gmail.com', 'tinhlu703@gmail.com', '$2y$10$NuRG5Y.LBHjFtPLxa38wC.8PcphXs5GJblVxa1pKgh3LrYj1VYBvG', '2026-04-02 12:09:19'),
(4, 'Tính Văn', '234@gmail.com', 'lvtinh-cntt17@tdu.edu.vn', '$2y$10$Wnqk0ZQ.KAjxaL0jIMtoDegUEpwgSYIO5DaCxqTSFujwXX4n2mJ7G', '2026-04-02 12:11:53');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
