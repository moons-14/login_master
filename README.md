# DBは以下のコマンドで作成できます

```sql
CREATE TABLE `user_data` (
    `user_id` int(20) NOT NULL AUTO_INCREMENT,
    `user_name` text NOT NULL,
    `user_email` text NOT NULL,
    `email_token` text NOT NULL,
    `user_password` text NOT NULL,
    PRIMARY KEY (`user_id`),
    UNIQUE KEY `user_name` (`user_name`) USING HASH,
    UNIQUE KEY `user_email` (`user_email`) USING HASH
   ) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4
```
