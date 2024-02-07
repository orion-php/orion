-- Orion tables first draft

-- orion_historical
CREATE TABLE orion_historical (
  id BIGINT PRIMARY KEY,
  unique_id varchar(24) NOT NULL,
  key_name varchar(150) NOT NULL,
  data_value varchar(255) NOT NULL,
  created TIMESTAMP NOT NULL,
  INDEX orion_historical_key_name (key_name),
  INDEX orion_historical_created (created),
  INDEX orion_historical_unique_id (unique_id)
);

-- orion_event
CREATE TABLE orion_event (
  id BIGINT PRIMARY KEY,
  unique_id varchar(24) NOT NULL,
  key_name varchar(150) NOT NULL,
  data_value blob NOT NULL,
  created TIMESTAMP NOT NULL,
  INDEX orion_event_key_name (key_name),
  INDEX orion_event_created (created),
  INDEX orion_event_unique_id (unique_id)
);

-- orion_series
CREATE TABLE orion_series (
  id BIGINT PRIMARY KEY,
  unique_id varchar(24) NOT NULL,
  series_id varchar(24) NOT NULL,
  key_name varchar(150) NOT NULL,
  data_value longblob NOT NULL,
  created TIMESTAMP NOT NULL,
  INDEX orion_series_key_name (key_name),
  INDEX orion_series_created (created),
  INDEX orion_series_unique_id (unique_id),
  INDEX orion_series_series_id (series_id)
);