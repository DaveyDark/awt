<?php
session_start();
if (isset($_SESSION['sta_user_id'])) {
  header("Location: home");
  exit();
}
header("Location: login");
