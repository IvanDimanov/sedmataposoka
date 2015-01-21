/*
  This file will create the basic schema for the project.
  For the inner values please refer to './initial_test_values' folder.
*/


/*Group responsible for the updates made from the administration panel*/
create table admin (
  id                      int not null auto_increment primary key,
  name                    varchar(80)  not null,
  type                    varchar(80)  not null,
  password                varchar(512) not null,
  salt                    varchar(512) not null,
  is_active               boolean default 0,
  access_token_value      varchar(512),
  access_token_created_at int unsigned,
  createdAt               timestamp default current_timestamp
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;


/*Will be used to record all error login attempts from any IP*/
create table error_login (
  id               int not null auto_increment primary key,
  ip               varchar(100) not null,
  count            int unsigned not null,
  last_error_login int unsigned
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;


/*
  Giving all events/subcategories a category to go under.
  Multiple languages are supported for the category name and description.
*/
create table categoryname (
  id int not null auto_increment primary key,
  bg text,
  en text
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

create table categorydescr (
  id int not null auto_increment primary key,
  bg text,
  en text
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

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
create table subcategoryname (
  id int not null auto_increment primary key,
  bg text,
  en text
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

create table subcategorydescr (
  id int not null auto_increment primary key,
  bg text,
  en text
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

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
create table eventtitle (
  id int not null auto_increment primary key,
  bg text,
  en text
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

create table eventdescr (
  id int not null auto_increment primary key,
  bg text,
  en text
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

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
create table thought_author (
  id int not null auto_increment primary key,
  bg text,
  en text
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

create table thought_text (
  id int not null auto_increment primary key,
  bg text,
  en text
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

create table thought (
  id        int not null auto_increment primary key,
  authorId  int not null,
  textId    int not null,
  startDate timestamp,
  endDate   timestamp,

  foreign key (authorId) 
    references thought_author(id)
    on delete cascade
    on update cascade,

  foreign key (textId) 
    references thought_text(id)
    on delete cascade
    on update cascade
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;


/*Different site ads have their images, time-to-live, URL link, and translatable title*/
create table adstitle (
  id int not null auto_increment primary key,
  bg text,
  en text
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

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
create table partnername (
  id int not null auto_increment primary key,
  bg text,
  en text
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;

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
create table ui_labels (
  id          int not null auto_increment primary key,
  language    varchar(12) not null,
  json_labels text not null
) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;