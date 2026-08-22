<?php
declare(strict_types=1);
namespace MovesCode\Storage;
use RuntimeException;
final class Image extends Storage
{
    private const MIMES=['image/jpeg','image/png','image/gif','image/webp'];
    public static function isAllowed(): array{return self::MIMES;}
    public function upload(array $image,string $name,int $width=2000,?array $quality=null): string
    {
        $tmp=(string)($image['tmp_name']??''); $info=@getimagesize($tmp);
        if (!$info || !in_array(strtolower((string)$info['mime']),self::MIMES,true)) throw new RuntimeException('Invalid image.');
        if ($info[0] <= $width) return $this->store($image,$name,self::MIMES);
        $source=match($info['mime']){'image/jpeg'=>imagecreatefromjpeg($tmp),'image/png'=>imagecreatefrompng($tmp),'image/gif'=>imagecreatefromgif($tmp),'image/webp'=>imagecreatefromwebp($tmp)};
        if (!$source) throw new RuntimeException('Unable to decode image.');
        $height=max(1,(int)round($info[1]*($width/$info[0]))); $target=imagecreatetruecolor($width,$height);
        if (in_array($info['mime'],['image/png','image/gif','image/webp'],true)){imagealphablending($target,false);imagesavealpha($target,true);}
        imagecopyresampled($target,$source,0,0,0,0,$width,$height,$info[0],$info[1]);
        $ok=match($info['mime']){'image/jpeg'=>imagejpeg($target,$tmp,$quality['jpg']??75),'image/png'=>imagepng($target,$tmp,$quality['png']??5),'image/gif'=>imagegif($target,$tmp),'image/webp'=>imagewebp($target,$tmp,$quality['webp']??80)};
        if(!$ok)throw new RuntimeException('Unable to resize image.');
        return $this->store($image,$name,self::MIMES);
    }
}
