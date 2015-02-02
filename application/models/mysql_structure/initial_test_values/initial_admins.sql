/*Basic and personal users for the administration panel*/
insert into admin
(name         , type         , password                                  , salt              , is_active) values
('7Admin'     , 'super_admin', '2f3008603dedf1f854f8e3bf36b3b527a3fd8a01', '0b5745acf1e6c9'  , 1        ), /*Password: c0380be174a91aeae153dc9115d8cdb8e468e97341bb04234f51b2b39be0ad89 = sha3('7@posoka')*/
('IvanDimanov', 'super_admin', '6c5073a6bf37efa0c9fae8f728e5efc4e7957ec8', '792b4b267c2da11e', 1        ); /*Password: da0d5b797b7c31d56ed318b54213e979adc9b9e12d25f1569ff7ded601338671 = sha3('dimanov$498')*/


/*
Common password + salt query:
  SELECT * FROM admin where password = sha( concat("c0380be174a91aeae153dc9115d8cdb8e468e97341bb04234f51b2b39be0ad89", salt))

where
  c0380be174a91aeae153dc9115d8cdb8e468e97341bb04234f51b2b39be0ad89 = sha3('7@posoka')
*/