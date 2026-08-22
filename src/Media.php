<?php
declare(strict_types=1);
namespace MovesCode\Storage;
final class Media extends Storage
{
    private const MIMES=['audio/mpeg','audio/ogg','audio/wav','video/mp4','video/webm','video/ogg'];
    public static function isAllowed(): array{return self::MIMES;}
    public function upload(array $media,string $name): string{return $this->store($media,$name,self::MIMES);}
}
