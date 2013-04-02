mysql> EXPLAIN EXTENDED
    -> SELECT count(DISTINCT(articleID)) FROM writesArticle WHERE userID = 6;
+----+-------------+---------------+------+---------------+--------------+---------+-------+------+----------+-------------+
| id | select_type | table         | type | possible_keys | key          | key_len | ref   | rows | filtered | Extra       |
+----+-------------+---------------+------+---------------+--------------+---------+-------+------+----------+-------------+
|  1 | SIMPLE      | writesArticle | ref  | userid_index  | userid_index | 4       | const |    1 |   100.00 | Using index |
+----+-------------+---------------+------+---------------+--------------+---------+-------+------+----------+-------------+
1 row in set, 1 warning (0.00 sec)

mysql> EXPLAIN EXTENDED
    -> SELECT articleID FROM articleKeyword WHERE keyword LIKE 'this' ORDER BY occurences DESC;
+----+-------------+----------------+------+---------------+------+---------+------+------+----------+-----------------------------+
| id | select_type | table          | type | possible_keys | key  | key_len | ref  | rows | filtered | Extra                       |
+----+-------------+----------------+------+---------------+------+---------+------+------+----------+-----------------------------+
|  1 | SIMPLE      | articleKeyword | ALL  | NULL          | NULL | NULL    | NULL |  345 |   100.00 | Using where; Using filesort |
+----+-------------+----------------+------+---------------+------+---------+------+------+----------+-----------------------------+
1 row in set, 1 warning (0.00 sec)

mysql> EXPLAIN EXTENDED
    -> SELECT userID from user WHERE username = 'chirag'
    -> ;
+----+-------------+-------+-------+-------------------------+----------+---------+-------+------+----------+-------------+
| id | select_type | table | type  | possible_keys           | key      | key_len | ref   | rows | filtered | Extra       |
+----+-------------+-------+-------+-------------------------+----------+---------+-------+------+----------+-------------+
|  1 | SIMPLE      | user  | const | username,username_index | username | 32      | const |    1 |   100.00 | Using index |
+----+-------------+-------+-------+-------------------------+----------+---------+-------+------+----------+-------------+
1 row in set, 1 warning (0.00 sec)

mysql> show create table articleKeyword;
+----------------+-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| Table          | Create Table                                                                                                                                                                                                                                                                                                                                                      |
+----------------+-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| articleKeyword | CREATE TABLE `articleKeyword` (
  `articleID` int(5) NOT NULL,
  `keyword` varchar(100) NOT NULL,
  `occurences` int(2) NOT NULL DEFAULT '1',
  PRIMARY KEY (`articleID`,`keyword`),
  CONSTRAINT `articleKeyword_ibfk_1` FOREIGN KEY (`articleID`) REFERENCES `article` (`articleID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 |
+----------------+-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
1 row in set (0.00 sec)

mysql> USE KEY PRIMARY;
ERROR 1044 (42000): Access denied for user 'mchirag'@'localhost' to database 'KEY'
mysql> USE KEY (PRIMARY);
ERROR 1044 (42000): Access denied for user 'mchirag'@'localhost' to database 'KEY'
mysql> USE INDEX (PRIMARY);
ERROR 1044 (42000): Access denied for user 'mchirag'@'localhost' to database 'INDEX'
mysql> show index from articleKeyword;
+----------------+------------+----------+--------------+-------------+-----------+-------------+----------+--------+------+------------+---------+---------------+
| Table          | Non_unique | Key_name | Seq_in_index | Column_name | Collation | Cardinality | Sub_part | Packed | Null | Index_type | Comment | Index_comment |
+----------------+------------+----------+--------------+-------------+-----------+-------------+----------+--------+------+------------+---------+---------------+
| articleKeyword |          0 | PRIMARY  |            1 | articleID   | A         |           2 |     NULL | NULL   |      | BTREE      |         |               |
| articleKeyword |          0 | PRIMARY  |            2 | keyword     | A         |         345 |     NULL | NULL   |      | BTREE      |         |               |
+----------------+------------+----------+--------------+-------------+-----------+-------------+----------+--------+------+------------+---------+---------------+
2 rows in set (0.00 sec)

mysql> EXPLAIN EXTENDED
    -> SELECT userID, comment, writtenAt, commentID FROM comment C WHERE postID = 5 AND NOT EXISTS (SELECT * FROM commentThread CT WHERE CT.childID = C.commentID) ORDER BY writtenAt;
+----+--------------------+-------+------+-----------------+---------+---------+------------------------------+------+----------+-----------------------------+
| id | select_type        | table | type | possible_keys   | key     | key_len | ref                          | rows | filtered | Extra                       |
+----+--------------------+-------+------+-----------------+---------+---------+------------------------------+------+----------+-----------------------------+
|  1 | PRIMARY            | C     | ref  | postID          | postID  | 4       | const                        |    1 |   100.00 | Using where; Using filesort |
|  2 | DEPENDENT SUBQUERY | CT    | ref  | PRIMARY,childID | PRIMARY | 4       | mchirag_wikiblog.C.commentID |    1 |   100.00 | Using index                 |
+----+--------------------+-------+------+-----------------+---------+---------+------------------------------+------+----------+-----------------------------+
2 rows in set, 2 warnings (0.00 sec)

mysql> explain extended
    -> SELECT userID FROM likesDislikes WHERE postID = 5 AND choice = 1;
+----+-------------+---------------+------+---------------+------+---------+------+------+----------+-------------+
| id | select_type | table         | type | possible_keys | key  | key_len | ref  | rows | filtered | Extra       |
+----+-------------+---------------+------+---------------+------+---------+------+------+----------+-------------+
|  1 | SIMPLE      | likesDislikes | ALL  | postID        | NULL | NULL    | NULL |    5 |    80.00 | Using where |
+----+-------------+---------------+------+---------------+------+---------+------+------+----------+-------------+
1 row in set, 1 warning (0.00 sec)

mysql> explain extended
    -> SELECT count(*) from follows WHERE followee = 6
    -> ;
+----+-------------+---------+------+---------------+----------+---------+-------+------+----------+-------------+
| id | select_type | table   | type | possible_keys | key      | key_len | ref   | rows | filtered | Extra       |
+----+-------------+---------+------+---------------+----------+---------+-------+------+----------+-------------+
|  1 | SIMPLE      | follows | ref  | followee      | followee | 4       | const |    2 |   100.00 | Using index |
+----+-------------+---------+------+---------------+----------+---------+-------+------+----------+-------------+
1 row in set, 1 warning (0.01 sec)

mysql> exit
