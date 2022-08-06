<?php
$settings = [
'app_info' => [ // Эти данные мы получили после регистрации приложения на https://my.telegram.org
'api_id' => env('TELEGRAM_API_ID'),
'api_hash' => env('TELEGRAM_API_HASH'),
'device_model'=>'Desktop',
],
'logger' => [ // Вывод сообщений и ошибок
'logger' => 3, // выводим сообещения через echo
'logger_level' => 4, // выводим только критические ошибки.
],
'serialization' => [
'serialization_interval' => 300,
//Очищать файл сессии от некритичных данных.
//Значительно снижает потребление памяти при интенсивном использовании, но может вызывать проблемы
'cleanup_before_serialization' => true,
],
];
$MadelineProto = new \danog\MadelineProto\API('session.madeline', $settings);
$MadelineProto->start();
