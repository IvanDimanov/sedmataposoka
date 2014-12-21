/*
  Hence the complicated foreign key relations removing all tables is not a straightforward job.
  The script below will remove all of them in the correct order
*/

drop table if exists admin;

drop table if exists ads;
drop table if exists adstitle;

drop table if exists partner;
drop table if exists partnername;
drop table if exists ui_labels;

drop table if exists tought;
drop table if exists tought_text;
drop table if exists tought_author;

drop table if exists event;
drop table if exists eventtitle;
drop table if exists eventdescr;

drop table if exists subcategory;
drop table if exists subcategoryname;
drop table if exists subcategorydescr;

drop table if exists category;
drop table if exists categoryname;
drop table if exists categorydescr;