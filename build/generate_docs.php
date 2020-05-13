<?php

/** @noinspection TransitiveDependenciesUsageInspection */

use voku\build\Template\TemplateFormatter;
use voku\helper\UTF8;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/vendor/autoload.php';

$phpFiles = \voku\SimplePhpParser\Parsers\PhpCodeParser::getPhpFiles(__DIR__ . '/../src/voku/helper/UTF8.php');
$phpClasses = $phpFiles->getClasses();
$phpUtf8Class = $phpClasses[UTF8::class];

// -------------------------------------

$templateDocument = file_get_contents(__DIR__ . '/docs/base.md');

$templateMethodParam = <<<RAW
- `%param%`
RAW;

/** @noinspection HtmlUnknownAnchorTarget */
$templateMethod = <<<RAW
## %name%
<a href="#class-methods">↑</a>
%description%

**Parameters:**
%params%

**Return:**
- `%return%`

--------

RAW;

$templateIndexLink = <<<RAW
<a href="%href%">%title%</a>
RAW;

// -------------------------------------

$functionsDocumentation = [];
$functionsIndex = [];

foreach ($phpUtf8Class->methods as $method) {
    assert($method instanceof \voku\SimplePhpParser\Model\PHPMethod);

    if ($method->access !== 'public') {
        continue;
    }

    if ($method->is_deprecated) {
        continue;
    }

    if (\strpos($method->name, '_') === 0) {
        continue;
    }

    $methodIndexTemplate = new TemplateFormatter($templateIndexLink);

    $methodTemplate = new TemplateFormatter($templateMethod);

    // -- params
    $params = [];
    $paramsTypes = [];
    foreach ($method->parameters as $param) {
        $paramsTemplate = new TemplateFormatter($templateMethodParam);
        $paramsTemplate->set('param', ($param->typeFromPhpDocPslam ?: $param->typeFromPhpDoc) . UTF8::str_replace_beginning($param->typeMaybeWithComment, $param->typeFromPhpDoc, ''));
        $params[] = $paramsTemplate->format();
        $paramsTypes[] = $param->typeFromPhpDoc . ' ' . '$' . $param->name;
    }

    if (count($params) !== 0) {
        $methodTemplate->set('params', implode("\n", $params));
    } else {
        $methodTemplate->set('params', '__nothing__');
    }

    // -- return

    $methodWithType = $method->name . '(' . implode(', ', $paramsTypes) . '): ' . $method->returnTypeFromPhpDoc;

    $description = trim($method->summary . "\n\n" . $method->description);

    $methodTemplate->set('name', $methodWithType);
    $methodTemplate->set('description', $description);
    $methodTemplate->set('return', $method->returnTypeMaybeWithComment);

    $methodIndexTemplate->set('title', $method->name);
    $methodIndexTemplate->set('href', '#' . UTF8::css_identifier($methodWithType));

    $functionsDocumentation[$method->name] = $methodTemplate->format();
    $functionsIndex[$method->name] = $methodIndexTemplate->format();
}

ksort($functionsDocumentation);
$functionsDocumentation = array_values($functionsDocumentation);

ksort($functionsIndex);

// -------------------------------------

$documentTemplate = new TemplateFormatter($templateDocument);
$documentTemplate->set('__functions_list__', implode("\n", $functionsDocumentation));

$indexLastChar = null;
$indexStrResult = '';
$counterTmp = 0;
foreach ($functionsIndex as $_index => $_template) {
    $counterTmp++;

    if ($counterTmp === 1) {
        $indexStrResult .= '<tr>';
    }

    $indexStrResult .= '<td>' . sprintf("%s\n", $_template) . '</td>';

    if ($counterTmp === 4) {
        $counterTmp = 0;
        $indexStrResult .= '</tr>';
    }
}
if ($counterTmp > 0) {
    $indexStrResult .= '</tr>';
}
$indexStrResult = '
<table>
    ' . $indexStrResult . '
</table>
';

$documentTemplate->set('__functions_index__', $indexStrResult);

file_put_contents(__DIR__ . '/../README.md', $documentTemplate->format());
