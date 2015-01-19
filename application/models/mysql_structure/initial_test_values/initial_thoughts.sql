/*Setting a test "Great tough of the month"*/

insert into thought_author
(en, bg) values
('Albert Einstein', 'Алберт Айнщаин');

insert into thought_text
(en, bg) values
("We can't solve problems by using the same kind of thinking we used when we created them.", 'Неможем да разрешим проблемите използвайки същото мислене, когато сме ги създали.');

insert into thought
(authorId, textId, startDate, endDate) values
(1, 1, '2013-11-16', '2013-12-16');