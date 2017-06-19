CREATE TABLE IF NOT EXISTS `down_folders` (
  `fd_id` 		int(11) NOT NULL auto_increment,
  `fd_name` 	varchar(50) default '',
  `fd_uid` 		int(11) default '0',
  `fd_mac` 		varchar(50) default '',
  `fd_pathLoc` 	varchar(255) default '',
  `fd_complete` tinyint(1) default '0',
  `fd_id_old` 	varchar(512) default '',
  `fd_percent` 	varchar(7) default '',
  PRIMARY KEY  (`fd_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;