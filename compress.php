<?php
require_once join('/', [__DIR__, (file_exists(__DIR__ . '/vendor/autoload.php') ? '/vendor/autoload.php' : '/../../autoload.php')]);

use Imagine\Gd\Imagine;

if ($argc !== 4) {
   echo "Falha na conversão das imagens, especifique a origem e destino";
   exit;
}

$origin = removeGarbageSeparator($argv[1]);
$destination = removeGarbageSeparator($argv[2]);

$quality = (int) $argv[3];

try {
   convertToWebp($origin, $destination, $quality);
   echo 'All files were converted' . PHP_EOL;
} catch (\Throwable $t) {
   echo $t->getMessage();
}

function removeGarbageSeparator(string $path): string
{
   $path = strpos($path, DIRECTORY_SEPARATOR, strlen($path) - 1) === false ? $path : substr_replace($path, "", -1);
   $path = strpos($path, ".") === 0 ?  substr($path, 1) : $path;
   $path = strpos($path, DIRECTORY_SEPARATOR) === 0 ?  substr($path, 1) : $path;

   return $path;
}

function convertToWebp(string $origin, string $destination, int $quality): void
{
   if (file_exists($origin) && is_dir($origin)) {
      if (!file_exists($destination)) {
         mkdir($destination);
      }
      foreach (glob(str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, join(DIRECTORY_SEPARATOR, [$origin, '*']))) as $file) {
         convertToWebp(
            $file,
            (str_replace(
               ['.png', '.jpg', '.jpeg', '.gif'],
               '.webp',
               str_replace($origin, $destination, $file)
            )),
            $quality
         );
      }
   } else {
      if (in_array(explode('.', $origin)[1], ['png', 'jpg', 'jpeg', 'gif'])) {
         $imagine = new Imagine();
         $image = $imagine->open($origin);
         $image->save($destination, array('webp_quality' => $quality));
      }
      return;
   }
}
