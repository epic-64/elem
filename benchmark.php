<?php
require_once 'vendor/autoload.php';

use function Epic64\Elem\div;
use function Epic64\Elem\raw;

$iterations = 10000;

echo "=== Bottleneck Analysis (10,000 iterations) ===\n\n";

// Test 1: ElementFactory::createElement directly
$start = hrtime(true);
for ($i = 0; $i < $iterations; $i++) {
    $el = \Epic64\Elem\ElementFactory::createElement('div');
}
$end = hrtime(true);
echo "ElementFactory::createElement:  " . number_format(($end - $start) / 1e6, 2) . " ms\n";

// Test 2: new Element
$start = hrtime(true);
for ($i = 0; $i < $iterations; $i++) {
    $el = new \Epic64\Elem\Element('div');
}
$end = hrtime(true);
echo "new Element('div'):             " . number_format(($end - $start) / 1e6, 2) . " ms\n";

// Test 3: div() function
$start = hrtime(true);
for ($i = 0; $i < $iterations; $i++) {
    $el = div();
}
$end = hrtime(true);
echo "div() function:                 " . number_format(($end - $start) / 1e6, 2) . " ms\n";

// Test 4: div with class
$start = hrtime(true);
for ($i = 0; $i < $iterations; $i++) {
    $el = div(class: 'test');
}
$end = hrtime(true);
echo "div(class: 'test'):             " . number_format(($end - $start) / 1e6, 2) . " ms\n";

// Test 5: class() method - realistic (few classes per element)
$start = hrtime(true);
for ($i = 0; $i < $iterations; $i++) {
    $el = div();
    $el->class('card', 'shadow', 'rounded');
}
$end = hrtime(true);
echo "class() realistic (3 classes):  " . number_format(($end - $start) / 1e6, 2) . " ms\n";

// Test 5b: class() method - worst case (cumulative adds)
$el = div();
$start = hrtime(true);
for ($i = 0; $i < 1000; $i++) {  // Only 1000 iterations for this edge case
    $el->class('test' . $i);
}
$end = hrtime(true);
echo "class() cumulative (1k adds):   " . number_format(($end - $start) / 1e6, 2) . " ms\n";

// Test 6: raw() function - creates temp HTMLDocument
$start = hrtime(true);
for ($i = 0; $i < $iterations; $i++) {
    $el = div()(raw('<span>test</span>'));
}
$end = hrtime(true);
echo "div with raw():                 " . number_format(($end - $start) / 1e6, 2) . " ms\n";

// Test 7: div with text
$start = hrtime(true);
for ($i = 0; $i < $iterations; $i++) {
    $el = div(text: 'Hello World');
}
$end = hrtime(true);
echo "div with text param:            " . number_format(($end - $start) / 1e6, 2) . " ms\n";

// Test 8: Nested structure
$start = hrtime(true);
for ($i = 0; $i < $iterations; $i++) {
    $el = div()(div()(div()));
}
$end = hrtime(true);
echo "Nested div()(div()(div())):     " . number_format(($end - $start) / 1e6, 2) . " ms\n";

// Test 9: toHtml
$el = div(class: 'parent')(div(class: 'child', text: 'Hello'));
$start = hrtime(true);
for ($i = 0; $i < $iterations; $i++) {
    $html = $el->toHtml();
}
$end = hrtime(true);
echo "toHtml (no pretty):             " . number_format(($end - $start) / 1e6, 2) . " ms\n";

// Test 10: toHtml pretty
$start = hrtime(true);
for ($i = 0; $i < $iterations; $i++) {
    $html = $el->toHtml(true);
}
$end = hrtime(true);
echo "toHtml (pretty):                " . number_format(($end - $start) / 1e6, 2) . " ms\n";
