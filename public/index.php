<?php
// require __DIR__ . '/vendor/autoload.php';
// require_once 'connect.php';

require '../vendor/autoload.php';
require_once 'DBconnect.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$valid_extensions = array('jpeg', 'jpg', 'png'); 

$path_avatar = $_ENV['AVATAR_FOLDER']; 
$path_medium = $_ENV['MEDIUM_FOLDER']; 
$path_thumb = $_ENV['THUMB_FOLDER'];
$host_url = $_ENV['URL_IMAGE_HOST'];


function resizeImageFunc(&$tempfile, $maxDim) {
    $file_name = $tempfile;
    list($width, $height, $type, $attr) = getimagesize( $file_name );
    if ( $width > $maxDim || $height > $maxDim ) {
        $target_filename = $file_name;
        $ratio = $width/$height;
        if( $ratio > 1) {
            $new_width = $maxDim;
            $new_height = $maxDim/$ratio;
        } else {
            $new_width = $maxDim*$ratio;
            $new_height = $maxDim;
        }
        $src = imagecreatefromstring( file_get_contents( $file_name ) );
        $dst = imagecreatetruecolor( (int) $new_width, (int) $new_height );
        imagecopyresampled( $dst, $src, 0, 0, 0, 0, (int) $new_width, (int) $new_height, (int) $width, (int) $height );
        imagedestroy( $src );
        imagejpeg( $dst, $target_filename ); // adjust format as needed
        imagedestroy( $dst );
    }
}

function getRandString($n) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $n; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $randomString .= $characters[$index];
    }
    return $randomString;
}


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}else{

    if(isset($_POST['token'])){
 
        $token =$_POST['token'];
        $topSecret=$_ENV['KEY_AUTHEN'];    
        $data = null;
    
        try {            
            $data = JWT::decode($token, new Key($topSecret, 'HS256'));
        } catch (\Throwable $th) {
            $data = null;
            echo "Error for Authencation!";
        }
    
        if(!is_null($data)){

            if(isset($_FILES['image'])){

                $sql = "SELECT avatar, medium, thumbnail FROM MemberProfile WHERE Member_ID=". $data->user->id .";";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {

                        $old_url_avatar = $row["avatar"];
                        $old_url_thumb = $row["thumbnail"];
                        $old_url_medium = $row["medium"];

                        if(!is_null($old_url_avatar)){
                            $old_url_avatar = explode("/", $old_url_avatar);
                            $old_url_avatar = end($old_url_avatar);
                            if (file_exists($path_avatar . $old_url_avatar)) unlink($path_avatar . $old_url_avatar);
                        }

                        if(!is_null($old_url_medium)){
                            $old_url_medium = explode("/", $old_url_medium);
                            $old_url_medium = end($old_url_medium);
                            if (file_exists($path_medium . $old_url_medium)) unlink($path_medium . $old_url_medium);
                        }

                        if(!is_null($old_url_thumb)){
                            $old_url_thumb = explode("/", $old_url_thumb);
                            $old_url_thumb = end($old_url_thumb);
                            if (file_exists($path_thumb . $old_url_thumb)) unlink($path_thumb . $old_url_thumb);
                        }
                        
                    }
                }

                // $img = $_FILES['image']['name'];
                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $final_image = $data->user->id.rand(1,1000).getRandString(7).rand(1,1000).'.'.$ext;


                if(in_array($ext, $valid_extensions)) {

                    $tempfile = $_FILES['image']['tmp_name'];
                
                    $path_avatar = $path_avatar.strtolower($final_image); 
                    resizeImageFunc($tempfile, 450);
                    if(copy($tempfile,$path_avatar)) {

                        $path_medium = $path_medium.strtolower($final_image); 
                        resizeImageFunc($tempfile, 260);
                        if(copy($tempfile,$path_medium)) {

                            $path_thumb = $path_thumb.strtolower($final_image); 
                            resizeImageFunc($tempfile, 120);
                            if(move_uploaded_file($tempfile,$path_thumb)) {

                                $sql = "UPDATE MemberProfile SET avatar='".$host_url.$path_avatar."' , medium='".$host_url.$path_medium."' , thumbnail='".$host_url.$path_thumb."' WHERE Member_ID='".$data->user->id."'";
                                if ($conn->query($sql) === TRUE) {
                                    echo $host_url.$path_avatar;
                                } else {
                                    echo "Error upload";
                                }

                            }

                        }

                    }

                }else{
                 echo 'Invalid image file';
                }
            }else{
                echo "Don't exist image file!";
            }
        }else{
            echo "Something wrong with Token!";
        }
    }else{
        echo "Require Token!";
    }

}