CREATE TABLE iplog (
  ip_addr TEXT,
  ac_time datetime
);

CREATE TABLE pubrg (
  name text,
  prename text,
  tagline text,
  unique (name, prename, tagline)
}
