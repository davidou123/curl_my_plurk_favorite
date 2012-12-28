
CREATE TABLE `myfavorite` (
  `link` varchar(50) NOT NULL,
  `uid` varchar(50) NOT NULL,
  `qualifier_translated` varchar(10) NOT NULL,
  `content` text NOT NULL,
  `date` varchar(30) NOT NULL,
  UNIQUE KEY `link` (`link`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 