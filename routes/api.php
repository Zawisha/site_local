<?php

use App\Http\Controllers\MainController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\PostsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('/test', [MainController::class, 'test']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/is_admin', [AuthController::class, 'is_admin']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/get_posts', [PostsController::class, 'get_posts']);
Route::post('/add_post', [PostsController::class, 'add_post']);
Route::post('/get_post_info', [PostsController::class, 'get_post_info']);
Route::post('/increment_counter', [PostsController::class, 'increment_counter']);
Route::post('/change_anchor', [PostsController::class, 'change_anchor']);
Route::post('/autorization', [\App\Http\Controllers\TelegramAuthController::class, 'autorization']);
Route::post('/get_phones', [\App\Http\Controllers\TelegramUsersController::class, 'get_phones']);
Route::post('/send_code', [\App\Http\Controllers\TelegramAuthController::class, 'send_code']);
Route::post('/get_users', [\App\Http\Controllers\TelegramUsersController::class, 'get_users']);
Route::post('/send_message', [\App\Http\Controllers\TelegramMessageController::class, 'send_message']);
Route::post('/test', [\App\Http\Controllers\TelegramUsersController::class, 'test']);
Route::post('/get_technology', [\App\Http\Controllers\TelegramUsersController::class, 'get_technology']);
Route::post('/get_random_users', [\App\Http\Controllers\TelegramUsersController::class, 'get_random_users']);
Route::post('/get_technology_text', [\App\Http\Controllers\TelegramAdminController::class, 'get_technology_text']);
Route::post('/save_technology_text', [\App\Http\Controllers\TelegramAdminController::class, 'save_technology_text']);
Route::post('/new_save_technology_text', [\App\Http\Controllers\TelegramAdminController::class, 'new_save_technology_text']);
Route::post('/get_count_technology', [\App\Http\Controllers\TelegramUsersController::class, 'get_count_technology']);
Route::post('/save_new_tel_user', [\App\Http\Controllers\TelegramUsersController::class, 'save_new_tel_user']);
Route::post('/add_old_users', [\App\Http\Controllers\TelegramUsersController::class, 'add_old_users']);
Route::post('/dotekanie', [\App\Http\Controllers\TelegramUsersController::class, 'dotekanie']);
Route::post('/add_group_name', [\App\Http\Controllers\TelegramAdminController::class, 'add_group_name']);
Route::post('/get_groups', [\App\Http\Controllers\TelegramAdminController::class, 'get_groups']);
Route::post('/add_phone_to_group', [\App\Http\Controllers\TelegramAdminController::class, 'add_phone_to_group']);
Route::post('/get_group_list', [\App\Http\Controllers\TelegramAdminController::class, 'get_group_list']);
Route::post('/delete_number', [\App\Http\Controllers\TelegramAdminController::class, 'delete_number']);
Route::post('/send_message_to_group', [\App\Http\Controllers\TelegramMessageController::class, 'send_message_to_group']);
Route::post('/add_channel', [\App\Http\Controllers\TelegramAdminController::class, 'add_channel']);
Route::post('/get_channels', [\App\Http\Controllers\TelegramUsersController::class, 'get_channels']);
Route::post('/delete_NO_users', [\App\Http\Controllers\TelegramUsersController::class, 'delete_NO_users']);
Route::post('/invite_users', [\App\Http\Controllers\TelegramUsersController::class, 'invite_users']);
Route::post('/get_telegram_messages', [\App\Http\Controllers\TelegramHistory::class, 'get_telegram_messages']);
Route::post('/add_channel_tech_history', [\App\Http\Controllers\TelegramHistory::class, 'add_channel_tech_history']);
Route::post('/add_word_tech_history', [\App\Http\Controllers\TelegramHistory::class, 'add_word_tech_history']);
Route::post('/clear_directory', [\App\Http\Controllers\TelegramHistory::class, 'clear_directory']);
Route::post('/get_oldest', [\App\Http\Controllers\TelegramHistory::class, 'get_oldest']);
Route::post('/add_old_clients', [\App\Http\Controllers\TelegramHistory::class, 'add_old_clients']);
Route::post('/get_separately_messages', [\App\Http\Controllers\TelegramHistory::class, 'get_separately_messages']);
Route::post('/get_telegram_messages_new_separately', [\App\Http\Controllers\TelegramHistory::class, 'get_telegram_messages_new_separately']);
Route::post('/get_ids_users', [\App\Http\Controllers\TelegramHistory::class, 'get_ids_users']);
Route::post('/send_message_to_group_to_find_cust', [\App\Http\Controllers\TelegramMessageController::class, 'send_message_to_group_to_find_cust']);
Route::post('/get_mad', [\App\Http\Controllers\TestController::class, 'get_mad']);
Route::post('/get_processing', [\App\Http\Controllers\ProcessingController::class, 'get_processing']);
Route::post('/delete_cust', [\App\Http\Controllers\ProcessingController::class, 'delete_cust']);
Route::post('/add_cust', [\App\Http\Controllers\ProcessingController::class, 'add_cust']);
Route::post('/send_to_telegram', [\App\Http\Controllers\VkController::class, 'send_to_telegram']);
Route::post('/add_vk_group_to_channel', [\App\Http\Controllers\VkController::class, 'add_vk_group_to_channel']);
Route::post('/getVKusers', [\App\Http\Controllers\VkController::class, 'getVKusers']);
Route::post('/get_list_of_search_group', [\App\Http\Controllers\VkController::class, 'get_list_of_search_group']);
Route::post('/send_inv_message', [\App\Http\Controllers\VkController::class, 'send_inv_message']);
Route::post('/send_inv_message_second', [\App\Http\Controllers\VkController::class, 'send_inv_message_second']);
Route::post('/showVKusers', [\App\Http\Controllers\VkController::class, 'showVKusers']);
Route::post('/deleteshowVKusers', [\App\Http\Controllers\VkController::class, 'deleteshowVKusers']);
Route::post('/get_vk_text_buy', [\App\Http\Controllers\VkController::class, 'get_vk_text_buy']);

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});
