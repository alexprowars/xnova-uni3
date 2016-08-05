<?php

$_SERVER['DOCUMENT_ROOT'] = '/var/www/xnova/data/www/uni3.xnova.su';

// упрощенная функция scandir
function myscandir($dir)
{
    $list = scandir($dir);
    unset($list[0],$list[1]);
    return array_values($list);
}

// функция очищения папки
function clear_dir($dir)
{
    $list = myscandir($dir);
    
    foreach ($list as $file)
    {
        if (is_dir($dir.$file))
        {
            clear_dir($dir.$file.'/');
            rmdir($dir.$file);
        }
        else
        {
            unlink($dir.$file);
        }
    }
}

// пример использования
clear_dir($_SERVER['DOCUMENT_ROOT'].'/cache/');

?>