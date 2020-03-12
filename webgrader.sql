-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 12, 2020 at 09:21 PM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `webgrader`
--

-- --------------------------------------------------------

--
-- Table structure for table `class`
--

CREATE TABLE `class` (
  `uuid` char(37) NOT NULL,
  `name` varchar(64) NOT NULL,
  `section` tinyint(3) UNSIGNED NOT NULL,
  `class` char(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `class`
--

INSERT INTO `class` (`uuid`, `name`, `section`, `class`) VALUES
('2fce480b-648f-11ea-86b8-1c1b0da7f6b6', 'Introduction to Programming in Python', 1, 'CS100'),
('2fce5936-648f-11ea-86b8-1c1b0da7f6b6', 'Introduction to Programming in Python', 2, 'CS100'),
('2fce66b4-648f-11ea-86b8-1c1b0da7f6b6', 'Programming Language Concepts', 5, 'CS280'),
('2fce73f1-648f-11ea-86b8-1c1b0da7f6b6', 'Programming Language Concepts', 10, 'CS280');

-- --------------------------------------------------------

--
-- Table structure for table `examanswer`
--

CREATE TABLE `examanswer` (
  `uuid` char(37) NOT NULL,
  `eqid` char(37) NOT NULL,
  `pointsReceived` tinyint(4) NOT NULL,
  `submissionText` varchar(256) NOT NULL,
  `professorFeedback` varchar(128) DEFAULT NULL,
  `autoFeedback` varchar(256) NOT NULL,
  `studentID` char(37) NOT NULL,
  `status` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `examquestion`
--

CREATE TABLE `examquestion` (
  `uuid` char(37) NOT NULL,
  `qid` char(37) NOT NULL,
  `maxPoints` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `professor`
--

CREATE TABLE `professor` (
  `uuid` char(37) NOT NULL,
  `name` varchar(64) NOT NULL,
  `cid` char(37) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `professor`
--

INSERT INTO `professor` (`uuid`, `name`, `cid`) VALUES
('24d70170-6494-11ea-86b8-1c1b0da7f6b6', 'Joe Smith', '2fce480b-648f-11ea-86b8-1c1b0da7f6b6'),
('24d711c1-6494-11ea-86b8-1c1b0da7f6b6', 'Sammie Robertson', '2fce5936-648f-11ea-86b8-1c1b0da7f6b6'),
('24d71f14-6494-11ea-86b8-1c1b0da7f6b6', 'Nancy Williams', '2fce73f1-648f-11ea-86b8-1c1b0da7f6b6');

-- --------------------------------------------------------

--
-- Table structure for table `question`
--

CREATE TABLE `question` (
  `uuid` char(37) NOT NULL,
  `text` varchar(512) NOT NULL,
  `difficulty` tinyint(4) NOT NULL,
  `topic` varchar(64) NOT NULL,
  `creatorID` varchar(37) NOT NULL,
  `creationDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `question`
--

INSERT INTO `question` (`uuid`, `text`, `difficulty`, `topic`, `creatorID`, `creationDate`) VALUES
('f5b87650-6499-11ea-86b8-1c1b0da7f6b6', 'Write a for loop that prints values on the range [1, 10]', 1, 'For Loops', '24d70170-6494-11ea-86b8-1c1b0da7f6b6', '2020-03-12 19:45:00'),
('f5b8a087-6499-11ea-86b8-1c1b0da7f6b6', 'A person\'s age is held in a variable called age. If they\'re over 18, they can vote. Make an if-else block to print \'You can vote!\' if the person can vote, or \'You cannot vote!\' if the person cannot.', 1, 'Conditionals', '24d71f14-6494-11ea-86b8-1c1b0da7f6b6', '2020-03-12 19:29:00'),
('f5b8b98f-6499-11ea-86b8-1c1b0da7f6b6', 'The parameter weekday is True if it is a weekday, and the parameter vacation is True if we are on vacation. We sleep in if it is not a weekday or we\'re on vacation. Return True if we sleep in.\r\n\r\n\r\nsleep_in(False, False) → True\r\nsleep_in(True, False) → False\r\nsleep_in(False, True) → True\r\n\r\nImplement a function sleep_in(weekday, vacation) to return whether we\'re sleeping or not.', 1, 'Conditionals', '24d711c1-6494-11ea-86b8-1c1b0da7f6b6', '2020-03-04 20:45:00'),
('f5b8cbed-6499-11ea-86b8-1c1b0da7f6b6', 'We have two monkeys, a and b, and the parameters a_smile and b_smile indicate if each is smiling. We are in trouble if they are both smiling or if neither of them is smiling. Return True if we are in trouble.\r\n\r\n\r\nmonkey_trouble(True, True) → True\r\nmonkey_trouble(False, False) → True\r\nmonkey_trouble(True, False) → False\r\n\r\nImplement this logic in a function monkey_trouble(a_smile, b_smile) that returns whether we are in trouble.', 1, 'Conditionals', '24d711c1-6494-11ea-86b8-1c1b0da7f6b6', '2020-03-12 19:45:00'),
('f5b8dbc7-6499-11ea-86b8-1c1b0da7f6b6', 'Given two int values, return their sum. Unless the two values are the same, then return double their sum.\r\n\r\n\r\nsum_double(1, 2) → 3\r\nsum_double(3, 2) → 5\r\nsum_double(2, 2) → 8\r\n\r\nImplement this behavior in function sum_double(a, b).', 1, 'Conditionals', '24d70170-6494-11ea-86b8-1c1b0da7f6b6', '2020-03-12 19:45:00'),
('f5b8f421-6499-11ea-86b8-1c1b0da7f6b6', 'Given an int n, return the absolute difference between n and 21, except return double the absolute difference if n is over 21.\r\n\r\n\r\ndiff21(19) → 2\r\ndiff21(10) → 11\r\ndiff21(21) → 0\r\n\r\nImplement this behavior in function diff21(n).', 1, 'Conditionals', '24d70170-6494-11ea-86b8-1c1b0da7f6b6', '2020-03-12 19:45:00'),
('f5b90931-6499-11ea-86b8-1c1b0da7f6b6', 'We have a loud talking parrot. The \"hour\" parameter is the current hour time in the range 0..23. We are in trouble if the parrot is talking and the hour is before 7 or after 20. Return True if we are in trouble.\r\n\r\n\r\nparrot_trouble(True, 6) → True\r\nparrot_trouble(True, 7) → False\r\nparrot_trouble(False, 6) → False\r\n\r\nImplement this logic in function parrot_trouble(talking, hour).', 1, 'Conditionals', '24d71f14-6494-11ea-86b8-1c1b0da7f6b6', '2020-03-20 19:45:00'),
('f5b924dd-6499-11ea-86b8-1c1b0da7f6b6', 'Given 2 ints, a and b, return True if one if them is 10 or if their sum is 10.\r\n\r\n\r\nmakes10(9, 10) → True\r\nmakes10(9, 9) → False\r\nmakes10(1, 9) → True\r\n\r\nImplement this behavior in function makes10(a, b).', 1, 'Conditionals', '24d711c1-6494-11ea-86b8-1c1b0da7f6b6', '2020-03-12 19:45:00'),
('f5b938ec-6499-11ea-86b8-1c1b0da7f6b6', 'Given an int n, return True if it is within 10 of 100 or 200. Note: abs(num) computes the absolute value of a number.\r\n\r\n\r\nnear_hundred(93) → True\r\nnear_hundred(90) → True\r\nnear_hundred(89) → False\r\n\r\nImplement this logic in function near_hundred(n).', 1, 'Conditionals', '24d70170-6494-11ea-86b8-1c1b0da7f6b6', '2020-03-12 19:45:00'),
('f5b9526f-6499-11ea-86b8-1c1b0da7f6b6', 'Given 2 int values, return True if one is negative and one is positive. Except if the parameter \"negative\" is True, then return True only if both are negative.\r\n\r\n\r\npos_neg(1, -1, False) → True\r\npos_neg(-1, 1, False) → True\r\npos_neg(-4, -5, True) → True\r\n\r\nImplement this behavior in function pos_neg(a, b, negative).', 2, 'Conditionals', '24d71f14-6494-11ea-86b8-1c1b0da7f6b6', '2020-03-11 19:45:00'),
('f5b96425-6499-11ea-86b8-1c1b0da7f6b6', 'Given a string, return a new string where \"not \" has been added to the front. However, if the string already begins with \"not\", return the string unchanged.\r\n\r\n\r\nnot_string(\'candy\') → \'not candy\'\r\nnot_string(\'x\') → \'not x\'\r\nnot_string(\'not bad\') → \'not bad\'\r\n\r\nImplement this behavior in function not_string(str).', 1, 'Conditionals', '24d71f14-6494-11ea-86b8-1c1b0da7f6b6', '2020-03-12 19:45:00'),
('f5b97809-6499-11ea-86b8-1c1b0da7f6b6', 'Given a non-empty string and an int n, return a new string where the char at index n has been removed. The value of n will be a valid index of a char in the original string (i.e. n will be in the range 0..len(str)-1 inclusive).\r\n\r\n\r\nmissing_char(\'kitten\', 1) → \'ktten\'\r\nmissing_char(\'kitten\', 0) → \'itten\'\r\nmissing_char(\'kitten\', 4) → \'kittn\'\r\n\r\nImplement this behavior in function missing_char(str, n).', 2, 'Conditionals', '24d71f14-6494-11ea-86b8-1c1b0da7f6b6', '2020-03-12 19:45:00'),
('f5b98fc1-6499-11ea-86b8-1c1b0da7f6b6', 'Given a string, return a new string where the first and last chars have been exchanged.\r\n\r\n\r\nfront_back(\'code\') → \'eodc\'\r\nfront_back(\'a\') → \'a\'\r\nfront_back(\'ab\') → \'ba\'', 1, 'Conditionals', '24d70170-6494-11ea-86b8-1c1b0da7f6b6', '2020-03-12 16:45:00'),
('f5b9a3f0-6499-11ea-86b8-1c1b0da7f6b6', 'Given a string, we\'ll say that the front is the first 3 chars of the string. If the string length is less than 3, the front is whatever is there. Return a new string which is 3 copies of the front.\r\n\r\n\r\nfront3(\'Java\') → \'JavJavJav\'\r\nfront3(\'Chocolate\') → \'ChoChoCho\'\r\nfront3(\'abc\') → \'abcabcabc\'\r\n\r\nImplement this logic in function front3(str)', 1, 'Strings', '24d711c1-6494-11ea-86b8-1c1b0da7f6b6', '2020-03-12 19:45:00'),
('f5b9bf54-6499-11ea-86b8-1c1b0da7f6b6', 'Given a string and a non-negative int n, return a larger string that is n copies of the original string.\r\n\r\n\r\nstring_times(\'Hi\', 2) → \'HiHi\'\r\nstring_times(\'Hi\', 3) → \'HiHiHi\'\r\nstring_times(\'Hi\', 1) → \'Hi\'\r\n\r\nImplement this behavior in function string_times(str, n).', 2, 'Strings', '24d70170-6494-11ea-86b8-1c1b0da7f6b6', '2020-03-12 19:45:00'),
('f5b9d2e2-6499-11ea-86b8-1c1b0da7f6b6', 'Given a string and a non-negative int n, we\'ll say that the front of the string is the first 3 chars, or whatever is there if the string is less than length 3. Return n copies of the front;\r\n\r\n\r\nfront_times(\'Chocolate\', 2) → \'ChoCho\'\r\nfront_times(\'Chocolate\', 3) → \'ChoChoCho\'\r\nfront_times(\'Abc\', 3) → \'AbcAbcAbc\'', 2, 'Strings', '24d71f14-6494-11ea-86b8-1c1b0da7f6b6', '2020-03-12 19:45:00'),
('f5b9e681-6499-11ea-86b8-1c1b0da7f6b6', 'Given a string, return a new string made of every other char starting with the first, so \"Hello\" yields \"Hlo\".\r\n\r\n\r\nstring_bits(\'Hello\') → \'Hlo\'\r\nstring_bits(\'Hi\') → \'H\'\r\nstring_bits(\'Heeololeo\') → \'Hello\'\r\n\r\nImplement this behavior in function string_bits(str).', 2, 'Strings', '24d711c1-6494-11ea-86b8-1c1b0da7f6b6', '2020-03-20 19:45:00'),
('f5b9f8dc-6499-11ea-86b8-1c1b0da7f6b6', 'Given a non-empty string like \"Code\" return a string like \"CCoCodCode\".\r\n\r\n\r\nstring_splosion(\'Code\') → \'CCoCodCode\'\r\nstring_splosion(\'abc\') → \'aababc\'\r\nstring_splosion(\'ab\') → \'aab\'\r\n\r\nImplement this behavior in function string_splosion(str).', 3, 'Strings', '24d71f14-6494-11ea-86b8-1c1b0da7f6b6', '2020-03-12 19:45:00'),
('f5ba1035-6499-11ea-86b8-1c1b0da7f6b6', 'Given a string, return the count of the number of times that a substring length 2 appears in the string and also as the last 2 chars of the string, so \"hixxxhi\" yields 1 (we won\'t count the end substring).\r\n\r\n\r\nlast2(\'hixxhi\') → 1\r\nlast2(\'xaxxaxaxx\') → 1\r\nlast2(\'axxxaaxx\') → 2\r\n\r\nImplement this behavior in function last2(str).', 3, 'Strings', '24d71f14-6494-11ea-86b8-1c1b0da7f6b6', '2020-03-12 19:45:00'),
('f5ba2265-6499-11ea-86b8-1c1b0da7f6b6', 'Given an array of ints, return True if the sequence of numbers 1, 2, 3 appears in the array somewhere.\r\n\r\n\r\narray123([1, 1, 2, 3, 1]) → True\r\narray123([1, 1, 2, 4, 1]) → False\r\narray123([1, 1, 2, 1, 2, 3]) → True\r\n\r\nImplement this logic in function array123(nums).', 2, 'Lists', '24d711c1-6494-11ea-86b8-1c1b0da7f6b6', '2020-03-10 19:45:00'),
('f5ba3af5-6499-11ea-86b8-1c1b0da7f6b6', 'Given an array of ints, return the number of 9\'s in the array.\r\n\r\n\r\narray_count9([1, 2, 9]) → 1\r\narray_count9([1, 9, 9]) → 2\r\narray_count9([1, 9, 9, 3, 9]) → 3\r\n\r\nImplement this logic in function array_count9(nums).', 1, 'Lists', '24d71f14-6494-11ea-86b8-1c1b0da7f6b6', '2020-03-12 19:45:00'),
('f5ba6c78-6499-11ea-86b8-1c1b0da7f6b6', 'Given 2 arrays of ints, a and b, return True if they have the same first element or they have the same last element. Both arrays will be length 1 or more.\r\n\r\n\r\ncommon_end([1, 2, 3], [7, 3]) → True\r\ncommon_end([1, 2, 3], [7, 3, 2]) → False\r\ncommon_end([1, 2, 3], [1, 3]) → True\r\n\r\nImplement this logic in function common_end(a, b).', 2, 'Lists', '24d711c1-6494-11ea-86b8-1c1b0da7f6b6', '2020-03-06 20:45:00'),
('f5ba7d0c-6499-11ea-86b8-1c1b0da7f6b6', 'Given an array of ints length 3, return the sum of all the elements.\r\n\r\n\r\nsum3([1, 2, 3]) → 6\r\nsum3([5, 11, 2]) → 18\r\nsum3([7, 0, 0]) → 7\r\n\r\nImplement this logic in function sum3(nums).', 1, 'Lists', '24d71f14-6494-11ea-86b8-1c1b0da7f6b6', '2020-03-12 19:45:00'),
('f5ba8be9-6499-11ea-86b8-1c1b0da7f6b6', 'Return the number of even ints in the given array. Note: the % \"mod\" operator computes the remainder, e.g. 5 % 2 is 1.\r\n\r\n\r\ncount_evens([2, 1, 2, 3, 4]) → 3\r\ncount_evens([2, 2, 0]) → 3\r\ncount_evens([1, 3, 5]) → 0\r\n\r\nImplement this logic in function count_evens(nums).', 2, 'Lists', '24d70170-6494-11ea-86b8-1c1b0da7f6b6', '2020-03-13 14:45:00'),
('f5baa7a8-6499-11ea-86b8-1c1b0da7f6b6', 'Return True if the string \"cat\" and \"dog\" appear the same number of times in the given string.\r\n\r\n\r\ncat_dog(\'catdog\') → True\r\ncat_dog(\'catcat\') → False\r\ncat_dog(\'1cat1cadodog\') → True\r\n\r\nImplement this logic in function cat_dog(str).', 2, 'Strings', '24d711c1-6494-11ea-86b8-1c1b0da7f6b6', '2020-03-12 19:45:00');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `uuid` char(37) NOT NULL,
  `name` varchar(64) NOT NULL,
  `cid` char(37) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`uuid`, `name`, `cid`) VALUES
('08ae7bf7-6492-11ea-86b8-1c1b0da7f6b6', 'James Cornell', '2fce480b-648f-11ea-86b8-1c1b0da7f6b6'),
('08ae7bf7-6492-11ea-86b8-1c1b0da7f6b6', 'James Cornell', '2fce73f1-648f-11ea-86b8-1c1b0da7f6b6'),
('08ae8cdc-6492-11ea-86b8-1c1b0da7f6b6', 'Michael Szera', '2fce480b-648f-11ea-86b8-1c1b0da7f6b6'),
('08ae9a4f-6492-11ea-86b8-1c1b0da7f6b6', 'Jenna Myers', '2fce480b-648f-11ea-86b8-1c1b0da7f6b6'),
('08ae9a4f-6492-11ea-86b8-1c1b0da7f6b6', 'Jenna Myers', '2fce66b4-648f-11ea-86b8-1c1b0da7f6b6'),
('08aea741-6492-11ea-86b8-1c1b0da7f6b6', 'Chiara Tarenne', '2fce480b-648f-11ea-86b8-1c1b0da7f6b6');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `name` varchar(16) NOT NULL,
  `hashedPW` varchar(65) NOT NULL,
  `profID` char(37) DEFAULT NULL,
  `studID` char(37) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `class`
--
ALTER TABLE `class`
  ADD PRIMARY KEY (`uuid`);

--
-- Indexes for table `examanswer`
--
ALTER TABLE `examanswer`
  ADD PRIMARY KEY (`uuid`,`eqid`),
  ADD KEY `eqid` (`eqid`),
  ADD KEY `studentID` (`studentID`);

--
-- Indexes for table `examquestion`
--
ALTER TABLE `examquestion`
  ADD PRIMARY KEY (`uuid`,`qid`),
  ADD KEY `qid` (`qid`);

--
-- Indexes for table `professor`
--
ALTER TABLE `professor`
  ADD PRIMARY KEY (`uuid`,`cid`),
  ADD KEY `cid` (`cid`);

--
-- Indexes for table `question`
--
ALTER TABLE `question`
  ADD PRIMARY KEY (`uuid`),
  ADD KEY `creatorID` (`creatorID`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`uuid`,`cid`),
  ADD KEY `cid` (`cid`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`name`),
  ADD KEY `profID` (`profID`),
  ADD KEY `studID` (`studID`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `examanswer`
--
ALTER TABLE `examanswer`
  ADD CONSTRAINT `examanswer_ibfk_1` FOREIGN KEY (`eqid`) REFERENCES `examquestion` (`uuid`),
  ADD CONSTRAINT `examanswer_ibfk_2` FOREIGN KEY (`studentID`) REFERENCES `student` (`uuid`);

--
-- Constraints for table `examquestion`
--
ALTER TABLE `examquestion`
  ADD CONSTRAINT `examquestion_ibfk_1` FOREIGN KEY (`qid`) REFERENCES `question` (`uuid`);

--
-- Constraints for table `professor`
--
ALTER TABLE `professor`
  ADD CONSTRAINT `professor_ibfk_1` FOREIGN KEY (`cid`) REFERENCES `class` (`uuid`);

--
-- Constraints for table `question`
--
ALTER TABLE `question`
  ADD CONSTRAINT `question_ibfk_1` FOREIGN KEY (`creatorID`) REFERENCES `professor` (`uuid`);

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `student_ibfk_1` FOREIGN KEY (`cid`) REFERENCES `class` (`uuid`);

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`profID`) REFERENCES `professor` (`uuid`),
  ADD CONSTRAINT `user_ibfk_2` FOREIGN KEY (`studID`) REFERENCES `student` (`uuid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
