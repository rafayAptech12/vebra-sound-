<?php
$dir = __DIR__ . '/HTML';
$files = glob($dir . '/*.html');
foreach ($files as $file) {
    $content = file_get_contents($file);
    $original = $content;
    
    // Replace <header class="site-header"> with <header class="site-header site-header--float">
    $content = preg_replace('/<header class="site-header">/', '<header class="site-header site-header--float">', $content);
    
    if ($content !== $original) {
        file_put_contents($file, $content);
        echo "Added float class in: " . basename($file) . "\n";
    }
}
?>
