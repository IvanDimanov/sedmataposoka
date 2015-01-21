/*Basic and personal users for the administration panel*/
insert into admin
(name         , type         , password                                  , salt              , is_active) values
('7Admin'     , 'super_admin', '6828d28372d2b369efefbbf75dcb58a49ba0df67', '0b5745acf1e6c9'  , 1        ), /*Password: 6c730ae5d030587ee60254aa4d0eb4174f9e8b4fc4a8cf59e5388cba396c77af = sha3('7@posoka')*/
('IvanDimanov', 'super_admin', '6c5073a6bf37efa0c9fae8f728e5efc4e7957ec8', '792b4b267c2da11e', 1        ); /*Password: da0d5b797b7c31d56ed318b54213e979adc9b9e12d25f1569ff7ded601338671 = sha3('dimanov$498')*/


/*
Common password + salt query:
  SELECT * FROM admin where password = sha( concat("6c730ae5d030587ee60254aa4d0eb4174f9e8b4fc4a8cf59e5388cba396c77af", salt))

where
  6c730ae5d030587ee60254aa4d0eb4174f9e8b4fc4a8cf59e5388cba396c77af = sha3('7@posoka')
*/