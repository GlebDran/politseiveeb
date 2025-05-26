<?php
// Вводим тестовый пароль
$parool = '12345';
$krypt = // Генерируем хеш пароля с использованием алгоритма bcrypt
password_hash($parool, PASSWORD_DEFAULT);
// Выводим готовый хеш
echo $krypt;