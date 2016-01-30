<?php

class Image
{
    private $source_image = null;
    private $source_image_type = null;
    private $image = null;
    public $errors = array();

    /**
     * Image constructor.
     * @param null $image_src
     */
    public function __construct($image_src = null)
    {
        if ($image_src) {
            $this->open($image_src);
        }
    }

    /**
     * @param string $type
     * @param null $quality
     * @param null $save_path
     * @return $this
     */
    private function image($type = 'jpeg', $quality = null, $save_path = null)
    {
        if (is_resource($this->image)) {
            switch ($type):
                case('jpeg'):
                case('jpg'):
                default:
                    if (!$save_path) {
                        header("content-type: image/jpeg");
                    }
                    $quality ? $quality : $quality = 75;

                    imagejpeg($this->image, $save_path, $quality);
                    break;
                case('png'):
                    if (!$save_path) {
                        header("content-type: image/png");
                    }
                    $quality ? $quality = ($quality % 10) : $quality = 7;

                    imagepng($this->image, $save_path, $quality);

                    break;
                case('gif'):
                    if (!$save_path) {
                        header("content-type: image/gif");
                    }
                    imagegif($this->image, $save_path);

                    break;
            endswitch;
        }
        return $this;
    }

    /**
     * @param string $image_type
     * @param null $image_quality
     * @return Image
     */
    public function display($image_type = 'jpeg', $image_quality = null)
    {
        return $this->image($image_type, $image_quality);
    }

    /**
     * @param string $image_path
     * @param string $image_type
     * @param null $image_quality
     * @return Image
     */
    public function save($image_path = '', $image_type = 'jpeg', $image_quality = null)
    {
        return $this->image($image_type, $image_quality, $image_path);
    }

    /**
     * @param null $image_src
     * @return $this
     */
    public function open($image_src = null)
    {
        if ($image_src && file_exists($image_src)) {
            $image_info = getimagesize($image_src);
            switch ($image_info[2]):
                case 1 :
                    $this->source_image = @imagecreatefromgif($image_src);
                    $this->source_image_type = 'gif';
                    break;
                case 2 :
                    $this->source_image = @imagecreatefromjpeg($image_src);
                    $this->source_image_type = 'jpeg';
                    break;
                case 3 :
                    $this->source_image = @imagecreatefrompng($image_src);
                    $this->source_image_type = 'png';
                    break;
            endswitch;

            $this->image = $this->source_image;
        }
        return $this;
    }

    /**
     * @param null $width
     * @param null $height
     * @param string $background
     * @param string $method
     * @param string $align
     * @param string $vertical_align
     * @return $this
     */
    public function resize($width = null, $height = null, $background = 'ffffff', $method = 'fit', $align = 'center', $vertical_align = 'middle')
    {
        if ($this->source_image && (($width = intval($width)) | ($height = intval($height)))) {
            $outimage = null;
            $outimage_width = $width;
            $outimage_height = $height;

            $outimage_x = 0;
            $outimage_y = 0;

            $image_width = imagesx($this->source_image);
            $image_height = imagesy($this->source_image);


            if (!$outimage_height) {
                //if not set height output
                $outimage_height = ceil($outimage_width / $image_width * $image_height);

            } elseif (!$outimage_width) {
                //if not set width output
                $outimage_width = ceil($outimage_height / $image_height * $image_width);
            }
            if ($outimage_width > $image_width && $outimage_height > $image_height):
                //if source image less output image
                $rect_width = $image_width;
                $rect_height = $image_height;

                if (!$width):
                    $outimage_width = $image_width;
                endif;
                if (!$height):
                    $outimage_height = $image_height;
                endif;

            else:
                //if source image greater output image

                $rect_width = $outimage_width;
                $rect_height = $outimage_height;

                $fix_width = (($image_width / $outimage_width * $outimage_height) >= $image_height);
                $fix_height = (($image_height / $outimage_height * $outimage_width) >= $image_width);

                switch ($method):

                    case('fit'):
                    default:
                        if ($fix_width) {
                            //fit to width

                            $rect_height = ceil($outimage_width / $image_width * $image_height);

                        } elseif ($fix_height) {
                            //fit to height

                            $rect_width = ceil($outimage_height / $image_height * $image_width);

                        }
                        break;
                    case('crop'):
                        if ($fix_width) {
                            //crop height

                            $rect_width = ceil($outimage_height / $image_height * $image_width);

                        } elseif ($fix_height) {
                            //crop width

                            $rect_height = ceil($outimage_width / $image_width * $image_height);

                        }
                        break;
                    case('scale'):
                        //continue
                        break;

                endswitch;
            endif;

            switch ($align) {
                case('center'):
                default:
                    $outimage_x = round($outimage_width / 2 - $rect_width / 2);
                    break;
                case('left'):
                    $outimage_x = 0;
                    break;
                case('right'):
                    $outimage_x = $outimage_width - $rect_width;
                    break;
            }

            switch ($vertical_align) {
                case('middle'):
                default:
                    $outimage_y = round(($outimage_height / 2) - ($rect_height / 2));
                    break;
                case('top'):
                    $outimage_y = 0;
                    break;
                case('bottom'):
                    $outimage_y = $outimage_height - $rect_height;
                    break;
            }


            if (!($outimage = imagecreatetruecolor($outimage_width, $outimage_height))) {
                $this->errors[] = 'Unable to create GD image';
                return $this;
            }

            if ($background == 'alpha') {
                imagesavealpha($outimage, true);
                imagealphablending($outimage, false);
                $outbackground = imagecolorallocatealpha($outimage, 0, 0, 0, 127);
            } else {
                if (!$background) {
                    $background = 'ffffff';
                }

                $outbackground = array();

                preg_match_all('/[\w\d]{2}/', substr($background, 0, 6), $background);
                $outbackground = imagecolorallocate($outimage, hexdec($background[0][0]), hexdec($background[0][1]), hexdec($background[0][2]));
            }
            imagefilledrectangle($outimage, 0, 0, $outimage_width, $outimage_height, $outbackground);
            imagecopyresampled($outimage, $this->source_image, $outimage_x, $outimage_y, 0, 0, $rect_width, $rect_height, $image_width, $image_height);

            $this->image = $outimage;
        }

        return $this;
    }
}