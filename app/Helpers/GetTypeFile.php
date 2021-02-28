<?php

function getTypeFile($file64)
{
    $f = finfo_open();
    $mime_type = finfo_buffer($f, $file64, FILEINFO_MIME_TYPE);
    return $mime_type;
}