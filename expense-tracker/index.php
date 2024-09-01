<?php
// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
  header("Location: login");
  exit();
} else {
  // Redirect to dashboard if user is logged in
  header("Location: dashboard");
  exit();
}
