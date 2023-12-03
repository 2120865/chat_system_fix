SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- --------------------------------------------------------

USE chat_system

-- Table structure for table `chat`


CREATE TABLE `chat` (
  `chatid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `chatroomid` int(11) NOT NULL,
  `message` varchar(200) NOT NULL,
  `chat_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- Table structure for table `chatroom`


CREATE TABLE `chatroom` (
  `chatroomid` int(11) NOT NULL,
  `chat_name` varchar(60) NOT NULL,
  `date_created` datetime NOT NULL,
  `chat_password` varchar(30) NOT NULL,
  `userid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- Dumping data for table `chatroom`

INSERT INTO `chatroom` (`chatroomid`, `chat_name`, `date_created`, `chat_password`, `userid`) VALUES
(1, 'Admin Chat Room', '2023-11-17 13:20:18', 'secret_password', 1),
(2, 'Free Entrance :)', '2017-11-17 13:20:51', '', 2),
(3, 'My First Chat Room', '2017-11-17 13:21:24', 'test', 2);

-- --------------------------------------------------------

-- Table structure for table `chat_member`

CREATE TABLE `chat_member` (
  `chat_memberid` int(11) NOT NULL,
  `chatroomid` int(11) NOT NULL,
  `userid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table `chat_member`

INSERT INTO `chat_member` (`chat_memberid`, `chatroomid`, `userid`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 2);

-- --------------------------------------------------------

-- Table structure for table `user`

CREATE TABLE `user` (
  `userid` int(11) NOT NULL,
  `username` varchar(30) NOT NULL,
  `password` varchar(150) NOT NULL,
  `uname` varchar(60) NOT NULL,
  `photo` varchar(200) NOT NULL,
  `access` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- Dumping data for table `user`

INSERT INTO `user` (`userid`, `username`, `password`, `uname`, `photo`, `access`) VALUES
(1, 'admin', '3dd1e950e484fcdab471a7088b9ec60c', 'Admin', '', 1),
(2, 'test', '098f6bcd4621d373cade4e832627b4f6', 'Test', '', 2);


-- Indexes for dumped tables

-- Indexes for table `chat`

ALTER TABLE `chat`
  ADD PRIMARY KEY (`chatid`);

-- Indexes for table `chatroom`

ALTER TABLE `chatroom`
  ADD PRIMARY KEY (`chatroomid`);


-- Indexes for table `chat_member`

ALTER TABLE `chat_member`
  ADD PRIMARY KEY (`chat_memberid`);


-- Indexes for table `user`

ALTER TABLE `user`
  ADD PRIMARY KEY (`userid`);


-- AUTO_INCREMENT for dumped tables



-- AUTO_INCREMENT for table `chat`

ALTER TABLE `chat`
  MODIFY `chatid` int(11) NOT NULL AUTO_INCREMENT;

-- AUTO_INCREMENT for table `chatroom`

ALTER TABLE `chatroom`
  MODIFY `chatroomid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

-- AUTO_INCREMENT for table `chat_member`

ALTER TABLE `chat_member`
  MODIFY `chat_memberid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

-- AUTO_INCREMENT for table `user`

ALTER TABLE `user`
  MODIFY `userid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;