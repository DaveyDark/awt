<?php

session_start();
session_destroy();
http_response_code(200);
header("Location: ../login");
