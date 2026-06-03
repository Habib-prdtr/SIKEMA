<?php
$dir = new RecursiveDirectoryIterator('resources/views');
$ite = new RecursiveIteratorIterator($dir);

foreach ($ite as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $path = $file->getPathname();
        $content = file_get_contents($path);
        
        // Match "Rp {{ number_format($var, 0, ',', '.') }}" -> "{{ format_rupiah($var) }}"
        $content = preg_replace('/Rp\s*\{\{\s*number_format\(([^,]+),\s*0\s*,\s*\'\,\'\s*,\s*\'\.\'\)\s*\}\}/', '{{ format_rupiah($1) }}', $content);
        
        // Match "Rp ' . number_format($var, 0, ',', '.')" -> "format_rupiah($var)"
        $content = preg_replace('/\'Rp \'\s*\.\s*number_format\(([^,]+),\s*0\s*,\s*\'\,\'\s*,\s*\'\.\'\)/', 'format_rupiah($1)', $content);
        
        file_put_contents($path, $content);
    }
}
echo "Selesai!\n";
