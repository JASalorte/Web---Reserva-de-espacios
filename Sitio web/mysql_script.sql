CREATE TABLE reserve_table (
	id INTEGER AUTO_INCREMENT PRIMARY KEY,
	site VARCHAR(100),
	plane VARCHAR(100),
	name VARCHAR(100),
	node INTEGER,
	startdate INTEGER(8),
	enddate INTEGER(8),
	state ENUM('Reserved','Revising','Bloqued'),
	userinfo VARCHAR(500),
	eventinfo VARCHAR(500),
	necesities VARCHAR(500),
	UNIQUE reserve_uk (site,plane,node,startdate)
);

CREATE TABLE users (
	id int(11) NOT NULL auto_increment,
	username varchar(20) NOT NULL,
	password char(40) NOT NULL,
	PRIMARY KEY (id),
	UNIQUE KEY username (username)
);