
<?php

// Home page
$app->get('/', 'WF3\Controller\HomeController::homePageAction')->bind('homepage');

//article list with author name
$app->get('/list', 'WF3\Controller\HomeController::listAction')->bind('listWithAuthors');

//author details page
$app->match('/author/{id}', 'WF3\Controller\HomeController::authorAction')->bind('author');

//article details page
$app->get('/article/{id}', 'WF3\Controller\HomeController::articleAction')->bind('article');

//login page
$app->get('/login', 'WF3\Controller\HomeController::loginAction')->bind('login');

//admin homepage
$app->get('/admin', 'WF3\Controller\AdminController::indexAction')->bind('adminHome');

//admin article edit page
$app->match('/admin/edit_article/{id}', 'WF3\Controller\AdminController::editArticleAction')->bind('admin_article_edit');

//admin article delete page
$app->get('/admin/delete_article/{id}', 'WF3\Controller\AdminController::deleteArticleAction')->bind('admin_article_delete');

//admin article add page
$app->match('/admin/add_article', 'WF3\Controller\AdminController::addArticleAction')->bind('admin_article_add');

//admin user edit page
$app->match('/admin/edit_user/{id}', 'WF3\Controller\AdminController::editUserAction')->bind('admin_user_edit');

//admin user delete page
$app->get('/admin/delete_user/{id}', 'WF3\Controller\AdminController::deleteUserAction')->bind('admin_user_delete');

//admin user add page
$app->match('/admin/add_user', 'WF3\Controller\AdminController::addUserAction')->bind('admin_user_add');

//sign in page
$app->match('/sign_in', 'WF3\Controller\HomeController::signInAction')->bind('signin');