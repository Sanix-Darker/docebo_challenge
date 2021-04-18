SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

--
-- Database: `docebo_challenge`
--
CREATE DATABASE IF NOT EXISTS docebo_challenge

-- --------------------------------------------------------

--
-- Table structure for table `node_tree`
--

CREATE TABLE `node_tree` (
  `idNode` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `iLeft` int(11) NOT NULL,
  `iRight` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- --------------------------------------------------------

--
-- Table structure for table `node_tree_names`
--

CREATE TABLE `node_tree_names` (
  `idNode` int(11) NOT NULL,
  `language` enum('english','italian') NOT NULL,
  `nodeName` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `node_tree`
--
ALTER TABLE `node_tree`
  ADD PRIMARY KEY (`idNode`);

--
-- Indexes for table `node_tree_names`
--
ALTER TABLE `node_tree_names`
  ADD KEY `idNode` (`idNode`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `node_tree`
--
ALTER TABLE `node_tree`
  MODIFY `idNode` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `node_tree_names`
--
ALTER TABLE `node_tree_names`
  ADD CONSTRAINT `node_tree_names_ibfk_1` FOREIGN KEY (`idNode`) REFERENCES `node_tree` (`idNode`);
COMMIT;
