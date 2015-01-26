/*Create top level demo categories*/
insert into categoryname
(en, bg) values
('Books' , 'Книги'),
('Yoga'  , 'Йога' ),
('Dances', 'Танци');

insert into categorydescription
(en, bg) values
('Reading books is a great way to step deep into new endeavors of you imagination.'                                                                        , 'Четенето на книги е великолепен начин за достъпване на дълбините на вашето въображение.'),
('Yoga is the physical, mental, and spiritual practices or disciplines which originated in ancient India with a view to attain a state of permanent peace.', 'Йога е една от шестте ведически философски школи, която приема медитацията за основно средство за постигане на освобождението.'),
('Dance is a type of art that generally involves movement of the body, often rhythmic and to music.'                                                       , 'Танцът е вид изкуство, при който средства за създаване на художествен образ са движението и смяната на положението на човешкото тяло.');

insert into category
(imagePath            , nameId, descriptionId) values
('category/books.png' , 1     , 1            ),
('category/yoga.png'  , 2     , 2            ),
('category/dances.png', 3     , 3            );