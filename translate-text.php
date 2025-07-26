<?php
/**
 * Google Translate API Endpoint
 * Handles translation requests from the frontend
 */

// Set JSON header
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['text']) || !isset($input['target'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing required parameters']);
    exit;
}

$text = $input['text'];
$target = $input['target'];

// Skip if text is empty or target is English
if (empty($text) || $target === 'en') {
    echo json_encode(['success' => true, 'translatedText' => $text]);
    exit;
}

// Skip translation for numbers, currency, and special content
if (preg_match('/^\$?[\d,\.]+%?$/', $text) || is_numeric($text)) {
    echo json_encode(['success' => true, 'translatedText' => $text]);
    exit;
}

$apiKey = getenv('GOOGLE_TRANSLATE_API_KEY');
if (!$apiKey) {
    echo json_encode(['success' => false, 'error' => 'API key not configured']);
    exit;
}

// Language code mapping
$languageMap = [
    'so' => 'so',  // Somali
    'vi' => 'vi',  // Vietnamese  
    'zh' => 'zh',  // Chinese (Simplified)
    'en' => 'en'   // English
];

$targetLang = isset($languageMap[$target]) ? $languageMap[$target] : $target;

try {
    // Prepare Google Translate API request
    $url = 'https://translation.googleapis.com/language/translate/v2?key=' . $apiKey;
    
    $postData = [
        'q' => $text,
        'target' => $targetLang,
        'source' => 'en',
        'format' => 'text'
    ];
    
    $options = [
        'http' => [
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($postData)
        ]
    ];
    
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    
    if ($response === FALSE) {
        throw new Exception('Failed to connect to Google Translate API');
    }
    
    $result = json_decode($response, true);
    
    if (isset($result['data']['translations'][0]['translatedText'])) {
        $translatedText = $result['data']['translations'][0]['translatedText'];
        
        // Decode HTML entities that might be returned by the API
        $translatedText = html_entity_decode($translatedText, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        echo json_encode([
            'success' => true,
            'translatedText' => $translatedText,
            'originalText' => $text,
            'targetLanguage' => $targetLang
        ]);
    } else {
        $errorMessage = isset($result['error']['message']) ? $result['error']['message'] : 'Unknown API error';
        echo json_encode([
            'success' => false,
            'error' => 'Translation failed: ' . $errorMessage
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Translation error: ' . $e->getMessage()
    ]);
}
?>