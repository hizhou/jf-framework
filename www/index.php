<?php
require_once './config.php';


/*
 * front controller init
 */
require_once 'JFramework/Controller/jfFrontController.php';
require_once 'JFramework/Controller/Action/jfAction.php';
$fontCtrl = jfFrontController::getInstance();
/* @var $fontCtrl jfFrontController */

$fontCtrl->throwExceptions(false);
$fontCtrl->addModuleDirectory(dirname(__FILE__) . '/../app/modules');
$fontCtrl->dispatch();


