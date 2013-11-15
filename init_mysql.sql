CREATE TABLE db_schema (
  id int NOT NULL PRIMARY KEY,
  version int NOT NULL COMMENT 'バージョン'
) COMMENT 'スキーマバージョン管理テーブル';
INSERT INTO db_schema VALUES(1, 0);
