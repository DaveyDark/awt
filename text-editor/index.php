<?php
session_start();

if (isset($_SESSION['te_user_id'])) {
  header("Location: home");
} else {
  header("Location: login");
}
