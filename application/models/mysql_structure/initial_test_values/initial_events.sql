/*
  Will use all of the information from 'initial_categories.sql' and 'initial_subcategories.sql'
  in order to create the event model described in the 'readme.md'
*/

insert into eventtitle
(en, bg) values

/*Books: Reading events name*/
('Reading of all times favorite "Jack & Jill"', 'Четене на любимата на всички времена "Джак и Джил"'),
('Poems from "The other Earth"', 'Поеми от "Другата Земя"'),

/*Books: Presentation events name*/
('James Brown will give us a tour of his "How to find Mitchel"', 'Джеймс Браун ще ни разведе из неговият "Как да намериш Митчъл"'),
('"Jobs" new bio', '"Jobs" новата биография'),

/*Yoga: Ashtanga events name*/
('Shir Svaminarayam, best practices', 'Шри Сваминарачям, най-добри практики'),
('US Yoga in action', 'Щатска йога'),

/*Yoga: Hatha events name*/
('In peace with master Gupta', 'В мир с учителя Гупта'),
('Basics class', 'Основен клас'),

/*Yoga: Vinyasa events name*/
('All Clubs Presentation', 'Пресентазия на всички клубове'),
('Ukraine republic champion tells of her success', 'Украинската републиканска шампионка ще разкаже за своя успеш'),

/*Dances: HipHop events name*/
('Snooping club presents its Bests', 'Snooping клубът ще представи своите най-добри'),
('HH Plovdiv Bests', 'Най-добрите от Пловдив HH'),

/*Dances: Break events name*/
('Club Sailus review', 'Клуб Sailus представя'),
('Basics from BreakBoys club', 'Основите според клуб BreakBoys'),

/*Dances: Salsa events name*/
('Cuba winners course #1', 'Курс #1 от кубинските победителите'),
('Modern salsa music bazar', 'Базар на модерна салса музика'),

/*Dances: Tango events name*/
('Argento Dance Club Q&A cruise', 'Въпроси и отговори с клуб Argento'),
('Sofia Dance Week presentation', 'Sofia Dance Week представя');


insert into eventdescr
(en, bg) values

/*Books: Reading events description*/
('Test event description for event name: Reading of all times favorite "Jack & Jill". Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'Тестово описание на събитието: Четене на любимата на всички времена "Джак и Джил" Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'),
('Test event description for event name: Poems from "The other Earth". Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'Тестово описание на събитието: Поеми от "Другата Земя" Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'),

/*Books: Presentation events description*/
('Test event description for event name: James Brown will give us a tour of his "How to find Mitchel". Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'Тестово описание на събитието: Джеймс Браун ще ни разведе из неговият "Как да намериш Митчъл" Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'),
('Test event description for event name: "Jobs" new bio. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'Тестово описание на събитието: "Jobs" новата биография.  Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'),

/*Yoga: Ashtanga events description*/
('Test event description for event name: Shir Svaminarayam, best practices. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'Тестово описание на събитието: Шри Сваминарачям, най-добри  практикиLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'),
('Test event description for event name: US Yoga in action. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'Тестово описание на събитието: Щатска  йогаLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'),

/*Yoga: Hatha events description*/
('Test event description for event name: In peace with master Gupta. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'Тестово описание на събитието: В мир с учителя  ГуптаLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'),
('Test event description for event name: Basics class. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'Тестово описание на събитието: Основен  класLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'),

/*Yoga: Vinyasa events description*/
('Test event description for event name: All Clubs Presentation. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'Тестово описание на събитието: Пресентазия на всички  клубовеLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'),
('Test event description for event name: Ukraine republic champion tells of her success. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'Тестово описание на събитието: Украинската републиканска шампионка ще разкаже за своя  успешLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'),

/*Dances: HipHop events description*/
('Test event description for event name: Snooping club presents its Bests. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'Тестово описание на събитието: Snooping клубът ще представи своите най- добриLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'),
('Test event description for event name: HH Plovdiv Bests. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'Тестово описание на събитието: Най-добрите от Пловдив  HHLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'),

/*Dances: Break events description*/
('Test event description for event name: Club Sailus review. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'Тестово описание на събитието: Клуб Sailus  представяLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'),
('Test event description for event name: Basics from BreakBoys club. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'Тестово описание на събитието: Основите според клуб  BreakBoysLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'),

/*Dances: Salsa events description*/
('Test event description for event name: Cuba winners course #1. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'Тестово описание на събитието: Курс #1 от кубинските  победителитеLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'),
('Test event description for event name: Modern salsa music bazar. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'Тестово описание на събитието: Базар на модерна салса  музикаLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'),

/*Dances: Tango events description*/
('Test event description for event name: Argento Dance Club Q&A cruise. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'Тестово описание на събитието: Въпроси и отговори с клуб  ArgentoLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'),
('Test event description for event name: Sofia Dance Week presentation. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', 'Тестово описание на събитието: Sofia Dance Week  представяLorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.');


insert into event
(titleId, descrId, subcatId, link, fee, startDate, endDate) values

/*The below code will set events evenly in time by the interval from now to every 4 days*/

/*Books: Reading events*/
(1, 1, 1, 'http://google.com', '20 BGN/лв.', CURRENT_DATE, ADDDATE(CURRENT_DATE, INTERVAL 4 DAY)),
(2, 2, 1, 'http://google.com', '20 BGN/лв.', CURRENT_DATE, ADDDATE(CURRENT_DATE, INTERVAL 4 DAY)),

/*Books: Presentation events*/
(3, 3, 2, 'http://google.com', '20 BGN/лв.', ADDDATE(CURRENT_DATE, INTERVAL 4 DAY), ADDDATE(CURRENT_DATE, INTERVAL 8 DAY)),
(4, 4, 2, 'http://google.com', '20 BGN/лв.', ADDDATE(CURRENT_DATE, INTERVAL 4 DAY), ADDDATE(CURRENT_DATE, INTERVAL 8 DAY)),

/*Yoga: Ashtanga events*/
(5, 5, 3, 'http://google.com', '20 BGN/лв.', ADDDATE(CURRENT_DATE, INTERVAL 8 DAY), ADDDATE(CURRENT_DATE, INTERVAL 12 DAY)),
(6, 6, 3, 'http://google.com', '20 BGN/лв.', ADDDATE(CURRENT_DATE, INTERVAL 8 DAY), ADDDATE(CURRENT_DATE, INTERVAL 12 DAY)),

/*Yoga: Hatha events*/
(7, 7, 4, 'http://google.com', '20 BGN/лв.', ADDDATE(CURRENT_DATE, INTERVAL 12 DAY), ADDDATE(CURRENT_DATE, INTERVAL 16 DAY)),
(8, 8, 4, 'http://google.com', '20 BGN/лв.', ADDDATE(CURRENT_DATE, INTERVAL 12 DAY), ADDDATE(CURRENT_DATE, INTERVAL 16 DAY)),

/*Yoga: Vinyasa events*/
( 9,  9, 5, 'http://google.com', '20 BGN/лв.', ADDDATE(CURRENT_DATE, INTERVAL 16 DAY), ADDDATE(CURRENT_DATE, INTERVAL 20 DAY)),
(10, 10, 5, 'http://google.com', '20 BGN/лв.', ADDDATE(CURRENT_DATE, INTERVAL 16 DAY), ADDDATE(CURRENT_DATE, INTERVAL 20 DAY)),

/*Dances: HipHop events*/
(11, 11, 6, 'http://google.com', '20 BGN/лв.', ADDDATE(CURRENT_DATE, INTERVAL 20 DAY), ADDDATE(CURRENT_DATE, INTERVAL 24 DAY)),
(12, 12, 6, 'http://google.com', '20 BGN/лв.', ADDDATE(CURRENT_DATE, INTERVAL 20 DAY), ADDDATE(CURRENT_DATE, INTERVAL 24 DAY)),

/*Dances: Break events*/
(13, 13, 7, 'http://google.com', '20 BGN/лв.', ADDDATE(CURRENT_DATE, INTERVAL 24 DAY), ADDDATE(CURRENT_DATE, INTERVAL 28 DAY)),
(14, 14, 7, 'http://google.com', '20 BGN/лв.', ADDDATE(CURRENT_DATE, INTERVAL 24 DAY), ADDDATE(CURRENT_DATE, INTERVAL 28 DAY)),

/*Dances: Salsa events*/
(15, 15, 8, 'http://google.com', '20 BGN/лв.', ADDDATE(CURRENT_DATE, INTERVAL 32 DAY), ADDDATE(CURRENT_DATE, INTERVAL 36 DAY)),
(16, 16, 8, 'http://google.com', '20 BGN/лв.', ADDDATE(CURRENT_DATE, INTERVAL 32 DAY), ADDDATE(CURRENT_DATE, INTERVAL 36 DAY)),

/*Dances: Tango events*/
(17, 17, 9, 'http://google.com', '20 BGN/лв.', ADDDATE(CURRENT_DATE, INTERVAL 40 DAY), ADDDATE(CURRENT_DATE, INTERVAL 44 DAY)),
(18, 18, 9, 'http://google.com', '20 BGN/лв.', ADDDATE(CURRENT_DATE, INTERVAL 40 DAY), ADDDATE(CURRENT_DATE, INTERVAL 44 DAY));