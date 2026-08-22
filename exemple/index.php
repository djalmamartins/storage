<?php
require dirname(__DIR__).'/vendor/autoload.php';
use MovesCode\Storage\Image;
if(isset($_FILES['image'])) echo (new Image(__DIR__.'/uploads','images'))->upload($_FILES['image'],'example');
