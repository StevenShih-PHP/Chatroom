<?php
    session_start();            //開啟session功能(可以暫時將登入資料或購物車等「用戶資料」暫時存在伺服器端)
    if(isset($_GET["user"]))
        $user = $_GET["user"];  //取得表單中的值(和$_POST不同的是，$_GET的值會保留在URL的暫存裡，安全性較低，且不能超過100個字符。)
    else
        $user = "";

    if($user != ""){
        if($user == "logout"){
            $name = $_SESSION["user"];  //取得session內的值
            $msg = "<div class='sign'>使用者:<span class='name'>" . $name . "</span>離開聊天室</div>\n\n";

            $fp = fopen("message.html", "a+");      //a+也是一種讀寫模式，只是安全性較高，在沒有檔案時也會先創建
            fwrite($fp, $msg);
            fclose($fp);
            session_destroy();                  //清除session
            header("Location:index.php");       //header有許多用法，此處用途為轉址給自己
        }

        if(!isset($_SESSION["user"])){
            $_SESSION["user"] = $user;
            $fp = fopen("message.html", "a+");
            $string = "<div class='sign'>使用者:<span class='name'>" . $user . "</span>進入聊天室</div>\n\n";
            fwrite($fp, $string);
            fclose($fp);
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajax聊天室</title>
    <link rel="stylesheet" href="style.css">
    <script src="jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function(){                   //jQuery起手式
            $("form#chat-form").submit(function(e){     //提交表單時，會觸發submit事件，該表單的id為chat-form
                e.preventDefault();                     //防止陌生行為發生
                var message = $("#chatmessage").val();  //val會返回被選定元素的值，此處會返回id為chatmessage的值

                $.post("writechat.php", {text: message});      //透過ajax去請求伺服器提供數據(類似PHP的$_POST)，$.post(url/必:被加載的網址, data/選:發送到伺服器的數據, fn/選:請求成功後回傳的函數, type/選:伺服器回傳的格式)
                $("#chatmessage").val('');                     //此處意指加載到writechat.php，要發送的內容為message，格式為text

                return false;
            });

            function loadContent(){
                var oldHeight = $("#chatwindow").prop("scrollHeight") - 20;     //回傳id為chatwindow的標籤中scrollHeight的值，scrollHeight指的是整份網頁的高度

                $.ajax({                    //ajax: 透過http協議請求，在同一個頁面中加載遠端數據
                    url:"message.html",     //url: ajax的參數之一，表示要發送的目的地
                    cache: false,           //cache: ajax的參數之一，預設值為true，設定為false後，jQuery在我們每次發出Request時，會補上一個參數"_"，而其內容是每次皆不同的亂數，這是Javascript端很常見的迴避Cache技巧。由於參數值不相同，每次Request都被視為不同，就能避開Cache裡的舊資料，強迫每次都將Request送至Server端執行。(簡單來說，安全性較高)
                    success: function(content){ //success: ajax的參數之一，請求成功後回傳的函數
                        $("#chatwindow").html(content);      //將html中的特定標籤(此處為id=chatwindow的標籤)內容改變(變成content)
                        var newHeight = $("#chatwindow").prop("scrollHeight") - 20;

                        if(newHeight > oldHeight){
                            $("#chatwindow").animate({scrollTop:newHeight}, 'slow');        //創建動畫(style:數值類的效果, speed:動畫速度, easing:動畫節奏, callback:跑完動畫後回傳的函式)
                        }
                    }
                });
            }
            setInterval(loadContent, 1000);
        });
    </script> 
</head>
<body>
    <div id="chatwrapper" class="chatwrapper">
        <?php
            if(!isset($_SESSION["user"])){
        ?>

        <form id="nameform" action="index.php" class="nameform">
            <span class="username">USERNAME</span>
            <span class="outline"><input type="text" id="username" name="user" class="user"/></span>
            <input type="submit" value="LOGIN" id="enterchat" class="enter"/>
        </form>

        <?php
            }
            else{
        ?>

        <div id="chattitle" class="chattitle">
            <div class="welcome">
                <p>WELCOME ： <?php echo $_SESSION["user"]; ?></p>
            </div>

            <div class="exit">
                <p><a href="index.php?user=logout" id="logout">登出</a></p>
            </div>             
        </div>
        
        <div id="chatwindow" class="chatwindow"></div>
        
        
        <div id="chatform" class="chatform">
            <form action="#" id="chat-form">
                <input type="text" id="chatmessage" name="message" class="text" placeholder="SAY SOMETHING..."/>
                <input type="submit" id="send" value="→" class="send"/>
            </form>
        </div>

    <?php }?>
    </div>
</body>
</html>