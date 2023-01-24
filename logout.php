<?php
    session_name("chronotaches");
    session_start();

    session_destroy();
    header("Location: ./login.php");