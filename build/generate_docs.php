<?php

use voku\helper\UTF8;

require __DIR__ . '/../vendor/autoload.php';

$factory = \phpDocumentor\Reflection\DocBlockFactory::createInstance();
$reflection = new ReflectionClass(UTF8::class);

// -------------------------------------

$templateDocument = file_get_contents(__DIR__ . '/docs/base.md');

$templateMethodParam = <<<RAW
- %param%
RAW;

$templateMethodReturn = <<<RAW
- %return%
RAW;

$templateMethod = <<<RAW
## %name%
<a href="#class-methods">↑</a>
%description%

**Parameters:**
%params%

**Return:**
%return%

--------

RAW;

$templateIndexLink = <<<RAW
<a href="%href%">%title%</a>
RAW;

// -------------------------------------

class TemplateFormatter
{
    /** @var array */
    private $vars = [];

    /** @var string */
    private $template;

    /**
     * @param string $template
     */
    public function __construct(string $template)
    {
        $this->template = $template;
    }

    /**
     * @param string $var
     * @param string $value
     *
     * @return mixed
     */
    public function set(string $var, string $value): self
    {
        $this->vars[$var] = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function format(): string
    {
        $s = $this->template;

        foreach ($this->vars as $name => $value) {
            $s = UTF8::replace($s, sprintf('%%%s%%', $name), $value);
        }

        return $s;
    }
}

// -------------------------------------

$functionsDocumentation = [];
$functionsIndex = [];

foreach ($reflection->getMethods() as $method) {
    if (!$method->isPublic()) {
        continue;
    }

    if (UTF8::str_starts_with($method->getShortName(), '_')) {
        continue;
    }

    $doc = $factory->create($method->getDocComment());

    $methodIndexTemplate = new TemplateFormatter($templateIndexLink);

    $methodTemplate = new TemplateFormatter($templateMethod);

    // -- params
    $params = [];
    $paramsTypes = [];
    foreach ($tagsInput = $doc->getTagsByName('param') as $tagParam) {
        /** @var \phpDocumentor\Reflection\DocBlock\Tags\Param $tagParam */
        $paramsTemplate = new TemplateFormatter($templateMethodParam);
        $paramsTemplate->set('param', (string)$tagParam);
        $params[] = $paramsTemplate->format();
        $paramsTypes[] = $tagParam->getType() . ' ' . '$' . $tagParam->getVariableName();
    }

    if (count($params) !== 0) {
        $methodTemplate->set('params', implode("\n", $params));
    } else {
        $methodTemplate->set('params', '__nothing__');
    }

    // -- return
    $returns = [];
    $returnsTypes = [];
    foreach ($tagsInput = $doc->getTagsByName('return') as $tagReturn) {
        /** @var \phpDocumentor\Reflection\DocBlock\Tags\Return_ $tagReturn */
        $returnTemplate = new TemplateFormatter($templateMethodReturn);
        $returnTemplate->set('return', (string)$tagReturn);
        $returns[] = $returnTemplate->format();
        $returnsTypes[] = $tagParam->getType();
    }

    if (count($returns) !== 0) {
        $methodTemplate->set('return', implode("\n", $returns));
    } else {
        $methodTemplate->set('return', '__void__');
    }

    $methodWithType = $method->getShortName() . '(' . implode(', ', $paramsTypes) . '): ' . implode('|', $returnsTypes);

    $description = (string)$doc->getDescription();
    if (!$description) {
        $description = $doc->getSummary();
    }

    $methodTemplate->set('name', $methodWithType);
    $methodTemplate->set('description', $description);
    $methodTemplate->set('code', '```php echo ```');

    $methodIndexTemplate->set('title', $method->getShortName());
    $methodIndexTemplate->set('href', '#' . UTF8::css_identifier($methodWithType));

    $functionsDocumentation[$method->getShortName()] = $methodTemplate->format();
    $functionsIndex[$method->getShortName()] = $methodIndexTemplate->format();
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

file_put_contents(__DIR__ . '/README_TEST.md', $documentTemplate->format());
