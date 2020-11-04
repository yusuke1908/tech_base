<DOCTYPE html> 
<html lang="ja"> 
<head> 
  <meta charset="UTF-8"> 
  <title></title> 
</head> 
<body> 
<?php 


$filename = "mysql:dbname=tb220737db;host=localhost" ; 
$messages = file($filename,FILE_IGNORE_NEW_LINES); 


    // 新規投稿 
    if(empty($_POST["name"]) && empty($_POST["text"])){ 
        $action_txt = "名前とコメントを入力してください"; 
    }elseif(empty($_POST["name"])) { 
        $action_txt = "名前を入力してください"; 
    }elseif(empty($_POST["text"])){ 
        $action_txt = "コメントを入力してください"; 
    }elseif(empty($_POST["password"])){ 
        $action_txt = "パスワードを入力してください"; 

    }elseif(!empty($_POST["name"]) && !empty($_POST["text"])){ 
         
         $name = $_POST["name"]; 
        $text = $_POST["text"]; 
        $today = date("Y/m/d H:i:s"); 
        $pass=$_POST["password"]; 

        if(file_exists($filename)){ 
            $num=count(file($filename))+1; 
        }else{ 
            $num = 1; 
        }         

         

        if(empty($_POST["editing"])){ //新規投稿 
            $str = $num."<>".$name."<>".$text."<>".$today."<>".$pass.PHP_EOL; 
            $fp = fopen($filename,"a");  
            fwrite($fp, $str);  
            fclose($fp); 
     
            $action_txt = "投稿されました！"; 

        }else{ //編集投稿 
            $fp = fopen($filename,'w'); 
            fclose($fp); 
         
                $fp = fopen($filename,"a"); 
                foreach($messages as $message){ 
                     $array = explode('<>', $message); 
                     
                if($array[0]!=$_POST["editing"]){ 
                    fwrite($fp,$message.PHP_EOL); 
                }else{ 

                    if($pass == $array[4]){ 
                        $editstr = $_POST["editing"]."<>".$name."<>".$text."<>".$today."<>".$pass.PHP_EOL; 
                        fwrite($fp, $editstr); 
                        $action_txt = "〜編集済み〜";         

                    }else{ 
                        fwrite($fp,$message.PHP_EOL); 
                        $action_txt = "パスワードが正しくありません"; 
                    } 
                } 

            }         
            fclose($fp);     
        } 
    } 




// 編集か投稿か 
if(!empty($_POST["edit"])){  
    $messages = file($filename,FILE_IGNORE_NEW_LINES); 
        $fp = fopen($filename, 'w');//上書きモード 
        fclose($fp); 
     

        $fp = fopen($filename,"a"); 
        foreach($messages as $message){ 
            $array = explode('<>', $message);         
             
            if($array[0]!= $_POST["edit"]){ 
                fwrite($fp,$message.PHP_EOL); 
            }else{ 
                $ediPass = $_POST["ediPass"]; 
                if(empty($ediPass)){ 
                    fwrite($fp,$message.PHP_EOL); 
                    $action_txt = "パスワードを入力してください"; 
                     
                }elseif($ediPass !== $array[4]){ 
                    fwrite($fp,$message.PHP_EOL); 
                    $action_txt = "パスワードが正しくありません";     

                }else{ 
                    $editName = $array[1]; 
                    $editText = $array[2]; 
                    fwrite($fp, $message.PHP_EOL);   //$edit."<>". 
                    $action_txt = "編集中です";  

                } 
            } 
        } 
             
    fclose($fp); 

} 



// 削除機能 
if(!empty($_POST["delete"])){  

    if(empty($_POST["delPass"])){ 
        $action_txt = "パスワードを入力してください"; 
    }elseif(!empty($_POST["delPass"])){ 

$delete = $_POST["delete"]; 
    $fp = fopen($filename,'w');//上書きモード 
    fclose($fp); 

    $fp = fopen($filename,"a"); 
        foreach($messages as $message){ 
            $array = explode('<>', $message); 
             
            if($array[0]!=$delete){ 
                fwrite($fp,$message.PHP_EOL); 
            }else{ 

                if($_POST["delPass"] == $array[4]){ 
                    fwrite($fp,"コメントがありません".PHP_EOL); 
                    $action_txt = "コメントを削除しました"; 
                }else{ 
                    fwrite($fp,$message.PHP_EOL); 
                    $action_txt = "パスワードが正しくありません"; 
                } 
            } 
        } 
             
        fclose($fp);     
    }  

} 

?> 


<!-- 入力フォーム --> 
<form method="POST" action=""> 
【投稿フォーム】<br> 
<input type="text" name="name" placeholder="名前" value="<?php if(!empty($_POST["edit"])){echo $editName;} ?>"><br> 
<input type="text" name="text" placeholder="コメント" value="<?php if(!empty($_POST["edit"])){echo $editText;} ?>"> 
<input type="hidden" name="editing" value="<?php if(!empty($_POST["edit"]) && (!empty($_POST["ediPass"])) && $ediPass == $array[4]){echo $_POST["edit"];} ?>"><br> 
<input type="password" name="password" placeholder="パスワード"> 
<input type="submit" name="submit" value="送信"><br> 
</form> 

<br> 
<form method="POST" action=""> 
【削除フォーム】<br> 
<input type="number" name="delete" placeholder="削除番号"><br> 
<input type="password" name="delPass" placeholder="パスワード"> 
<input type="submit" name="submit" value="削除"> 
</form> 

<br> 

<form method="POST" action=""> 
【編集フォーム】<br> 
<input type="number" name="edit" placeholder="編集番号"><br> 
<input type="password" name="ediPass" placeholder="パスワード"> 
<input type="submit" name="submit" value="編集"> 
</form> 
<br> 

<?php 

// 送信後のメッセージ 
    echo "<br>"; 
    echo $action_txt; 
    echo "<br>"; 
    echo"____________________________"."<br>";
// 掲示板の表示 
     
    if(file_exists($filename)){ 
         
    $messages = file($filename,FILE_IGNORE_NEW_LINES); 
        echo "【投稿】"."<br>"; 
        foreach($messages as $message){ 
            $array = explode('<>', $message); 
            echo $array[0]. " ".$array[1]. " ".$array[2]. " ".$array[3]. " "; 
            echo "<br>"; 
    } 

    } 
     



?> 

</body> 
</html>