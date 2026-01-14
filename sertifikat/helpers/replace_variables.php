<?php
/**
 * Replace Variables Helper
 * Replaces template variables with participant data
 * 
 * Usage:
 * $content = "Nama: {nama}, Instansi: {instansi}, Peran: {peran}";
 * $data = ['nama' => 'John Doe', 'instansi' => 'PT ABC', 'peran' => 'Manager'];
 * echo replaceVariables($content, $data);
 * // Output: "Nama: John Doe, Instansi: PT ABC, Peran: Manager"
 */

/**
 * Replace all {field} placeholders with values from participant data
 * 
 * @param string $content Content with placeholders like {nama}, {instansi}, {peran}
 * @param array $participantData Associative array with replacement values
 * @return string Content with variables replaced
 */
function replaceVariables($content, $participantData = [])
{
    if (!is_string($content)) {
        return '';
    }

    if (empty($participantData) || !is_array($participantData)) {
        $participantData = [];
    }

    // Pattern: {field_name} where field_name contains only word characters
    $pattern = '/\{(\w+)\}/';

    // Replace function that substitutes placeholder with participant data value
    $replacement = function($matches) use ($participantData) {
        $fieldName = $matches[1]; // Get the field name from regex capture group
        
        // Return value if exists in participantData, otherwise empty string
        return isset($participantData[$fieldName]) ? 
            (string)$participantData[$fieldName] : '';
    };

    // Use preg_replace_callback for secure replacement
    return preg_replace_callback($pattern, $replacement, $content);
}

/**
 * Validate participant data contains required fields
 * 
 * @param array $participantData Participant data to validate
 * @param array $requiredFields Required field names (default: nama, instansi, peran)
 * @return bool True if all required fields are present and non-empty
 */
function validateParticipantData($participantData, $requiredFields = ['nama', 'instansi', 'peran'])
{
    if (!is_array($participantData)) {
        return false;
    }

    foreach ($requiredFields as $field) {
        if (empty($participantData[$field])) {
            return false;
        }
    }

    return true;
}

/**
 * Extract all placeholder variables from content
 * 
 * @param string $content Content to scan for placeholders
 * @return array Array of found variable names
 */
function extractVariables($content)
{
    if (!is_string($content)) {
        return [];
    }

    $pattern = '/\{(\w+)\}/';
    $matches = [];

    if (preg_match_all($pattern, $content, $found)) {
        return $found[1]; // Return captured groups (variable names)
    }

    return [];
}
?>
