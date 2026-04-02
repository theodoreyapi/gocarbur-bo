<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.login');
});
Route::get('index', function () {
    return view('index');
});
Route::get('log', function () {
    return view('pages.activity-logs');
});
Route::get('app-version', function () {
    return view('pages.app-versions');
});
Route::get('articles', function () {
    return view('pages.articles');
});
Route::get('banners', function () {
    return view('pages.banners');
});
Route::get('garages', function () {
    return view('pages.garages');
});
Route::get('notifications', function () {
    return view('pages.notifications');
});
Route::get('partner', function () {
    return view('pages.partner-requests');
});
Route::get('payments', function () {
    return view('pages.payments');
});
Route::get('promotions', function () {
    return view('pages.promotions');
});
Route::get('reviews', function () {
    return view('pages.reviews');
});
Route::get('settings', function () {
    return view('pages.settings');
});
Route::get('stations', function () {
    return view('pages.stations');
});
Route::get('subscriptions', function () {
    return view('pages.subscriptions');
});
Route::get('users', function () {
    return view('pages.users');
});
