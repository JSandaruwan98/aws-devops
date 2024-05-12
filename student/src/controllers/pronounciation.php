<?php

$url = 'https://pronunciation-assessment1.p.rapidapi.com/pronunciation';
$headers = [
    'Content-Type: application/json',
    'X-RapidAPI-Key: 24c2caf12cmsh2931583209525f1p1e80b1jsn63d4d600054f',
    'X-RapidAPI-Host: pronunciation-assessment1.p.rapidapi.com',
];

// Replace 'path/to/your/audio/file.wav' with the actual path to your WAV file

$inputFile = '.recording2.wav';
$audioFilePath = 'a1.mp3';

$ffmpegCommand = 'ffmpeg -i ' . escapeshellarg($inputFile) . ' -b:a 192K ' . escapeshellarg($audioFilePath);

exec($ffmpegCommand, $output, $returnCode);

// Check if the file exists
if (!file_exists($audioFilePath)) {
    die('Error: Audio file not found');
}

// Read the audio file content
$audioData = file_get_contents($audioFilePath);

// Check if reading the file was successful
if ($audioData === false) {
    die('Error: Unable to read the audio file');
}

// Check if the audio data is empty
if (empty($audioData)) {
    die('Error: Audio data is empty');
}

$data = [
    'audio_base64' => base64_encode($audioData),
    'audio_format' => 'mp3',
    'text' => 'I really like green apples',
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Curl error: ' . curl_error($ch);
}

curl_close($ch);

echo $response;

unlink('a1.mp3');

//echo $data['text'];


$data1 = json_decode($response, true);
//print_r( $data1['words']);
//echo  $response['words'];

// Extract scores and confidence levels for each word
$words_data = $data1['words'];
$new_array = array();
foreach ($words_data as $word) {
    $new_array[] = array(
        'label' => $word['label'],
        'score' => $word['score'],
        'confidence' => $word['phones'][0]['confidence']
    );
}

$string_representation = json_encode($new_array);

echo $string_representation;

?>
