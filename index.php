<?php

$relativePath = "..\umkm-digital-master\\";

// Do Not Touch
function getDirContents($dir, &$results = array()) {
    $files = scandir($dir);

    foreach ($files as $key => $value) {
        $path = ($dir . DIRECTORY_SEPARATOR . $value);
        if (is_file($path)) {
            $results[] = $path;
        } else if ($value != "." && $value != "..") {
            getDirContents($path, $results);

            if(!is_dir($path)){
                $results[] = $path;
            }
        }
    }

    return $results;
}

$content = file_get_contents('folders.txt');
$list = explode("\n", $content);

$data = [];

foreach ($list as $item){
    $path = $relativePath.trim($item);

    if(is_file($path)){
        $data[] = $path;
    }
    else if(file_exists($path)){
        $folder = scandir($path);

        $data = array_merge($data, getDirContents($path));
    }
}
/* END OF PREPROCESSOR */

include "vendor/autoload.php";

$phpWord = new \PhpOffice\PhpWord\PhpWord();

$length = count($data);
foreach ($data as $key => $item){
    echo "Add files " . $key + 1 . " out of " . $length . PHP_EOL;
    $section = $phpWord->addSection();
    $section->addText(
        substr($item, strlen($relativePath)),
        array('bold' => true, 'name' => 'Times New Roman', 'size' => 12, 'bgColor' => 'yellow')
    );
    $section->addText(
        str_replace("\n", "<w:br/>", htmlspecialchars(file_get_contents($item))),
        array('name' => 'Consolas', 'size' => 11)
    );
}

$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord);
$objWriter->save('result.docx');

//$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
//$objWriter->save('php://output');