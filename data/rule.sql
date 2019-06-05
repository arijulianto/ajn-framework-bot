CREATE TABLE IF NOT EXISTS `bot_chat_rule` (
`idchatbot` int(8) NOT NULL AUTO_INCREMENT PRIMARY KEY,
`pattern` text NOT NULL,
`callback` varchar(75) NOT NULL,
`prepare_text` varchar(100) NOT NULL,
`prio` varchar(5) NOT NULL,
`jenis` enum('task','ask') NOT NULL DEFAULT 'task',
`jawaban` varchar(255) NOT NULL,
`format` varchar(12) NOT NULL,
`aktif` enum('1','0') NOT NULL DEFAULT '1'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;