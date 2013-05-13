DROP TABLE IF EXISTS playlogs;

DROP TABLE IF EXISTS loop_content_bins;
DROP TABLE IF EXISTS loop_ads;
DROP TABLE IF EXISTS loops;

DROP TABLE IF EXISTS content_items;
DROP TABLE IF EXISTS content_bins;
DROP TABLE IF EXISTS ads;

DROP TABLE IF EXISTS command_file_assets;
DROP TABLE IF EXISTS file_assets;
DROP TABLE IF EXISTS assets;
DROP TABLE IF EXISTS commands;

DROP TABLE IF EXISTS sync_hours;

CREATE TABLE IF NOT EXISTS sync_hours (
  day_of_week   INTEGER                       NOT NULL,
  none          INTEGER                       NOT NULL    DEFAULT 0,
  `all`         INTEGER                       NOT NULL    DEFAULT 1,
  start         INTEGER,
  `end`         INTEGER
);

CREATE TABLE IF NOT EXISTS commands (
  id				      INTEGER			NOT NULL,
  priority		    INTEGER			NOT NULL		DEFAULT 1,
  `name`			    TEXT			  NOT NULL,
  status			    TEXT			  NOT NULL		DEFAULT 'received',
  result          TEXT,
  error_message		TEXT,
  secret          TEXT,
  created			    DATETIME 		NOT NULL		DEFAULT CURRENT_TIMESTAMP,
  completed			  DATETIME,
  PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS assets (
	id			  INTEGER					NOT NULL,
	type		  TEXT						NOT NULL	DEFAULT 'video',
  `name`    TEXT            NOT NULL, 
  duration  INTEGER         NOT NULL,
	created		DATETIME 				NOT NULL	DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS remote_assets (
  id            INTEGER						NOT NULL,
  asset_id		  INTEGER						NOT NULL,
  source_host   TEXT						  NOT NULL,
  filename      TEXT						  NOT NULL,
  md5           TEXT						  NOT NULL,
  remote_path   TEXT						  NOT NULL,
  ready         INTEGER						NOT NULL    DEFAULT 0,
  created		    DATETIME					NOT NULL	DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  FOREIGN KEY (asset_id) REFERENCES assets (id)
);

CREATE TABLE IF NOT EXISTS video_meta_data (
  `id` INTEGER NOT NULL,
  `height` INTEGER DEFAULT NULL,
  `width` INTEGER DEFAULT NULL,
  `duration` REAL DEFAULT NULL,
  `container` TEXT DEFAULT NULL,
  `video_codec` TEXT DEFAULT NULL,
  `video_bitrate` INTEGER DEFAULT NULL,
  `framerate` REAL DEFAULT NULL,
  `timebase_framerate` REAL DEFAULT NULL,
  `audio_codec` TEXT DEFAULT NULL,
  `audio_bitrate` INTEGER DEFAULT NULL,
  `sampling_rate` INTEGER DEFAULT NULL,
  PRIMARY KEY (`id`)
);


CREATE TABLE IF NOT EXISTS video_assets (
  id                  INTEGER           NOT NULL,
  filename            TEXT            NOT NULL,
  local_path          TEXT,
  asset_id            INTEGER           NOT NULL,
  video_meta_data_id  INTEGER NOT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (video_meta_data_id) REFERENCES video_meta_data(id),
  FOREIGN KEY (asset_id) REFERENCES asset(id)
);

CREATE TABLE IF NOT EXISTS command_remote_assets (
  command_id    	INTEGER                       NOT NULL,
  remote_asset_id     INTEGER                       NOT NULL,
  PRIMARY KEY (command_id, remote_asset_id),
  FOREIGN KEY (remote_asset_id) REFERENCES file_assets(id),
  FOREIGN KEY (command_id) REFERENCES commands(id)
);


CREATE TABLE IF NOT EXISTS ads (
	id			  INTEGER						 NOT NULL,
	`name`		TEXT						   NOT NULL,
	asset_id	INTEGER						 NOT NULL,
	status		TEXT						   NOT NULL,
	created		DATETIME 					 NOT NULL		DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS content_bins (
	id			INTEGER						NOT NULL,
	`name`		TEXT						NOT NULL,
	status		TEXT						NOT NULL,
	created		DATETIME 					NOT NULL		DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS content_item (
	id			INTEGER						NOT NULL,
	`name`		TEXT						NOT NULL,
	asset_id	INTEGER						NOT NULL,
	status		TEXT						NOT NULL,
	created		DATETIME 					NOT NULL		DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS content_bin_items (
  content_bin_id      INTEGER           NOT NULL,
  content_item_id     INTEGER           NOT NULL,
  PRIMARY KEY (content_bin_id, content_item_id),
  FOREIGN KEY (content_bin_id) REFERENCES content_bin (id),
  FOREIGN KEY (content_item_id) REFERENCES content_item (id)
);

CREATE TABLE IF NOT EXISTS loops (
  id            	INTEGER                       NOT NULL,
  `name`        	TEXT                          NOT NULL,
  loop_length   	INTEGER                       NOT NULL,
  allowed_variance	REAL						NOT NULL	DEFAULT 0,
  active        	INTEGER                       NOT NULL  DEFAULT 0,
  playing       	INTEGER                       NOT NULL  DEFAULT 0,
  created			DATETIME 		NOT NULL		DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS loop_ads (
	loop_id			INTEGER			NOT NULL,
	ad_id			INTEGER			NOT NULL,
	PRIMARY KEY (loop_id, ad_id),
	FOREIGN KEY (loop_id) REFERENCES loops (id),
	FOREIGN KEY (ad_id) REFERENCES ads (id)
);

CREATE TABLE IF NOT EXISTS loop_content_bins (
	loop_id			INTEGER			NOT NULL,
	content_bin_id	INTEGER			NOT NULL,
	PRIMARY KEY (loop_id, content_bin_id),
	FOREIGN KEY (loop_id) REFERENCES loops (id),
	FOREIGN KEY (content_bin_id) REFERENCES content_bins (id)
);

CREATE TABLE IF NOT EXISTS playlogs (
  id				INTEGER			PRIMARY KEY		AUTOINCREMENT,
  ad_id				INTEGER                       	NOT NULL,
  `date`			TEXT                          	NOT NULL,
  plays				INTEGER                       	NOT NULL  DEFAULT 0,
  created			DATETIME 		NOT NULL		DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (ad_id) REFERENCES ads(id)
);

