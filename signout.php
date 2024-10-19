<?php
session_start();

require_once "./libs/mysql.php";
require_once "./repositories/users.php";
require_once "./usecases/users.php";
require_once "./usecases/auth.php";
require_once "./libs/constants.php";

use Libs\MySQL;
use Repository\UserRepository;
use Usecases\AuthUsecases;
use Usecases\UserUsecases;

$mysql = new MySQL();
$userRepository = new UserRepository($mysql);
$userUsecases = new UserUsecases($userRepository);
$authUsecases = new AuthUsecases($userUsecases);

if ($_SERVER["REQUEST_METHOD"] === "GET") {
  $authUsecases->signout();
  header(REDIRECT_TO_SIGNIN);
}
