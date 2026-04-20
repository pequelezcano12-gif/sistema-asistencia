<?php
ob_start();
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 0);
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../routes/web.php';
