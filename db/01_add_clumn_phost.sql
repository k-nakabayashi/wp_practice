use wp_db;

alter table wp_posts MODIFY COLUMN post_date datetime not null default '1000-01-01 00:00:00',
MODIFY COLUMN post_date_gmt datetime not null default '1000-01-01 00:00:00',
MODIFY COLUMN post_modified datetime not null default '1000-01-01 00:00:00',
MODIFY COLUMN post_modified_gmt datetime not null default '1000-01-01 00:00:00',
ADD hash_tags VARCHAR(252) DEFAULT "",
ADD search_text VARCHAR(252) DEFAULT "";