<?php
require_once __DIR__ . '/../config/ai.php';

function generateText($input) {
    $model  = 'models/gemini-2.0-flash-lite';
    $url = "https://generativelanguage.googleapis.com/v1beta/$model:generateContent?key=" . urlencode(geminiAPIKey);
    $data = [
        "contents" => [         
            [
                "parts" => [   
                    ["text" => $input]
                ]
            ]
        ],
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt($ch, CURLOPT_POST, true); 
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $response = curl_exec($ch); 
    if (curl_errno($ch)) {
        echo "Curl error: " . curl_error($ch);
    }
    curl_close($ch);

    return json_decode($response, true)['candidates'][0]['content']['parts'][0]['text'];
}
?>