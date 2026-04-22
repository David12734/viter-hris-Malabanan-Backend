-- Table structure for table `memo`

CREATE TABLE `memo` (
  `memo_aid` int(11) NOT NULL,
  `memo_is_active` tinyint(1) NOT NULL DEFAULT 1,
  `memo_from` varchar(200) NOT NULL,
  `memo_to` varchar(200) NOT NULL,
  `memo_date` varchar(20) NOT NULL,
  `memo_category` varchar(128) NOT NULL,
  `memo_text` text NOT NULL,
  `memo_created` datetime NOT NULL,
  `memo_updated` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Indexes for table `memo`
ALTER TABLE `memo`
  ADD PRIMARY KEY (`memo_aid`);

-- AUTO_INCREMENT for table `memo`
ALTER TABLE `memo`
  MODIFY `memo_aid` int(11) NOT NULL AUTO_INCREMENT;