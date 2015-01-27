/*Setting a test "Great tough of the month"*/

insert into thought_author
(en, bg) values
('Albert Einstein'  , 'Алберт Айнщаин'),
('Winston Churchill', 'Уинстън Чърчил'),
('Deepak Chopra'    , 'Дийпак Чопра'),
('George Sheehan'   , 'Джордж Шийан'),
('Mark Twain'       , 'Марк Твен');


insert into thought_text
(en, bg) values
("We can't solve problems by using the same kind of thinking we used when we created them.", 'Неможем да разрешим проблемите използвайки същото мислене, когато сме ги създали.'),
('Success is going from failure to failure without losing enthusiasm.', 'Успех е да минаваш от провал към провал без да губиш ентусиазъм.'),
('Success in life could be defined as the continued expansion of happiness and the progressive realization of worthy goals.', 'Успехът в живота може да се определи с постоянното увеличаване на щастието и постигането на заслужаващи си цели.'),
('Success means having the courage, the determination, and the will to become the person you believe you were meant to be.', 'Успехът означава да имаш коражът, устремеността и желанието да станех човека, който мислиш, че трябва да бъдеш.'),
("If you tell the truth, you don't have to remember anything.", 'Ако казваш истинате, не ти трябва да помниш нищо');


insert into thought
(authorId, textId, startDate, endDate) values
(1, 1, '2013-10-10 00:00:00', '2013-10-10 05:00:00'),
(2, 2, '2015-11-11 00:00:00', '2015-11-11 05:00:00'),
(3, 3, '2015-12-12 00:00:00', '2015-12-12 05:00:00'),
(4, 4, '2016-01-01 00:00:00', '2016-01-01 05:00:00'),
(5, 5, '2016-02-02 00:00:00', '2016-02-02 05:00:00');