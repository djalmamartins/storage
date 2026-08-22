<?php
declare(strict_types=1);
namespace MovesCode\Storage;
final class File extends Storage
{
    private const MIMES=['application/pdf','application/zip','application/x-zip-compressed','text/plain','text/csv','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
    public static function isAllowed(): array{return self::MIMES;}
    public function upload(array $file,string $name): string{return $this->store($file,$name,self::MIMES);}
}
