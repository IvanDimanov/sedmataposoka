/*Set all describing values for the most bottom level of categorization */
insert into subcategoryname
(en, bg) values

/*Books subcategory names*/
('Reading'     , 'Четене'     ),
('Presentation', 'Представяне'),

/*Yoga subcategory names*/
('Ashtanga', 'Ащанга'),
('Hatha'   , 'Хатха' ),
('Vinyasa' , 'Виняса'),

/*Dances subcategory names*/
('HipHop', 'Хип-хоп'),
('Break' , 'Брейк'  ),
('Salsa' , 'Салса'  ),
('Tango' , 'Танго'  );


insert into subcategorydescr
(en, bg) values

/*Books subcategory descriptions*/
('Reading the top best sellers in front of an audience.', 'Четене на нах-продаваните книги пред публика.'),
('Presenting books that came on the shelfs recently.', 'Представяне на книгите излезли скоро на продажба.'),

/*Yoga subcategory descriptions*/
('Ashtanga Yoga, is a style of yoga founded and popularized by K. Pattabhi Jois, and which is often promoted as a modern-day form of classical Indian yoga.', 'Ащанга йога, е стил на йога основана и популяризирана от K. Pattabhi Jois, и които често се насърчава като съвременна форма на класическата индийска йога.'),
('Hatha yoga, also called hatha vidya, is a kind of yoga focusing on physical and mental strength building exercises and postures described primarily in three texts of Hinduism', 'Хáтха йога или още на български често наричана Хáта йога е вид Йога, клон на Раджа йога, създадена през XV век от мъдрецът Йоги Сватмарама и описана от него в съчинението Хатха Йога Прадипика (прадипика означава буквално "това, което хвърля светлина").'),
('Vinyasa is a Sanskrit term often employed in relation to certain styles of yoga.', 'Виняса е санскритски термин често се използва във връзка с определени стилове на йога.'),

/*Dances subcategory descriptions*/
('Hip hop is a broad conglomerate of artistic forms that originated within a marginalized subculture in the South Bronx amongst black and latino youth during the 1970s in New York City.', 'Хип-хоп е течение в културата, което възниква през 60-те и 70-те години в Ню Йорк, в средите на афроамериканците и латиноамериканците'),
('In popular music, a break is an instrumental or percussion section or interlude during a song derived from or related to stop-time – being a "break" from the main parts of the song or piece.', 'В популярната музика, а почивката е инструментален или ударни раздел или антракт по време на песента, получени от или свързани с стоп-време - е "почивка" от основните части на песен или част.'),
('Salsa is a dance form with origins from the Cuban Son (circa 1920s) and Afro-Cuban dance (specifically Afro-Cuban Rumba).', 'Салса (исп. salsa) е танц по двойки който се изпълнява на салса музика. Понякога се танцува и самостоятелно.'),
('The tango is a partner dance that originated in the 1890s along the Río de la Plata, the natural border between Argentina and Uruguay, and soon spread to the rest of the world.', 'Танго̀ (на повечето езици се произнася с ударение на "а", та̀нго, в България и Франция се среща и с ударение на "о") е музикален жанр - песен, текст и танц.');


insert into subcategory
(catId, pictureSrc, nameId, descrId) values

/*Books subcategories*/
(1, 'subcategory/books_reading.png'     , 1, 1),
(1, 'subcategory/books_presentation.png', 2, 2),

/*Yoga subcategories*/
(2, 'subcategory/yoga_ashtanga.png', 3, 3),
(2, 'subcategory/yoga_hatha.png'   , 4, 4),
(2, 'subcategory/yoga_vinyasa.png' , 5, 5),

/*Dances subcategories*/
(3, 'subcategory/dances_hiphop.png', 6, 6),
(3, 'subcategory/dances_break.png' , 7, 7),
(3, 'subcategory/dances_salsa.png' , 8, 8),
(3, 'subcategory/dances_tango.png' , 9, 9);