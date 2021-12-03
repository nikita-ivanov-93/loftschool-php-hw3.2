<?php

class Burger
{
    public function getUser(string $email)
    {
        $db = Db::getInstance();
        $query = "SELECT * FROM users WHERE email = :email";
        return $db->fetchOne($query, __METHOD__, [':email' => $email]);
    }

    public function addUser(string $email, string $name, string $phone)
    {
        $db = Db::getInstance();
        $query = "INSERT INTO users(email, `name`, phone) VALUES (:email, :name, :phone)";
        $result = $db->exec($query, "__METHOD__", [
            ':email' => $email,
            ':name' => $name,
            ':phone' => $phone
        ]);
        if (!$result) return false;

        return $db->lastInsertId();
    }

    public function addOrder(int $userId, array $data)
    {
        $db = Db::getInstance();
//        $query = "INSERT INTO orders(user_id, address, comment, payment, callback, created_at)
//                    VALUES (:user_id, :address, :comment, :payment, :callback, :created_at)";
        $query = "INSERT INTO orders(user_id, address, created_at, comment) VALUES (:user_id, :address, :created_at, :comment)";
        $result = $db->exec(
            $query,
            __METHOD__,
            [
                ':user_id' => $userId,
                ':address' => $data['address'],
                ':comment' => $data['comment'],
                ':created_at' => date('Y-m-d H:i:s'),

            ]

        );
        if (!$result) return false;
        return $db->lastInsertId();
    }

    public function incOrders(int $userId)
    {
        $db = Db::getInstance();
        $query = "UPDATE users SET orders_count = orders_count +1 WHERE id = $userId";
        return $db->exec($query, __METHOD__);
    }
}