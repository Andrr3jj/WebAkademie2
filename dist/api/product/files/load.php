<?php

class VideoStream
{
    private $path = "";
    private $stream = "";
    private $buffer = 102400;
    private $start  = -1;
    private $end    = -1;
    private $size   = 0;
 
    function __construct($filePath) 
    {
        $this->path = $filePath;
    }
     
    /**
     * Open stream
     */
    private function open()
    {
        if (!($this->stream = fopen($this->path, 'rb'))) {
            die('Could not open stream for reading');
        }
         
    }
     
    /**
     * Set proper header to serve the video content
     */
    private function setHeader()
    {
        ob_get_clean();
        header("Cache-Control: max-age=2592000, public");
        header("Expires: ".gmdate('D, d M Y H:i:s', time()+2592000) . ' GMT');
        header("Last-Modified: ".gmdate('D, d M Y H:i:s', @filemtime($this->path)) . ' GMT' );
        $this->start = 0;
        $this->size  = filesize($this->path);
        $this->end   = $this->size - 1;
        header("Accept-Ranges: 0-".$this->end);
         
        if (isset($_SERVER['HTTP_RANGE'])) {
  
            $c_start = $this->start;
            $c_end = $this->end;
 
            list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
            if (strpos($range, ',') !== false) {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes $this->start-$this->end/$this->size");
                exit;
            }
            if ($range == '-') {
                $c_start = $this->size - substr($range, 1);
            }else{
                $range = explode('-', $range);
                $c_start = $range[0];
                 
                $c_end = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $c_end;
            }
            $c_end = ($c_end > $this->end) ? $this->end : $c_end;
            if ($c_start > $c_end || $c_start > $this->size - 1 || $c_end >= $this->size) {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes $this->start-$this->end/$this->size");
                exit;
            }
            $this->start = $c_start;
            $this->end = $c_end;
            $length = $this->end - $this->start + 1;
            fseek($this->stream, $this->start);
            header('HTTP/1.1 206 Partial Content');
            header("Content-Length: ".$length);
            header("Content-Range: bytes $this->start-$this->end/".$this->size);
        }
        else
        {
            header("Content-Length: ".$this->size);
        }  
         
    }
    
    /**
     * close curretly opened stream
     */
    private function end()
    {
        fclose($this->stream);
        exit;
    }
     
    /**
     * perform the streaming of calculated range
     */
    private function stream()
    {
        $i = $this->start;
        set_time_limit(0);
        while(!feof($this->stream) && $i <= $this->end) {
            $bytesToRead = $this->buffer;
            if(($i+$bytesToRead) > $this->end) {
                $bytesToRead = $this->end - $i + 1;
            }
            $data = fread($this->stream, $bytesToRead);
            echo $data;
            flush();
            $i += $bytesToRead;
        }
    }
     
    /**
     * Start streaming video content
     */
    function start()
    {
        $this->open();
        $this->setHeader();
        $this->stream();
        $this->end();
    }
}



function is_owned($product_id) {
    global $conn;
    $user_id = $_SESSION['id'];
    $products_owned = "";
    $sql = "SELECT productsOwned FROM `user` WHERE id='$user_id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        while($row = mysqli_fetch_assoc($result)) {
            $products_owned = $row['productsOwned'];
        }
    }
    if (!empty($products_owned)) {
        if (str_contains($products_owned, $product_id)) return true;
    }
    return false;
}

function is_subscriber() {
    global $conn;
    $user_id = $_SESSION['id'];
    $time_now = time();

    $sql = "SELECT * FROM subscription WHERE user_id='$user_id' AND duration>'$time_now' AND gopay_status='PAID'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 0) return false;
    return true;
}




session_start();
//include("../../antispam.php");
$logged_in = false;
$owned = false;
if (isset($_SESSION['logged_in'])) $logged_in = true;
if (!empty($_GET['id']) and !empty($_GET['file'])) {
    $id = $_GET['id'];
    $file = $_GET['file'];
    $subdir = "";
    if (!empty($_GET['subdir'])) {
        $subdir = $_GET['subdir'];
    }

    include('../../dbh.api.php');
    
    $email = "";
    if (!empty($_SESSION['email'])) $email = $_SESSION['email'];
    
    
    if ($logged_in) {
        if ((is_owned($id) or $_SESSION['is_admin']) or (is_subscriber() and true)) { // true -> should check if product is video
            $owned = true;
        }
    }
    session_write_close();
    
    $sql = "SELECT * FROM product WHERE id='$id'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) == 1) {
        while($row = mysqli_fetch_assoc($result)) {
            $dir = $row['data'];
        }
        
        $filePath = "";
        if (!empty($dir)) {
            if ($subdir == "") {
                if ($owned) $filePath = '../../../data/uploads/product/' . $dir . "/" . $file;
            } 
            else $filePath = '../../../data/uploads/product/' . $dir . "/" . $subdir . "/" . $file;
            if ( file_exists( $filePath ) and is_file( $filePath) ) {
                $images = ['png', 'jpg', 'jpeg', 'gif', 'WebP'];
                $videos = ['mp4', 'avi', 'WebM'];
                $audios = ['mp3', 'wav', 'mpeg'];

                $ext = pathinfo($filePath, PATHINFO_EXTENSION);

                if (in_array($ext, $images)){
                    header('Content-Type:image/'.$ext);
                    header('Content-Length: ' . filesize($filePath));
                    readfile($filePath);
                }
                if (in_array($ext, $videos)){
                    header("Content-Type: video/mp4");
                    $stream = new VideoStream($filePath);
                    $stream->start();
                }
                if (in_array($ext, $audios)){
                    header('Content-Type: audio/'.$ext);
                    header('Content-length: ' . filesize($filePath));
                    $stream = new VideoStream($filePath);
                    $stream->start();
                }
            }
        } 
    }



    
    //else response("Request failed", "Email not found / Multiple found");
} //else response("Invalid request");
