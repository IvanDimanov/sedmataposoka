/*Set some testing banners and ads*/

insert into adstitle
(en, bg) values
('Test banner 1', 'Тестови баннер 1' ),
('Test banner 2', 'Тестови баннер 2' ),
('Test add 1'   , 'Тестова реклама 1'),
('Test add 1'   , 'Тестова реклама 2');


insert into ads
(imagePath            , link                         , titleId, type, startDate   , endDate     ) values
('banner/banner_1.jpg', 'http://test_banner_link.com', 1      , 1   , '2013-11-16', '2013-12-16'),
('banner/banner_2.jpg', 'http://test_banner_link.com', 2      , 1   , '2013-11-16', '2013-12-16'),
('ads/add_1.jpg'      , 'http://test_add_link.com'   , 3      , 2   , '2013-11-16', '2013-12-16'),
('ads/add_2.jpg'      , 'http://test_add_link.com'   , 4      , 2   , '2013-11-16', '2013-12-16');