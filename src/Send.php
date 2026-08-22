<?php
declare(strict_types=1);
namespace MovesCode\Storage;
final class Send extends Storage
{
    public function __construct(string $uploadDir,string $fileTypeDir,private array $mimes,private array $extensions,bool $monthYearPath=true){parent::__construct($uploadDir,$fileTypeDir,$monthYearPath);}
    public static function isAllowed(): array{return [];}
    public function upload(array $file,string $name): string{return $this->store($file,$name,$this->mimes,$this->extensions);}
}
