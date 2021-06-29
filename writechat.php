<?php
    session_start();
    if(isset($_SESSION["user"])){
        $text = $_POST["text"];
        date_default_timezone_set("Asia/Taipei");
        $date = date("H:i:s");
        $fp = fopen("message.html", "a+");

        $msg = "<div class='message'>\n<span class='name'>" . $_SESSION["user"] . "</span>:\t" . stripslashes(htmlspecialchars($text)) . "<span class='right'>" . $date . "</span>\n</div>\n\n";
        fwrite($fp, $msg);
        fclose($fp);
    }
?>