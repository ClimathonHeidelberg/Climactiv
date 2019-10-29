<?php
if (!defined('OC_CAPTCHA_KEY')){
    define('OC_CAPTCHA_KEY', '1ASD2A4D2AA4DA15A');
}

$string = base64_decode(str_replace(OC_CAPTCHA_KEY, '', $_GET['i']));
$nums = explode('#', $string);

//create an image 
$my_img = imagecreatetruecolor( 82, 32 );

//define a background color for image
$background = imagecolorallocate( $my_img, 255, 255, 255 );

//define text color
$text_color = imagecolorallocate( $my_img, 0, 0, 0);

//font should be changed as per theme
$font = 'Lato-Regular.ttf';

//text to be placed in image
$text = $nums[0] . '  +  ' . $nums[1] . '   =  ';

//background
imagefilledrectangle($my_img, 0, 0, 399, 32, $background);

// Add the text:: imagettftext($img_resource, $font_size, $angle, $x_position, $y_position, $font, $text)
imagettftext($my_img, 12, 0, 12, 23, $text_color, $font, $text);

header( "Content-type: image/png" );
imagepng( $my_img );
imagecolordeallocate( $text_color );
imagecolordeallocate( $background );
imagedestroy( $my_img );
