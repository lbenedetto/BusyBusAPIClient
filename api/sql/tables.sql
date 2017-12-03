CREATE TABLE IF NOT EXISTS trips
(
  route_id INT,
  trip_id INT PRIMARY KEY ,
  label VARCHAR(100),
  shape_id INT
);

CREATE TABLE IF NOT EXISTS shapes
(
  shape_id INT,
  latitude VARCHAR(13),
  longitude VARCHAR(13),
  sequence INT
);

CREATE TABLE IF NOT EXISTS stops
(
  trip_id INT,
  arrival_time VARCHAR(8),
  departure_time VARCHAR(8),
  stop_name_short VARCHAR(8)
);

CREATE TABLE IF NOT EXISTS stop_names
(
  long_name VARCHAR(50),
  short_name VARCHAR(8)
);