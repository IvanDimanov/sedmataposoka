/*
  This file will create the basic schema for the project.
  For the inner values please refer to './initial_test_values' folder.
*/


/*Group responsible for the updates made from the administration panel*/
drop table if exists admin;
create table admin (
  id        int not null auto_increment primary key,
  name      varchar(80)  not null,
  pass      varchar(512) not null,
  salt      varchar(512) not null,
  createdAt timestamp default current_timestamp
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;


/*
  Giving all events/subcategories a category to go under.
  Multiple languages are supported for the category name and description.
*/
drop table if exists categoryname;
create table categoryname (
  id int not null auto_increment primary key,
  bg text,
  en text
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

drop table if exists categorydescr;
create table categorydescr (
  id int not null auto_increment primary key,
  bg text,
  en text
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

drop table if exists category;
create table category (
  id         int not null auto_increment primary key,
  pictureSrc varchar(150) not null,
  nameId     int not null,
  descrId    int not null,
  createdAt  timestamp default current_timestamp,

  foreign key (nameId) 
    references categoryname(id)
    on delete cascade
    on update cascade,

  foreign key (descrId) 
    references categorydescr(id)
    on delete cascade
    on update cascade
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;


/*
  There could be times when basic categories fork into subcategories,
  catID will present the relation between them.
*/
drop table if exists subcategoryname;
create table subcategoryname (
  id int not null auto_increment primary key,
  bg text,
  en text
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

drop table if exists subcategorydescr;
create table subcategorydescr (
  id int not null auto_increment primary key,
  bg text,
  en text
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

drop table if exists subcategory;
create table subcategory (
  id         int not null auto_increment primary key,
  catId      int not null,
  pictureSrc varchar(150),
  nameId     int not null,
  descrId    int not null,
  createdAt  timestamp default current_timestamp,

  foreign key (catId) 
    references category(id)
    on delete cascade
    on update cascade,

  foreign key (nameId) 
    references subcategoryname(id)
    on delete cascade
    on update cascade,

  foreign key (descrId) 
    references subcategorydescr(id)
    on delete cascade
    on update cascade
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;


/*All important event information with translatable title and description*/
drop table if exists eventtitle;
create table eventtitle (
  id int not null auto_increment primary key,
  bg text,
  en text
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

drop table if exists eventdescr;
create table eventdescr (
  id int not null auto_increment primary key,
  bg text,
  en text
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

drop table if exists event;
create table event (
  id        int not null auto_increment primary key,
  titleId   int not null,
  descrId   int not null,
  subcatId  int not null,
  link      varchar(150),
  fee       varchar(50), /*Not int in order to keep currency*/
  startDate timestamp,
  endDate   timestamp,

  foreign key (titleId) 
    references eventtitle(id)
    on delete cascade
    on update cascade,

  foreign key (titleId) 
    references eventtitle(id)
    on delete cascade
    on update cascade,

  foreign key (subcatId) 
    references subcategory(id)
    on delete cascade
    on update cascade
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;


/*
  Tough of the day (week or month, etc.) is been
  presented by its text content and author.
  Both translatable.
*/
drop table if exists tought_author;
create table tought_author (
  id int not null auto_increment primary key,
  bg text,
  en text
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

drop table if exists tought_text;
create table tought_text (
  id int not null auto_increment primary key,
  bg text,
  en text
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

drop table if exists tought;
create table tought (
  id        int not null auto_increment primary key,
  authorId  int not null,
  textId    int not null,
  startDate timestamp,
  endDate   timestamp,

  foreign key (authorId) 
    references tought_author(id)
    on delete cascade
    on update cascade,

  foreign key (textId) 
    references tought_text(id)
    on delete cascade
    on update cascade
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;


/*Different site ads have their images, time-to-live, URL link, and translatable title*/
drop table if exists adstitle;
create table adstitle (
  id int not null auto_increment primary key,
  bg text,
  en text
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

drop table if exists ads;
create table ads (
  id        int not null auto_increment primary key,
  imagePath varchar(150) not null,
  link      varchar(150),
  titleId   int not null,
  type      int not null,
  startDate timestamp,
  endDate   timestamp,

  foreign key (titleId) 
    references adstitle(id)
    on delete cascade
    on update cascade
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;


/*Every site partner can have its log and translatable name*/
drop table if exists partnername;
create table partnername (
  id int not null auto_increment primary key,
  bg text,
  en text
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

drop table if exists partner;
create table partner (
  id      int not null auto_increment primary key,
  logoSrc varchar(150) not null,
  link    varchar(150),
  nameId  int not null,

  foreign key (nameId) 
    references partnername(id)
    on delete cascade
    on update cascade
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;


/*Will hold each page specific labels per language as JSON*/
drop table if exists ui_labels;
create table ui_labels (
  id          int not null auto_increment primary key,
  language    varchar(12) not null,
  json_labels text not null
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;