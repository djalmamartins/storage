<?php
declare(strict_types=1);
namespace MovesCode\Storage;

use RuntimeException;

abstract class Storage
{
    public function __construct(protected string $baseDir, protected string $typeDir, protected bool $monthYearPath = true)
    {
        $this->baseDir = rtrim($baseDir, '/\\');
        $this->typeDir = trim($typeDir, '/\\');
    }

    abstract public static function isAllowed(): array;

    public function multiple(string $inputName, array $files): array
    {
        if (!isset($files[$inputName]['name']) || !is_array($files[$inputName]['name'])) return [];
        $result=[];
        foreach ($files[$inputName]['name'] as $i=>$name) {
            $result[]=['name'=>$name,'type'=>$files[$inputName]['type'][$i]??'', 'tmp_name'=>$files[$inputName]['tmp_name'][$i]??'', 'error'=>$files[$inputName]['error'][$i]??UPLOAD_ERR_NO_FILE, 'size'=>$files[$inputName]['size'][$i]??0];
        }
        return $result;
    }

    protected function store(array $file, string $name, array $allowedMimes, ?array $allowedExtensions = null): string
    {
        $tmp=(string)($file['tmp_name']??'');
        if (($file['error']??UPLOAD_ERR_NO_FILE)!==UPLOAD_ERR_OK || !is_file($tmp)) throw new RuntimeException('Invalid upload.');
        $mime=strtolower((string)(new \finfo(FILEINFO_MIME_TYPE))->file($tmp));
        if (!in_array($mime,array_map('strtolower',$allowedMimes),true)) throw new RuntimeException('File type is not allowed.');
        $extension=strtolower(pathinfo((string)($file['name']??''),PATHINFO_EXTENSION));
        if ($allowedExtensions && !in_array($extension,array_map('strtolower',$allowedExtensions),true)) throw new RuntimeException('File extension is not allowed.');
        if ($extension==='' || preg_match('/^[a-z0-9]{1,10}$/',$extension)!==1) throw new RuntimeException('Invalid file extension.');
        $dir=$this->directory();
        $safe=$this->slug($name) ?: 'file';
        $path=$dir.'/'.$safe.'-'.bin2hex(random_bytes(6)).'.'.$extension;
        $moved=is_uploaded_file($tmp) ? move_uploaded_file($tmp,$path) : (PHP_SAPI==='cli' && rename($tmp,$path));
        if (!$moved) throw new RuntimeException('Unable to store upload.');
        return $path;
    }

    protected function directory(): string
    {
        $suffix=$this->monthYearPath ? '/'.date('Y/m') : '';
        $dir=$this->baseDir.'/'.$this->typeDir.$suffix;
        if (!is_dir($dir) && !mkdir($dir,0755,true) && !is_dir($dir)) throw new RuntimeException('Unable to create storage directory.');
        return $dir;
    }

    protected function slug(string $value): string
    {
        $value=mb_strtolower(trim($value),'UTF-8');
        $ascii=iconv('UTF-8','ASCII//TRANSLIT//IGNORE',$value);
        return trim(preg_replace('/[^a-z0-9]+/','-',strtolower($ascii?:$value))??'', '-');
    }
}
