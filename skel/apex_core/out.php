<?php

$files = scandir(__DIR__);
$code = "\n    private array \$templates = [\n";

foreach ($files as $file) { 
    if (in_array($file, ['.', '..', 'out.txt', 'out.php', 'emails'])) { 
        continue;
        }

    $text = base64_encode(file_get_contents($file));
    $code .= "        '$file' => '$text',\n";
}

$code = preg_replace("/,\n$/", "", $code) . "\n    ];\n\n";
file_put_contents('out.txt', $code);

echo "Done\n";

