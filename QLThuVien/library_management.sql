-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost
-- Thời gian đã tạo: Th10 19, 2024 lúc 09:49 PM
-- Phiên bản máy phục vụ: 5.7.34
-- Phiên bản PHP: 8.2.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `library_management`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `Authors`
--

CREATE TABLE `Authors` (
  `AuthorID` int(11) NOT NULL,
  `Name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Biography` text COLLATE utf8_unicode_ci,
  `Nationality` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `Authors`
--

INSERT INTO `Authors` (`AuthorID`, `Name`, `Biography`, `Nationality`) VALUES
(1, 'Nguyen Nhat Anh', 'One of the most famous authors of children\'s literature in Vietnam.', 'Vietnam'),
(2, 'Haruki Murakami', 'A Japanese author known for his mysterious and deep works.', 'Japan'),
(3, 'J.K. Rowling', 'The author of the famous Harry Potter series.', 'United Kingdom'),
(4, '唐家三少', 'A famous Chinese author known for his fantasy novels.', 'Chinese'),
(5, 'Gabriel Garcia Marquez', 'Colombian novelist known for his magical realism.', 'Colombia'),
(6, 'Mark Twain', 'Famous American author known for his novels about life along the Mississippi River.', 'USA'),
(7, 'George Orwell', 'English novelist known for his political allegories.', 'United Kingdom'),
(8, 'Leo Tolstoy', 'Russian author known for his epic novels, particularly \"War and Peace\".', 'Russia'),
(9, 'Jane Austen', 'English novelist known for her works on romantic fiction.', 'United Kingdom'),
(10, 'Agatha Christie', 'English writer known for her detective novels.', 'United Kingdom'),
(11, 'Stephen King', 'American author known for his horror and supernatural fiction.', 'USA'),
(12, 'Paulo Coelho', 'A Brazilian author known for his novel The Alchemist, which has sold millions of copies worldwide.', 'Brazil'),
(13, 'Dale Carnegie', 'An American writer and lecturer, Dale Carnegie is best known for his self-improvement, interpersonal skills, and public speaking courses, particularly his famous book \"How to Win Friends and Influence People\".', 'American');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `BookDamages`
--

CREATE TABLE `BookDamages` (
  `DamageID` int(11) NOT NULL,
  `BookID` int(11) DEFAULT NULL,
  `DamageType` enum('Lost','Damaged','Missing','Stolen','Destroyed') COLLATE utf8_unicode_ci DEFAULT 'Damaged',
  `Description` text COLLATE utf8_unicode_ci,
  `ReportedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `BookDamages`
--

INSERT INTO `BookDamages` (`DamageID`, `BookID`, `DamageType`, `Description`, `ReportedAt`) VALUES
(1, 1, 'Damaged', 'The cover of the book is torn.', '2024-10-08 15:42:19'),
(2, 2, 'Lost', 'The book is missing.', '2024-10-08 15:42:19'),
(3, 1, 'Damaged', 'The book is wet.', '2024-10-08 15:46:02'),
(4, 2, 'Lost', 'The book is missing.', '2024-10-08 15:46:02'),
(5, 3, 'Damaged', 'Pages are wrinkled and stained.', '2024-10-08 15:46:02'),
(6, 4, 'Lost', 'Not returned after 30 days.', '2024-10-08 15:46:02'),
(7, 5, 'Damaged', 'Spine is broken.', '2024-10-08 15:46:02'),
(8, 6, 'Lost', 'Cannot locate in the inventory.', '2024-10-08 15:46:02'),
(9, 7, 'Damaged', 'Water damage on several pages.', '2024-10-08 15:46:02'),
(10, 8, 'Damaged', 'Highlights and notes on every page.', '2024-10-08 15:46:02'),
(11, 9, 'Lost', 'Reported missing by the reader.', '2024-10-08 15:46:02'),
(12, 10, 'Damaged', 'Torn pages and markings throughout the book.', '2024-10-08 15:46:02'),
(13, 10, 'Destroyed', 'My Dog Eats My Book', '2024-10-08 16:54:15');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `Books`
--

CREATE TABLE `Books` (
  `BookID` int(11) NOT NULL,
  `Title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Language` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Publisher` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Quantity` int(11) DEFAULT NULL,
  `ISBN` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Status` enum('Available','Unavailable','Reserved','Pending') COLLATE utf8_unicode_ci DEFAULT 'Available',
  `AuthorID` int(11) DEFAULT NULL,
  `SupplierID` int(11) DEFAULT NULL,
  `GenreID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `Books`
--

INSERT INTO `Books` (`BookID`, `Title`, `Language`, `Publisher`, `Quantity`, `ISBN`, `Status`, `AuthorID`, `SupplierID`, `GenreID`) VALUES
(1, 'The Secret Garden', 'Vietnamese', 'Tre Publishing House', 49, '978-3-16-148410-0', 'Available', 1, 1, 1),
(2, 'Animal Farm', 'English', 'Secker & Warburg', 15, '978-0-452-28424-1', 'Available', 7, 1, 1),
(3, 'Go Set a Watchman', 'English', 'HarperCollins', 9, '978-0-06-240985-0', 'Available', 2, 2, 1),
(4, 'The Secret Garden 2', 'Vietnamese', 'Tre Publishing House', 100, '978-3-16-148410-0', 'Available', 1, 1, 1),
(5, 'Harry Potter and the Chamber of Secrets', 'English', 'Bloomsbury', 20, '978-0-7475-3849-9', 'Available', 3, 2, 4),
(6, 'Harry Potter and the Chammber of Secrets', 'Vietnamese', 'Kim Dong Publishing House', 8, '978-0-545-01022-1', 'Available', 3, 2, 5),
(7, 'One Hundred Years of Solitude', 'Vietnamese', 'NXB Phuong Nam', 10, '978-0-06-088328-7', 'Available', 4, 3, NULL),
(8, 'The Adventures of Tom Sawyer', 'Vietnamese', 'House of Books', 15, '978-0-141-03490-3', 'Available', 5, NULL, NULL),
(10, 'War and Peace', 'Vietnamese', 'First News', 6, '978-0-14-303999-0', 'Available', 7, 5, 3),
(11, 'The Great Gatsby', 'English', 'Scribner', 10, '978-0-7432-7356-6', 'Available', 8, 1, 1),
(12, 'Brave New World', 'English', 'Chatto & Windus', 50, '978-0-06-085052-4', 'Available', 6, 2, 5),
(13, 'The Alchemist', 'English', 'HarperCollins', 20, '978-0-06-231500-7', 'Available', 12, 2, 4),
(14, 'The Great Gatsby', 'English', 'Scribner', 9, '978-0743273565', 'Available', 1, NULL, NULL),
(15, 'To Kill a Mockingbird', 'English', 'J.B. Lippincott & Co.', 5, '978-0061120084', 'Available', 2, NULL, NULL),
(16, '1984', 'English', 'Secker & Warburg', 8, '978-0451524935', 'Available', 3, NULL, NULL),
(17, 'Pride and Prejudice', 'English', 'CreateSpace Independent Publishing Platform', 4, '978-1503290563', 'Available', 4, NULL, NULL),
(18, 'Moby-Dick', 'English', 'CreateSpace Independent Publishing Platform', 15, '978-1503280786', 'Available', 5, NULL, NULL),
(19, 'War and Peace', 'English', 'Digireads.com Publishing', 12, '978-1420954304', 'Available', 6, NULL, NULL),
(20, 'The Catcher in the Rye', 'English', 'Little, Brown and Company', 7, '978-0316769488', 'Available', 7, NULL, NULL),
(21, 'Brave New World', 'English', 'HarperCollins', 6, '978-0060850524', 'Available', 8, NULL, NULL),
(22, 'The Picture of Dorian Gray', 'English', 'CreateSpace Independent Publishing Platform', 5, '978-1515195038', 'Available', 9, NULL, NULL),
(23, 'Fahrenheit 451', 'English', 'Simon & Schuster', 10, '978-1451673319', 'Available', 10, NULL, NULL),
(24, 'Animal Farm', 'English', 'Secker & Warburg', 12, '978-0451526340', 'Available', 11, NULL, NULL),
(25, '1984', 'English', 'Secker & Warburg', 8, '978-0451524935', 'Available', 12, NULL, NULL),
(26, 'The Great Gatsby', 'English', 'Scribner', 24, '978-0743273565', 'Available', 13, NULL, NULL),
(27, 'Moby Dick', 'English', 'CreateSpace Independent Publishing Platform', 3, '978-1503280786', 'Available', 14, NULL, NULL),
(28, 'To Kill a Mockingbird', 'English', 'HarperCollins', 6, '978-0061120084', 'Available', 15, NULL, NULL),
(29, 'War and Peace', 'English', 'Independently published', 2, '978-1420952460', 'Available', 16, NULL, NULL);

--
-- Bẫy `Books`
--
DELIMITER $$
CREATE TRIGGER `DeleteBookFromInventory` BEFORE DELETE ON `Books` FOR EACH ROW BEGIN
    -- Xóa sách khỏi bảng Inventory trước khi xóa sách
    DELETE FROM Inventory
    WHERE BookID = OLD.BookID;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `InsertInventoryOnBookInsert` AFTER INSERT ON `Books` FOR EACH ROW BEGIN
    -- Thêm bản ghi mới vào bảng Inventory cho sách mới
    INSERT INTO Inventory (BookID, QuantityInStock, ReorderLevel, LastUpdated)
    VALUES (NEW.BookID, NEW.Quantity, 10, NOW());
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `UpdateInventoryOnBookChange` AFTER INSERT ON `Books` FOR EACH ROW BEGIN
    UPDATE Inventory
    SET QuantityInStock = (
        SELECT COALESCE(SUM(B.Quantity), 0) + COALESCE(SUM(L.Quantity), 0)
        FROM Books B
        LEFT JOIN (
            SELECT BookID, COUNT(*) AS Quantity
            FROM Loans
            WHERE ReturnDate IS NULL
            GROUP BY BookID
        ) L ON B.BookID = L.BookID
        WHERE Inventory.BookID = B.BookID
    )
    WHERE Inventory.BookID = NEW.BookID;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `UpdateInventoryOnBookEdit` AFTER UPDATE ON `Books` FOR EACH ROW BEGIN
    -- Cập nhật số lượng trong bảng Inventory
    UPDATE Inventory
    SET QuantityInStock = QuantityInStock + (NEW.Quantity - OLD.Quantity)
    WHERE BookID = NEW.BookID;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `UpdateInventoryOnBookQuantityChange` AFTER UPDATE ON `Books` FOR EACH ROW BEGIN
    -- Cập nhật số lượng trong bảng Inventory
    UPDATE Inventory
    SET QuantityInStock = QuantityInStock + (NEW.Quantity - OLD.Quantity), 
        LastUpdated = NOW()
    WHERE BookID = NEW.BookID;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `UpdateInventoryOnBookUpdate` AFTER UPDATE ON `Books` FOR EACH ROW BEGIN
    -- Cập nhật số lượng trong bảng Inventory
    UPDATE Inventory
    SET QuantityInStock = NEW.Quantity
    WHERE BookID = NEW.BookID;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `Genre`
--

CREATE TABLE `Genre` (
  `GenreID` int(11) NOT NULL,
  `GenreName` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Description` text COLLATE utf8_unicode_ci
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `Genre`
--

INSERT INTO `Genre` (`GenreID`, `GenreName`, `Description`) VALUES
(1, 'Fiction', 'Literary works created from the imagination.'),
(2, 'Non-Fiction', 'Factual and informative texts based on reality.'),
(3, 'Mystery', 'Fictional works involving a crime or puzzle to be solved.'),
(4, 'Fantasy', 'Fiction including magical elements and imaginary worlds.'),
(5, 'Science Fiction', 'Fiction based on futuristic concepts and technology.'),
(6, 'Romance', 'Literary works focusing on romantic relationships.'),
(7, 'Horror', 'Fiction intended to frighten or disturb readers.'),
(8, 'Biography', 'Non-fiction texts detailing a person\'s life.'),
(9, 'Self-Help', 'Books designed to help readers improve their lives.'),
(10, 'Historical Fiction', 'Fiction taking place in the past, often with real historical events.');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `Inventory`
--

CREATE TABLE `Inventory` (
  `InventoryID` int(11) NOT NULL,
  `BookID` int(11) DEFAULT NULL,
  `QuantityInStock` int(11) DEFAULT NULL,
  `ReorderLevel` int(11) DEFAULT NULL,
  `LastUpdated` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `Inventory`
--

INSERT INTO `Inventory` (`InventoryID`, `BookID`, `QuantityInStock`, `ReorderLevel`, `LastUpdated`) VALUES
(59, 26, 44, 5, '2024-11-19 21:31:37'),
(58, 25, 8, 10, '2024-10-17 00:00:00'),
(57, 24, 12, 10, '2024-10-17 00:00:00'),
(56, 23, 11, 10, '2024-10-17 00:00:00'),
(55, 22, 7, 10, '2024-10-17 00:00:00'),
(54, 21, 6, 10, '2024-10-17 00:00:00'),
(53, 20, 7, 10, '2024-10-17 00:00:00'),
(52, 19, 12, 10, '2024-10-17 00:00:00'),
(51, 18, 15, 10, '2024-10-17 00:00:00'),
(50, 17, 4, 10, '2024-10-17 00:00:00'),
(49, 16, 8, 10, '2024-10-17 00:00:00'),
(48, 15, 5, 10, '2024-10-17 00:00:00'),
(47, 14, 10, 10, '2024-10-18 23:21:13'),
(46, 13, 21, 10, '2024-10-17 00:00:00'),
(45, 12, 50, 10, '2024-10-17 00:00:00'),
(44, 11, 10, 10, '2024-10-17 00:00:00'),
(43, 10, 7, 10, '2024-10-17 00:00:00'),
(41, 8, 15, 10, '2024-10-17 00:00:00'),
(40, 7, 11, 10, '2024-10-17 00:00:00'),
(4, 6, 10, 9, '2024-10-29 02:50:24'),
(5, 5, 21, 9, '2024-10-13 02:52:51'),
(60, 1, 51, 10, '2024-10-18 23:51:58'),
(1, 4, 100, 10, '2024-11-19 21:44:06'),
(39, 3, 11, NULL, '2024-10-18 23:51:58'),
(38, 2, 20, 5, '2024-11-19 21:40:43');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `Inventory_Transactions`
--

CREATE TABLE `Inventory_Transactions` (
  `TransactionID` int(11) NOT NULL,
  `BookID` int(11) DEFAULT NULL,
  `Quantity` int(11) DEFAULT NULL,
  `TransactionType` enum('Import','Export') COLLATE utf8_unicode_ci DEFAULT NULL,
  `TransactionDate` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `Inventory_Transactions`
--

INSERT INTO `Inventory_Transactions` (`TransactionID`, `BookID`, `Quantity`, `TransactionType`, `TransactionDate`) VALUES
(1, 1, 30, 'Import', '2024-11-20 00:00:00');

--
-- Bẫy `Inventory_Transactions`
--
DELIMITER $$
CREATE TRIGGER `UpdateBookQuantity` AFTER INSERT ON `Inventory_Transactions` FOR EACH ROW BEGIN
    IF NEW.TransactionType = 'Export' THEN
        UPDATE Books
        SET Quantity = Quantity - NEW.Quantity
        WHERE BookID = NEW.BookID;
    ELSEIF NEW.TransactionType = 'Import' THEN
        UPDATE Books
        SET Quantity = Quantity + NEW.Quantity
        WHERE BookID = NEW.BookID;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_after_transaction_insert` AFTER INSERT ON `Inventory_Transactions` FOR EACH ROW BEGIN
    IF NEW.TransactionType = 'Import' THEN
        UPDATE Inventory
        SET QuantityInStock = QuantityInStock + NEW.Quantity,
            LastUpdated = NOW()
        WHERE BookID = NEW.BookID;
    ELSEIF NEW.TransactionType = 'Export' THEN
        UPDATE Inventory
        SET QuantityInStock = QuantityInStock - NEW.Quantity,
            LastUpdated = NOW()
        WHERE BookID = NEW.BookID;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `LibraryCards`
--

CREATE TABLE `LibraryCards` (
  `CardID` int(11) NOT NULL,
  `ReaderID` int(11) DEFAULT NULL,
  `CardRank` enum('Basic','Silver','Gold','Platinum') COLLATE utf8_unicode_ci DEFAULT 'Basic',
  `IssueDate` date DEFAULT NULL,
  `ExpiryDate` date DEFAULT NULL,
  `Duration` int(11) DEFAULT '12',
  `Status` enum('Active','Expired') COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `LibraryCards`
--

INSERT INTO `LibraryCards` (`CardID`, `ReaderID`, `CardRank`, `IssueDate`, `ExpiryDate`, `Duration`, `Status`) VALUES
(1, 101, 'Gold', '2024-10-01', '2025-10-01', 12, 'Active'),
(2, 102, 'Basic', '2024-10-02', '2025-10-02', 12, 'Active'),
(3, 103, 'Basic', '2024-10-03', '2025-10-03', 12, 'Active'),
(4, 104, 'Platinum', '2024-10-04', '2025-10-04', 12, 'Active'),
(5, 105, 'Basic', '2024-10-05', '2025-10-05', 12, 'Active'),
(6, 106, 'Basic', '2024-10-06', '2025-10-06', 12, 'Active'),
(7, 107, 'Basic', '2024-10-07', '2025-10-07', 12, 'Active'),
(8, 108, 'Basic', '2024-10-08', '2025-10-08', 12, 'Active'),
(9, 109, 'Silver', '2024-10-09', '2025-10-09', 12, 'Expired'),
(10, 110, 'Basic', '2024-10-10', '2025-10-10', 12, 'Expired'),
(120, 1, 'Platinum', '2024-10-16', NULL, 12, 'Active');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `Loans`
--

CREATE TABLE `Loans` (
  `LoanID` int(11) NOT NULL,
  `ReaderID` int(11) DEFAULT NULL,
  `BookID` int(11) DEFAULT NULL,
  `LoanDate` datetime DEFAULT NULL,
  `ReturnDate` datetime DEFAULT NULL,
  `DueDate` datetime DEFAULT NULL,
  `Status` enum('Active','Returned','Overdue') COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `Loans`
--

INSERT INTO `Loans` (`LoanID`, `ReaderID`, `BookID`, `LoanDate`, `ReturnDate`, `DueDate`, `Status`) VALUES
(1, 1, 1, '2024-10-01 10:00:00', '2024-10-17 02:33:26', '2024-10-15 10:00:00', 'Active'),
(2, 1, 2, '2024-10-06 10:00:00', '2024-10-19 00:16:44', '2024-10-16 10:00:00', 'Active'),
(3, 2, 3, '2024-10-03 10:00:00', '2024-10-17 01:55:14', '2024-10-14 10:00:00', 'Active'),
(4, 1, 1, '2024-10-01 10:00:00', '2024-10-16 23:02:48', '2024-10-15 10:00:00', 'Active'),
(5, 1, 2, '2024-10-02 10:00:00', '2024-10-16 23:45:07', '2024-10-16 10:00:00', 'Active'),
(6, 2, 3, '2024-10-03 10:00:00', '2024-10-17 00:20:21', '2024-10-17 10:00:00', 'Active'),
(7, 3, 4, '2024-10-04 10:00:00', '2024-10-17 00:53:13', NULL, 'Active'),
(8, 4, 5, '2024-10-05 10:00:00', '2024-10-17 00:20:25', '2024-10-19 10:00:00', 'Active'),
(9, 5, 6, '2024-10-06 10:00:00', '2024-10-13 00:41:03', NULL, 'Active'),
(10, 6, 7, '2024-10-07 10:00:00', '2024-10-17 00:23:21', '2024-10-21 10:00:00', 'Active'),
(11, 7, 8, '2024-10-08 10:00:00', '2024-10-17 00:33:24', '2024-10-22 10:00:00', 'Active'),
(12, 8, 9, '2024-10-09 10:00:00', '2024-10-17 00:23:33', '2024-10-23 10:00:00', 'Active'),
(13, 9, 10, '2024-10-10 10:00:00', '2024-10-17 00:30:41', '2024-10-24 10:00:00', 'Active'),
(14, 10, 1, '2024-10-11 10:00:00', '2024-10-17 00:44:15', '2024-10-25 10:00:00', 'Active'),
(15, 1, 2, '2024-09-01 20:45:03', '2024-10-16 21:56:44', '2024-10-10 20:45:03', 'Active'),
(16, 3, 4, '2024-10-11 21:54:08', '2024-10-14 21:22:07', '2024-11-10 21:54:08', NULL),
(17, 3, 5, '2024-10-11 21:54:08', '2024-10-17 00:23:37', '2024-11-10 21:54:08', NULL),
(18, 3, 10, '2024-10-11 21:56:13', '2024-10-17 01:10:36', '2024-11-10 21:56:13', NULL),
(19, 3, 11, '2024-10-11 21:56:13', '2024-10-17 01:10:48', '2024-11-10 21:56:13', NULL),
(20, 3, 3, '2024-10-11 21:58:36', '2024-10-17 01:20:44', '2024-11-10 21:58:36', NULL),
(21, 3, 4, '2024-10-11 21:58:36', '2024-10-17 01:26:28', '2024-11-10 21:58:36', NULL),
(22, 7, 29, '2024-10-12 00:23:52', '2024-10-17 01:20:55', '2024-11-11 00:23:52', NULL),
(23, 101, 5, '2024-10-12 00:39:29', '2024-10-17 01:26:36', '2024-12-11 00:39:29', NULL),
(24, 101, 6, '2024-10-12 00:39:29', '2024-10-17 01:37:24', '2024-12-11 00:39:29', NULL),
(25, 101, 7, '2024-10-12 00:39:29', '2024-10-17 01:55:20', '2024-12-11 00:39:29', NULL),
(26, 101, 10, '2024-10-12 00:39:29', '2024-10-17 01:37:28', '2024-12-11 00:39:29', NULL),
(27, 102, 6, '2024-10-12 00:39:55', NULL, '2024-11-11 00:39:55', NULL),
(28, 102, 10, '2024-10-12 00:39:55', NULL, '2024-11-11 00:39:55', NULL),
(29, 105, 7, '2024-10-12 00:47:37', NULL, '2024-11-11 00:47:37', NULL),
(30, 101, 3, '2024-10-12 23:51:20', '2024-10-17 01:37:32', '2024-12-11 23:51:20', NULL),
(31, 11, 1, '2024-10-14 20:41:08', NULL, '2024-11-13 20:41:08', NULL),
(32, 11, 2, '2024-10-14 20:41:08', NULL, '2024-11-13 20:41:08', NULL),
(33, 12, 2, '2024-10-14 20:42:25', NULL, '2024-11-13 20:42:25', NULL),
(34, 103, 5, '2024-10-14 20:51:29', NULL, '2024-11-13 20:51:29', NULL),
(35, 103, 6, '2024-10-14 20:51:29', NULL, '2024-11-13 20:51:29', NULL),
(36, 104, 13, '2024-10-14 21:18:21', NULL, '2025-01-12 21:18:21', NULL),
(37, 104, 16, '2024-10-14 21:18:21', '2024-10-14 21:21:18', '2025-01-12 21:18:21', NULL),
(38, 106, 22, '2024-10-16 21:57:46', NULL, '2024-11-15 21:57:46', NULL),
(39, 106, 23, '2024-10-16 21:57:46', NULL, '2024-11-15 21:57:46', NULL),
(42, 1, 13, '2024-10-16 23:57:18', '2024-10-17 01:21:03', '2025-01-14 23:57:18', NULL),
(43, 1, 14, '2024-10-18 23:21:13', NULL, '2025-01-16 23:21:13', NULL),
(44, 1, 1, '2024-10-18 23:51:58', NULL, '2025-01-16 23:51:58', NULL),
(45, 1, 3, '2024-10-18 23:51:58', NULL, '2025-01-16 23:51:58', NULL);

--
-- Bẫy `Loans`
--
DELIMITER $$
CREATE TRIGGER `ReduceBookQuantityOnLoan` AFTER INSERT ON `Loans` FOR EACH ROW BEGIN
    -- Giảm số lượng sách trong bảng Books tương ứng với BookID trong Loans
    UPDATE Books
    SET Quantity = Quantity - 1
    WHERE BookID = NEW.BookID;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `UpdateInventoryOnLoanChange` AFTER INSERT ON `Loans` FOR EACH ROW BEGIN
    UPDATE Inventory
    SET QuantityInStock = (
        SELECT COALESCE(SUM(B.Quantity), 0) + COALESCE(SUM(L.Quantity), 0)
        FROM Books B
        LEFT JOIN (
            SELECT BookID, COUNT(*) AS Quantity
            FROM Loans
            WHERE ReturnDate IS NULL
            GROUP BY BookID
        ) L ON B.BookID = L.BookID
        WHERE Inventory.BookID = B.BookID
    )
    WHERE Inventory.BookID = NEW.BookID;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `Prices`
--

CREATE TABLE `Prices` (
  `PriceID` int(11) NOT NULL,
  `BookID` int(11) NOT NULL,
  `Title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `Prices`
--

INSERT INTO `Prices` (`PriceID`, `BookID`, `Title`, `Name`, `Price`) VALUES
(3, 6, 'Harry Potter and the Chammber of Secrets', 'J.K. Rowling', 75000.00),
(4, 4, 'The Secret Garden 2', 'Nguyen Nhat Anh', 100000.00),
(5, 8, 'The Adventures of Tom Sawyer', 'Gabriel Garcia Marquez', 90000.00);

--
-- Bẫy `Prices`
--
DELIMITER $$
CREATE TRIGGER `before_insert_prices` BEFORE INSERT ON `Prices` FOR EACH ROW BEGIN
    DECLARE author_name VARCHAR(100);
    
    -- Lấy tên tác giả từ bảng Authors dựa vào BookID
    SELECT a.Name INTO author_name
    FROM Authors a
    JOIN Books b ON b.AuthorID = a.AuthorID
    WHERE b.BookID = NEW.BookID;

    -- Cập nhật tên sách và tác giả vào bảng Prices
    SET NEW.Title = (SELECT Title FROM Books WHERE BookID = NEW.BookID);
    SET NEW.Name = author_name;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `Readers`
--

CREATE TABLE `Readers` (
  `ReaderID` int(11) NOT NULL,
  `Name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ReaderType` enum('Student','Staff','Other') COLLATE utf8_unicode_ci DEFAULT NULL,
  `Phone` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Email` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Status` int(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `Readers`
--

INSERT INTO `Readers` (`ReaderID`, `Name`, `ReaderType`, `Phone`, `Email`, `Address`, `Status`) VALUES
(1, 'John Doe', 'Student', '0123456789', 'john.doe@example.com', '123 Main St, City A', 0),
(2, 'Jane Smith', '', '0987654321', 'jane.smith@example.com', '456 Elm St, City B', 0),
(3, 'Alice Johnson', 'Student', '0112233445', 'alice.johnson@example.com', '789 Oak St, City C', 0),
(4, 'Bob Brown', 'Other', '0223344556', 'bob.brown@example.com', '321 Pine St, City D', 1),
(5, 'Charlie Davis', 'Student', '0334455667', 'charlie.davis@example.com', '654 Maple St, City E', 0),
(6, 'Eva White', '', '0445566778', 'eva.white@example.com', '987 Birch St, City F', 0),
(7, 'Grace Green', 'Student', '0556677889', 'grace.green@example.com', '246 Cedar St, City G', 0),
(8, 'Henry Black', 'Other', '0667788990', 'henry.black@example.com', '135 Spruce St, City H', 0),
(9, 'Isabella Blue', '', '0778899001', 'isabella.blue@example.com', '579 Fir St, City I', 1),
(10, 'Jack Red', 'Student', '0889900112', 'jack.red@example.com', '369 Walnut St, City J', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `Reports`
--

CREATE TABLE `Reports` (
  `ReportID` int(11) NOT NULL,
  `ReportType` enum('Monthly Summary','Lost Books Report','Damaged Books Report','New Arrivals','Overdue Books Report') COLLATE utf8_unicode_ci DEFAULT 'Monthly Summary',
  `ReportData` text COLLATE utf8_unicode_ci,
  `CreatedAt` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `Reports`
--

INSERT INTO `Reports` (`ReportID`, `ReportType`, `ReportData`, `CreatedAt`) VALUES
(1, 'Monthly Summary', 'Total books borrowed: 120, Total books returned: 115', '2024-10-07 17:00:00'),
(2, 'Lost Books Report', 'Lost books: 3, Titles: Book A, Book B, Book C', '2024-10-07 17:00:00'),
(3, 'Damaged Books Report', 'Damaged books: 5, Titles: Book D, Book E', '2024-10-07 17:00:00'),
(4, 'New Arrivals', 'New books added this month: 20, Genres: Fiction, Non-fiction', '2024-10-07 17:00:00'),
(5, 'Overdue Books Report', 'Overdue books: 10, Readers: Reader A, Reader B', '2024-10-07 17:00:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `Suppliers`
--

CREATE TABLE `Suppliers` (
  `SupplierID` int(11) NOT NULL,
  `Name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ContactPerson` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Phone` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Email` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `Suppliers`
--

INSERT INTO `Suppliers` (`SupplierID`, `Name`, `ContactPerson`, `Phone`, `Email`, `Address`) VALUES
(1, 'Tre Publishing House', 'Nguyen Van A', '0123456789', 'info@trepublishing.com', '123 ABC Street, District 1, HCM City'),
(2, 'Kim Dong Publishing House', 'Tran Thi B', '0987654321', 'contact@kimdong.com', '456 DEF Street, District 2, HCM City'),
(3, 'Tre Publishing House', 'Nguyen Van A', '0123456789', 'info@trepublishing.com', '123 ABC Street, District 1, HCM City'),
(4, 'Kim Dong Publishing House', 'Tran Thi B', '0987654321', 'contact@kimdong.com', '456 DEF Street, District 2, HCM City'),
(5, 'NXB Phuong Nam', 'Le Van C', '0112233445', 'info@phuongnam.com', '789 GHI Street, District 3, HCM City'),
(6, 'House of Books', 'Nguyen Thi D', '0223344556', 'contact@houseofbooks.com', '321 JKL Street, District 4, HCM City'),
(7, 'Alpha Books', 'Tran Van E', '0334455667', 'info@alphabooks.com', '654 MNO Street, District 5, HCM City'),
(8, 'First News', 'Hoang Van F', '0445566778', 'contact@firstnews.com', '987 PQR Street, District 6, HCM City'),
(9, 'Huy Hoang Publishing', 'Phan Van G', '0556677889', 'info@huyhoang.com', '159 STU Street, District 7, HCM City'),
(10, 'Dinh Tien Publishing', 'Nguyen Van H', '0667788990', 'contact@dinhthien.com', '753 VWX Street, District 8, HCM City'),
(11, 'Van Hoa Publishing', 'Le Thi I', '0778899001', 'info@vanhoa.com', '159 YZ Street, District 9, HCM City'),
(12, 'Lao Dong Publishing', 'Tran Van J', '0889900112', 'contact@laodong.com', '258 ABC Street, District 10, HCM City');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `Users`
--

CREATE TABLE `Users` (
  `UserID` int(11) NOT NULL,
  `Username` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `Password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `FullName` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Role` enum('Admin','Librarian','User') COLLATE utf8_unicode_ci DEFAULT 'User'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `Users`
--

INSERT INTO `Users` (`UserID`, `Username`, `Password`, `FullName`, `Role`) VALUES
(5, 'admin', '$2y$10$ZEQ/GB5gaMdZ5QOG/OO3nOnu94OLBrh2.3BZmH5.hLJ1UsS3Qq0vS', 'Ngân', 'User'),
(4, 'ngan', '$2y$10$a6AosLgD2goZn8o3Vw4Cw.qjRY4XQgO6ve4q8WGs/UpLF959/tp4C', NULL, 'User');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `Authors`
--
ALTER TABLE `Authors`
  ADD PRIMARY KEY (`AuthorID`);

--
-- Chỉ mục cho bảng `BookDamages`
--
ALTER TABLE `BookDamages`
  ADD PRIMARY KEY (`DamageID`),
  ADD KEY `BookID` (`BookID`);

--
-- Chỉ mục cho bảng `Books`
--
ALTER TABLE `Books`
  ADD PRIMARY KEY (`BookID`),
  ADD KEY `AuthorID` (`AuthorID`),
  ADD KEY `SupplierID` (`SupplierID`);

--
-- Chỉ mục cho bảng `Genre`
--
ALTER TABLE `Genre`
  ADD PRIMARY KEY (`GenreID`);

--
-- Chỉ mục cho bảng `Inventory`
--
ALTER TABLE `Inventory`
  ADD PRIMARY KEY (`InventoryID`),
  ADD KEY `BookID` (`BookID`);

--
-- Chỉ mục cho bảng `Inventory_Transactions`
--
ALTER TABLE `Inventory_Transactions`
  ADD PRIMARY KEY (`TransactionID`),
  ADD KEY `BookID` (`BookID`);

--
-- Chỉ mục cho bảng `LibraryCards`
--
ALTER TABLE `LibraryCards`
  ADD PRIMARY KEY (`CardID`),
  ADD KEY `ReaderID` (`ReaderID`);

--
-- Chỉ mục cho bảng `Loans`
--
ALTER TABLE `Loans`
  ADD PRIMARY KEY (`LoanID`),
  ADD KEY `ReaderID` (`ReaderID`),
  ADD KEY `BookID` (`BookID`);

--
-- Chỉ mục cho bảng `Prices`
--
ALTER TABLE `Prices`
  ADD PRIMARY KEY (`PriceID`),
  ADD KEY `BookID` (`BookID`);

--
-- Chỉ mục cho bảng `Readers`
--
ALTER TABLE `Readers`
  ADD PRIMARY KEY (`ReaderID`);

--
-- Chỉ mục cho bảng `Reports`
--
ALTER TABLE `Reports`
  ADD PRIMARY KEY (`ReportID`);

--
-- Chỉ mục cho bảng `Suppliers`
--
ALTER TABLE `Suppliers`
  ADD PRIMARY KEY (`SupplierID`);

--
-- Chỉ mục cho bảng `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Username` (`Username`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `Authors`
--
ALTER TABLE `Authors`
  MODIFY `AuthorID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `BookDamages`
--
ALTER TABLE `BookDamages`
  MODIFY `DamageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT cho bảng `Books`
--
ALTER TABLE `Books`
  MODIFY `BookID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT cho bảng `Genre`
--
ALTER TABLE `Genre`
  MODIFY `GenreID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `Inventory`
--
ALTER TABLE `Inventory`
  MODIFY `InventoryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT cho bảng `Inventory_Transactions`
--
ALTER TABLE `Inventory_Transactions`
  MODIFY `TransactionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `LibraryCards`
--
ALTER TABLE `LibraryCards`
  MODIFY `CardID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT cho bảng `Loans`
--
ALTER TABLE `Loans`
  MODIFY `LoanID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT cho bảng `Prices`
--
ALTER TABLE `Prices`
  MODIFY `PriceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `Readers`
--
ALTER TABLE `Readers`
  MODIFY `ReaderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `Reports`
--
ALTER TABLE `Reports`
  MODIFY `ReportID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT cho bảng `Suppliers`
--
ALTER TABLE `Suppliers`
  MODIFY `SupplierID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT cho bảng `Users`
--
ALTER TABLE `Users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `Prices`
--
ALTER TABLE `Prices`
  ADD CONSTRAINT `Prices_ibfk_1` FOREIGN KEY (`BookID`) REFERENCES `Books` (`BookID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
