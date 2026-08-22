# MovesCode Storage

Componente oficial de armazenamento e upload do MovesOS. Valida o conteúdo real, organiza arquivos em diretórios e oferece classes específicas para imagens, documentos, mídias e tipos personalizados.

## Requisitos e instalação

- PHP 8.2+
- Fileinfo
- GD
- Mbstring

```bash
composer require movescode/storage:^1.0
```

## Imagens

```php
use MovesCode\Storage\Image;

$storage = new Image('storage', 'images');
$path = $storage->upload($_FILES['image'], 'foto do perfil', 1200, [
    'jpg' => 75,
    'png' => 5,
    'webp' => 80,
]);
```

`Image::upload()` valida MIME e estrutura da imagem. Quando a largura excede o limite informado, redimensiona proporcionalmente e preserva transparência em PNG, GIF e WebP.

## Arquivos e mídias

```php
use MovesCode\Storage\File;
use MovesCode\Storage\Media;

$document = (new File('storage', 'files'))
    ->upload($_FILES['document'], 'contrato');

$video = (new Media('storage', 'medias'))
    ->upload($_FILES['video'], 'apresentação');
```

`File` aceita documentos comuns, planilhas, ZIP, CSV e texto. `Media` aceita formatos de áudio e vídeo declarados em `isAllowed()`.

## Tipos personalizados

```php
use MovesCode\Storage\Send;

$storage = new Send(
    'storage',
    'custom',
    ['application/postscript'],
    ['ai', 'eps']
);

$path = $storage->upload($_FILES['file'], 'arte');
```

O MIME e a extensão precisam ser permitidos simultaneamente.

## Múltiplos uploads

```php
$storage = new Image('storage', 'gallery');

foreach ($storage->multiple('photos', $_FILES) as $photo) {
    $storage->upload($photo, $photo['name'], 1600);
}
```

## Diretórios e retorno

O construtor recebe diretório base, subdiretório do tipo e a opção de organização por ano/mês:

```php
new File('storage', 'files', monthYearPath: true);
```

`upload()` retorna o caminho gravado. O nome é normalizado e recebe sufixo aleatório para evitar colisões.

## Validação e erros

Falhas lançam `RuntimeException`: upload incompleto, arquivo temporário ausente, MIME proibido, extensão inválida, falha de diretório ou escrita. Capture a exceção na camada da aplicação.

## Segurança

O MIME é detectado com Fileinfo, sem confiar no navegador. Nomes não controlam diretórios, extensões são limitadas e uploads normais usam `move_uploaded_file()`. O fallback de `rename()` existe somente em CLI para testes.

Veja `exemple/index.php`. Licença MIT.
