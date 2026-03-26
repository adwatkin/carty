-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 26, 2026 at 03:01 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `green`
--

-- --------------------------------------------------------

--
-- Table structure for table `basket`
--

CREATE TABLE `basket` (
  `basketid` int NOT NULL,
  `orderid` int NOT NULL,
  `itemid` int NOT NULL,
  `quantity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `custorder`
--

CREATE TABLE `custorder` (
  `orderid` int NOT NULL,
  `datemade` int NOT NULL,
  `datefor` int NOT NULL,
  `userid` int NOT NULL,
  `delORcol` text NOT NULL,
  `orderstatus` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `item`
--

CREATE TABLE `item` (
  `itemid` int NOT NULL,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `category` text NOT NULL,
  `dailyquantity` int NOT NULL,
  `imglink` text NOT NULL,
  `unitprice` decimal(10,0) NOT NULL,
  `nutritional information` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `item`
--

INSERT INTO `item` (`itemid`, `name`, `description`, `category`, `dailyquantity`, `imglink`, `unitprice`, `nutritional information`) VALUES
(1, 'Cheese', 'Cheesy Cheese from the Cheesiest cheese farm in the whole of chedderville', 'Dairy', 20, 'cheese.png', 13, 'This is some numbers'),
(2, 'Tomato', 'A lovely red tomato... probably grown on your uncle Dave\'s allotment. ', 'Fruit', 30, 'tomato.png', 1, 'This is some numbers for tomato'),
(3, 'Hamburger Patty', 'This is a lovely 100% beef hamburger patty... might also not be 100% as might be salt and some onion in it.. might also not be beef. ', 'Meat', 30, 'hamburger.png', 5, 'Some numbers about beef'),
(4, 'Eggs (Dozen)', 'Lovely fresh eggs from down\'t rowd from them lovely chicken botherers. Yum yum fresh!', 'Dairy', 100, 'eggs.png', 5, 'Some numbers about chicken surprise eggs. '),
(5, 'Milk (2 Litre)', 'Lovely fresh cow juice straight from the Bull, I mean cow. Produced over\'t hill at local farm. ', 'Dairy', 100, '', 3, 'Milk numbers'),
(6, 'Cucumber (1 length)', 'Lovely cucumber, is it for your eyes, your salad or a cocktail or other... its lovely, fresh and home grown in god\'s own country... YORKSHIRE!', 'Vegatable', 20, 'cucumber.png', 3, 'Numbers'),
(7, 'Chicken Breasts (2 pieces)', 'Lovely fresh chicken from\'t farm down rowad! Good for your gainz!', 'Meat', 20, 'chicken.png', 6, 'Numbers');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `userid` int NOT NULL,
  `email` text NOT NULL,
  `password` text NOT NULL,
  `fname` text NOT NULL,
  `sname` text NOT NULL,
  `addrln1` text NOT NULL,
  `addrln2` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`userid`, `email`, `password`, `fname`, `sname`, `addrln1`, `addrln2`) VALUES
(1, 'adam.watkin@gmail.com', '$2y$10$UqMOOR7Pt/JI3qt0Kxn/W.d.GXexDH93U4.vs7kQNqLzlIp4mZR72', 'A', 'A', 'A', 'A');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `basket`
--
ALTER TABLE `basket`
  ADD PRIMARY KEY (`basketid`),
  ADD KEY `orderid` (`orderid`,`itemid`),
  ADD KEY `itemid` (`itemid`);

--
-- Indexes for table `custorder`
--
ALTER TABLE `custorder`
  ADD PRIMARY KEY (`orderid`),
  ADD KEY `userid` (`userid`);

--
-- Indexes for table `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`itemid`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`userid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `basket`
--
ALTER TABLE `basket`
  MODIFY `basketid` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `custorder`
--
ALTER TABLE `custorder`
  MODIFY `orderid` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `item`
--
ALTER TABLE `item`
  MODIFY `itemid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `userid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `basket`
--
ALTER TABLE `basket`
  ADD CONSTRAINT `basket_ibfk_1` FOREIGN KEY (`itemid`) REFERENCES `item` (`itemid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `basket_ibfk_2` FOREIGN KEY (`orderid`) REFERENCES `custorder` (`orderid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `custorder`
--
ALTER TABLE `custorder`
  ADD CONSTRAINT `custorder_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `user` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
