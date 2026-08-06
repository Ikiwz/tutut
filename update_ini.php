<?php
$file = 'C:\Users\Acer\.config\herd\bin\php84\php.ini';
$content = file_get_contents($file);
// Ensure we don't duplicate
if (strpos($content, 'upload_max_filesize = 500M') === false) {
    file_put_contents($file, "\n\n; Updated by AI\nupload_max_filesize = 500M\npost_max_size = 500M\nmemory_limit = 512M\n", FILE_APPEND);
}
echo "Done\n";
