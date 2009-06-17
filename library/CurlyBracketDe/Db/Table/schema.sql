CREATE TABLE contents (
  name        varchar(32) NOT NULL,
  title       varchar(64) NOT NULL,
  contents    text NOT NULL,
  created     datetime NOT NULL,
  PRIMARY KEY (name),
  UNIQUE (title)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
