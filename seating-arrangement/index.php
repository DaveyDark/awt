<?php
session_start();
if (isset($_SESSION['sa_user_id'])) {
  header("Location: admin");
  exit();
}
header("Location: home");
