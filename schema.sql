CREATE TABLE activity (
   id INTEGER PRIMARY KEY AUTOINCREMENT,
   title varchar(200) NOT NULL,
   type char DEFAULT 'p',
   abstract text,
   user_id int unsigned DEFAULT NULL,
   duration time default NULL,
   location_id int unsigned DEFAULT NULL,
   day tinyint unsigned DEFAULT NULL,
   notes text, 
	 starttime time DEFAULT NULL, 
   url_slides TEXT DEFAULT NULL, 
   url_paper TEXT DEFAULT NULL, 
   url_stream TEXT DEFAULT NULL, 
   url_audio TEXT DEFAULT NULL, 
   url_misc TEXT DEFAULT NULL, 
   url_image TEXT DEFAULT NULL, 
   status INTEGER DEFAULT 1,
   editlock datetime default 0,
   UNIQUE (title)
);

CREATE TABLE location (
   id INTEGER PRIMARY KEY AUTOINCREMENT,
   name varchar(50) NOT NULL,
   editlock datetime default 0
);

CREATE TABLE user (
   id INTEGER PRIMARY KEY AUTOINCREMENT,
   name varchar(50) NOT NULL,
   bio text, email TEXT DEFAULT NULL,
   editlock datetime default 0,
   UNIQUE (name)
);
CREATE TABLE usermap (
   activity_id INTEGER,
   user_id INTEGER,
   UNIQUE (activity_id, user_id)
);
