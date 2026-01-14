<?php
/**
 * Fabric.js Canvas to HTML Converter
 * Converts Fabric.js canvas JSON to HTML for PDF generation
 * 
 * Usage:
 * $canvasJson = '{"objects":[...],"background":"..."}';
 * $html = fabricCanvasToHtml($canvasJson, ['nama' => 'John', ...]);
 * // Pass $html to Dompdf
 */

require_once __DIR__ . '/replace_variables.php';

/**
 * Convert Fabric.js canvas JSON to HTML
 * 
 * @param string $canvasJson Canvas JSON from fabric.canvas.toJSON()
 * @param array $participantData Optional participant data for variable replacement
 * @return string HTML ready for Dompdf
 */
function fabricCanvasToHtml($canvasJson, $participantData = [])
{
    // Parse JSON
    if (is_string($canvasJson)) {
        $canvasData = json_decode($canvasJson, true);
        if ($canvasData === null) {
            return '<div style="width: 800px; height: 566px; background: white;">Error: Invalid JSON</div>';
        }
    } else {
        $canvasData = $canvasJson;
    }

    // Canvas dimensions (A4 landscape)
    $width = $canvasData['width'] ?? 800;
    $height = $canvasData['height'] ?? 566;
    $backgroundColor = $canvasData['backgroundColor'] ?? 'white';

    // Background image URL
    $backgroundImage = $canvasData['backgroundImage'] ?? null;
    $backgroundStyle = '';

    if (!empty($backgroundImage)) {
        $bgUrl = $backgroundImage;
        // Handle data URIs and URLs
        if (strpos($bgUrl, 'data:') === 0 || strpos($bgUrl, 'http') === 0) {
            $backgroundStyle = "background-image: url('{$bgUrl}'); background-size: cover; background-position: center;";
        } else {
            $backgroundStyle = "background-color: {$backgroundColor};";
        }
    } else {
        $backgroundStyle = "background-color: {$backgroundColor};";
    }

    // Start HTML container
    $html = "<div style=\"position: relative; width: {$width}px; height: {$height}px; {$backgroundStyle} overflow: hidden; font-family: Arial, sans-serif;\">";

    // Process canvas objects
    $objects = $canvasData['objects'] ?? [];

    foreach ($objects as $obj) {
        $html .= fabricObjectToHtml($obj, $participantData);
    }

    // Close container
    $html .= '</div>';

    return $html;
}

/**
 * Convert single Fabric.js object to HTML
 * 
 * @param array $obj Fabric object data
 * @param array $participantData Participant data for variable replacement
 * @return string HTML for the object
 */
function fabricObjectToHtml($obj, $participantData = [])
{
    $type = $obj['type'] ?? 'text';

    // Handle text objects
    if ($type === 'text' || $type === 'i-text') {
        return fabricTextToHtml($obj, $participantData);
    }

    // Handle rectangles
    if ($type === 'rect') {
        return fabricRectToHtml($obj);
    }

    // Handle circles
    if ($type === 'circle') {
        return fabricCircleToHtml($obj);
    }

    // Handle lines
    if ($type === 'line') {
        return fabricLineToHtml($obj);
    }

    // Handle images
    if ($type === 'image') {
        return fabricImageToHtml($obj);
    }

    return '';
}

/**
 * Convert Fabric text object to HTML
 * 
 * @param array $obj Fabric text object
 * @param array $participantData For variable replacement
 * @return string HTML div with positioned text
 */
function fabricTextToHtml($obj, $participantData = [])
{
    $left = isset($obj['left']) ? (int)$obj['left'] : 0;
    $top = isset($obj['top']) ? (int)$obj['top'] : 0;
    $fontSize = isset($obj['fontSize']) ? (int)$obj['fontSize'] : 16;
    $fill = $obj['fill'] ?? '#000000';
    $fontWeight = $obj['fontWeight'] ?? 'normal';
    $fontStyle = $obj['fontStyle'] ?? 'normal';
    $textAlign = $obj['textAlign'] ?? 'left';
    $width = isset($obj['width']) ? (int)$obj['width'] : 'auto';
    $height = isset($obj['height']) ? (int)$obj['height'] : 'auto';

    $text = $obj['text'] ?? '';

    // Replace variables if participant data provided
    if (!empty($participantData)) {
        $text = replaceVariables($text, $participantData);
    }

    // Escape HTML
    $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

    $style = "position: absolute; left: {$left}px; top: {$top}px; font-size: {$fontSize}px; color: {$fill}; font-weight: {$fontWeight}; font-style: {$fontStyle}; text-align: {$textAlign};";

    if ($width !== 'auto') {
        $style .= " width: {$width}px;";
    }

    if ($height !== 'auto') {
        $style .= " height: {$height}px; overflow: hidden;";
    }

    return "<div style=\"{$style}\">{$text}</div>";
}

/**
 * Convert Fabric rectangle to HTML
 * 
 * @param array $obj Fabric rect object
 * @return string HTML div styled as rectangle
 */
function fabricRectToHtml($obj)
{
    $left = isset($obj['left']) ? (int)$obj['left'] : 0;
    $top = isset($obj['top']) ? (int)$obj['top'] : 0;
    $width = isset($obj['width']) ? (int)$obj['width'] : 50;
    $height = isset($obj['height']) ? (int)$obj['height'] : 50;
    $fill = $obj['fill'] ?? '#ffffff';
    $stroke = $obj['stroke'] ?? 'none';
    $strokeWidth = $obj['strokeWidth'] ?? 0;

    $borderStyle = 'none';
    if (!empty($stroke) && $stroke !== 'none') {
        $borderStyle = "{$strokeWidth}px solid {$stroke}";
    }

    $style = "position: absolute; left: {$left}px; top: {$top}px; width: {$width}px; height: {$height}px; background-color: {$fill}; border: {$borderStyle};";

    return "<div style=\"{$style}\"></div>";
}

/**
 * Convert Fabric circle to HTML
 * 
 * @param array $obj Fabric circle object
 * @return string HTML div styled as circle
 */
function fabricCircleToHtml($obj)
{
    $left = isset($obj['left']) ? (int)$obj['left'] : 0;
    $top = isset($obj['top']) ? (int)$obj['top'] : 0;
    $radius = isset($obj['radius']) ? (int)$obj['radius'] : 25;
    $fill = $obj['fill'] ?? '#ffffff';
    $stroke = $obj['stroke'] ?? 'none';
    $strokeWidth = $obj['strokeWidth'] ?? 0;

    $diameter = $radius * 2;
    $borderStyle = 'none';
    if (!empty($stroke) && $stroke !== 'none') {
        $borderStyle = "{$strokeWidth}px solid {$stroke}";
    }

    $style = "position: absolute; left: {$left}px; top: {$top}px; width: {$diameter}px; height: {$diameter}px; background-color: {$fill}; border: {$borderStyle}; border-radius: 50%;";

    return "<div style=\"{$style}\"></div>";
}

/**
 * Convert Fabric line to HTML
 * 
 * @param array $obj Fabric line object
 * @return string HTML div styled as line
 */
function fabricLineToHtml($obj)
{
    $x1 = isset($obj['x1']) ? (int)$obj['x1'] : 0;
    $y1 = isset($obj['y1']) ? (int)$obj['y1'] : 0;
    $x2 = isset($obj['x2']) ? (int)$obj['x2'] : 50;
    $y2 = isset($obj['y2']) ? (int)$obj['y2'] : 50;
    $stroke = $obj['stroke'] ?? '#000000';
    $strokeWidth = $obj['strokeWidth'] ?? 1;

    // Calculate line dimensions
    $minX = min($x1, $x2);
    $minY = min($y1, $y2);
    $length = sqrt(pow($x2 - $x1, 2) + pow($y2 - $y1, 2));
    $angle = atan2($y2 - $y1, $x2 - $x1) * 180 / M_PI;

    $style = "position: absolute; left: {$minX}px; top: {$minY}px; width: {$length}px; height: {$strokeWidth}px; background-color: {$stroke}; transform: rotate({$angle}deg); transform-origin: 0 50%;";

    return "<div style=\"{$style}\"></div>";
}

/**
 * Convert Fabric image object to HTML
 * 
 * @param array $obj Fabric image object
 * @return string HTML img tag with positioning
 */
function fabricImageToHtml($obj)
{
    $left = isset($obj['left']) ? (int)$obj['left'] : 0;
    $top = isset($obj['top']) ? (int)$obj['top'] : 0;
    $width = isset($obj['width']) ? (int)$obj['width'] : 100;
    $height = isset($obj['height']) ? (int)$obj['height'] : 100;
    $src = $obj['src'] ?? '';

    if (empty($src)) {
        return '';
    }

    // Escape src for HTML attribute
    $src = htmlspecialchars($src, ENT_QUOTES, 'UTF-8');

    $style = "position: absolute; left: {$left}px; top: {$top}px; width: {$width}px; height: {$height}px; object-fit: cover;";

    return "<img src=\"{$src}\" style=\"{$style}\" alt=\"\" />";
}

/**
 * Wrap HTML in complete document for Dompdf
 * 
 * @param string $content Inner HTML content
 * @return string Complete HTML document
 */
function wrapInDocument($content)
{
    return <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Certificate</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
        }
    </style>
</head>
<body>
{$content}
</body>
</html>
HTML;
}
?>
