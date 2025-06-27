<?php

// Set the content type of the response to JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Allow requests from any origin
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

/**
 * Converts a Gregorian date string to a Hijri date string.
 *
 * @param string $gregorianDateString The date to convert, in 'd/m/Y' format (e.g., '27/07/2025').
 * @param string $format The desired output format for the Hijri date.
 * @param string $locale The locale to use for formatting (e.g., 'ar-SA' for Arabic, Saudi Arabia).
 * @return string|null The formatted Hijri date string, or null on failure.
 */
function gregorianToHijri(string $gregorianDateString, string $format = 'yyyy/MM/dd', string $locale = 'ar-SA'): ?string
{
    $gregorianDate = DateTime::createFromFormat('d/m/Y', $gregorianDateString);

    if ($gregorianDate === false) {
        return null;
    }

    $hijriFormatter = new IntlDateFormatter(
        $locale . '@calendar=islamic-umalqura',
        IntlDateFormatter::FULL,
        IntlDateFormatter::FULL,
        'UTC',
        IntlDateFormatter::TRADITIONAL,
        $format
    );

    return $hijriFormatter->format($gregorianDate);
}

// --- API Logic ---

$response = [];

// Handle pre-flight OPTIONS request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}


// Get the raw POST data
$jsonInput = file_get_contents('php://input');
// Decode the JSON input
$inputData = json_decode($jsonInput, true);

// Check if a date was provided in the JSON payload
if (isset($inputData['date']) && !empty($inputData['date'])) {
    $dateToConvert = $inputData['date'];

    // Use a regex to validate the d/m/Y format
    if (!preg_match('/^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[0-2])\/\d{4}$/', $dateToConvert)) {
        http_response_code(400); // Bad Request
        $response['status'] = 'error';
        $response['message'] = "Invalid date format. Please use 'dd/mm/yyyy'.";
    } else {
        // Try to convert the date and get multiple formats
        $hijriNumeric = gregorianToHijri($dateToConvert, 'yyyy-MM-dd');
        
        if ($hijriNumeric === null) {
            http_response_code(400); // Bad Request
            $response['status'] = 'error';
            $response['message'] = "Could not parse the provided date. Make sure it is a valid Gregorian date.";
        } else {
            http_response_code(200); // OK
            $response['status'] = 'success';
            $response['message'] = 'Date converted successfully.';
            $response['data'] = [
                'gregorian_date' => $dateToConvert,
                'hijri_date' => [
                    'numeric' => $hijriNumeric,
                    'long_arabic' => gregorianToHijri($dateToConvert, 'd MMMM, yyyy', 'ar-SA'),
                    'full_arabic' => gregorianToHijri($dateToConvert, 'eeee, d MMMM, yyyy G', 'ar-SA'),
                ]
            ];
        }
    }
} else {
    // If no date is provided, return an error
    http_response_code(400); // Bad Request
    $response['status'] = 'error';
    $response['message'] = "No date provided. Please send a JSON payload with a 'date' key. e.g., {\"date\": \"27/07/2025\"}";
}

// Encode the response array to JSON and output it
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

?>
