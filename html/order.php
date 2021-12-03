<?php

include '../src/config.php';
include '../src/class.db.php';
include '../src/class.burger.php';

$burger = new Burger();

$email = $_POST['email'];
$name = $_POST['name'];
$phone = $_POST['phone'];

$addressFields = ['street', 'home', 'part', 'appt', 'floor'];
$address = '';
foreach ($_POST as $field => $value) {
    if ($value && in_array($field, $addressFields)) {
        $address .= $value . ',';
    }
}
$data =
    [
        'address' => $address,
        'comment' => $_POST['comment']
    ];

$user = $burger->getUser($email);

if ($user) {
    $userId = $user['id'];
    $burger->incOrders($user['id']);
    $orderNumber = $user['orders_count'] + 1;
} else {
    $orderNumber = 1;
    $userId = $burger->addUser($email, $name, $phone);
}

$orderId = $burger->addOrder($userId, $data);

echo "Спасибо, ваш заказ будет доставлен по адресу: $address<br>
Номер вашего заказа: #$orderId <br>
Это ваш $orderNumber-й заказ!";