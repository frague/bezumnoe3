<?

class ImgResizer
{
    var $imageQuality = 75;

    var $thumbW  = false;
    var $thumbH  = false;

    var $src = false;
    var $srcFile = false;
    var $srcType = false;
    var $srcWidth = false;
    var $srcHeight = false;

    var $thumb = false;

    function ImgResizer($f, $tw = false, $th = false) {
        if (is_file($f)) {
            $this->thumbW = $tw;
            $this->thumbH = $th;
            $this->srcFile = $f;

            $this->LoadInfo();
            $this->LoadImageByType();
        }
    }

    function LoadInfo() {
        list($this->srcWidth, $this->srcHeight, $this->srcType) = getimagesize($this->srcFile);

        if($this->thumbH === false && $this->thumbW !== false) {
            $this->thumbH = round($this->srcHeight*($this->thumbW/$this->srcWidth));
        } else if($this->thumbH !== false && $this->thumbW === false) {
            $this->thumbW = round($this->srcWidth*($this->thumbH/$this->srcHeight));
        } else if($this->thumbH === false && $this->thumbW === false) {
            die("Incorrect resampled dimensions!");
        }
    }

    function PrintResized() {
        $this->DoResize();
        if ($this->thumb !== false) {
            header('Content-type: image/jpeg');
            @imagejpeg($this->thumb);
            imagedestroy($this->thumb);
        }
    }

    function PrintEmpty($text = "") {
        $thumb  = imagecreate(300, 200);
        $bgc = imagecolorallocate($thumb, 235, 246, 230);
        $tc  = imagecolorallocate($thumb, 63, 79, 36);
        imagefilledrectangle($thumb, 10, 10, 50, 40, $bgc);

        if ($text) {
            imagestring($thumb, 2, 9, 7, $text, $tc);
        } else {
            imagestring($thumb, 2, 189, 7, "ImgResizer Class", $tc);
            imagestring($thumb, 1, 226, 22, "version 0.1", $tc);
        }

        header('Content-type: image/jpeg');
        @imagegif($thumb);
        imagedestroy($thumb);
    }

    function SaveResized($filePath) {
        $this->DoResize();
        if ($this->thumb !== false && $filePath) {
            @imagejpeg($this->thumb, $filePath, $this->imageQuality);
            imagedestroy($this->thumb);
            echo "$filePath";
        }
    }

    function LoadImageByType() {
        if ($this->srcFile !== false && $this->srcType !== false) {
            switch($this->srcType) {
                case IMAGETYPE_GIF:
                    $this->src = @imagecreatefromgif($this->srcFile);
                    break;
                case IMAGETYPE_JPEG:
                    $this->src = @imagecreatefromjpeg($this->srcFile);
                    break;
                case IMAGETYPE_PNG:
                    $this->src = @imagecreatefrompng($this->srcFile);
                    break;
            }
        }
    }

    function DoResize() {
        if ($this->src !== false) {
            $this->thumb = imageCreateTrueColor($this->thumbW, $this->thumbH);
            imagecopyresampled($this->thumb, $this->src, 0, 0, 0, 0, $this->thumbW, $this->thumbH, $this->srcWidth, $this->srcHeight);
            imagedestroy($this->src);
        }
    }
}

?>
