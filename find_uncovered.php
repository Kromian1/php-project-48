<?php
xdebug_start_code_coverage();

require 'vendor/autoload.php';

use Gendiff\Parser;

\$parser = new Parser();

// 1. JSON
\$parser->parse('tests/fixtures/file1.json');

// 2. YAML
\$parser->parse('tests/fixtures/file1.yml');

// 3. .yaml расширение
\$parser->parse('tests/fixtures/file2.yaml');

// 4. Ошибка чтения
try {
    \$parser->parse('/tmp/nonexistent_' . uniqid() . '.json');
} catch (Exception \$e) {}

// 5. Неизвестное расширение
\$temp = tempnam(sys_get_temp_dir(), 'test') . '.txt';
file_put_contents(\$temp, 'test');
try {
    \$parser->parse(\$temp);
} catch (Exception \$e) {}
unlink(\$temp);

\$coverage = xdebug_get_code_coverage();
xdebug_stop_code_coverage();

foreach (\$coverage as \$file => \$lines) {
    if (strpos(\$file, 'Parser.php') !== false) {
        \$source = file(\$file);
        echo "Uncovered lines in Parser.php:\n";
        \$hasUncovered = false;
        foreach (\$lines as \$num => \$status) {
            if (\$status === 0 && isset(\$source[\$num-1])) {
                \$hasUncovered = true;
                echo "  Line \$num: " . trim(\$source[\$num-1]) . "\n";
            }
        }
        if (!\$hasUncovered) {
            echo "  ✅ All lines covered!\n";
        }
    }
}
