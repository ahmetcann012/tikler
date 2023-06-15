<?php

function downloadYouTubeVideo($videoUrl, $resolution = 'highest')
{
    // YouTube'dan video indirmek için gerekli kodlar
    $videoId = getYouTubeVideoId($videoUrl);
    $videoInfoUrl = "https://www.youtube.com/get_video_info?video_id=$videoId";
    $videoInfo = file_get_contents($videoInfoUrl);
    parse_str($videoInfo, $videoData);

    $streamMap = explode(",", $videoData['url_encoded_fmt_stream_map']);

    $selectedStream = null;
    $selectedResolution = null;

    foreach ($streamMap as $stream) {
        parse_str($stream, $streamData);

        if (isset($streamData['quality_label'])) {
            // Çözünürlük mevcutsa quality_label kullanılır
            if ($streamData['quality_label'] == $resolution) {
                $selectedStream = $streamData;
                $selectedResolution = $resolution;
                break;
            }
        } else {
            // quality_label mevcut değilse itag kullanılır
            if ($streamData['itag'] == $resolution) {
                $selectedStream = $streamData;
                $selectedResolution = $resolution;
                break;
            }
        }
    }

    if ($selectedStream) {
        $videoUrl = urldecode($selectedStream['url']);

        // İndirme işlemi gerçekleştirilir
        $outputFile = $videoId . '_' . $selectedResolution . '.mp4';
        file_put_contents($outputFile, fopen($videoUrl, 'r'));

        // İndirilen videoyu mp3'e dönüştürmek için burada gerekli kodlar olacak
        // ...

        echo "Video indirme tamamlandı: $outputFile";
    } else {
        echo "Belirtilen çözünürlük bulunamadı.";
    }
}

function getYouTubeVideoId($videoUrl)
{
    $parsedUrl = parse_url($videoUrl);
    parse_str($parsedUrl['query'], $query);
    return $query['v'];
}

// Örnek kullanım:
$youtubeUrl = "https://www.youtube.com/watch?v=MDK8fV1yqYU"; // İndirilecek video URL'sini buraya girin
$resolution = 'hd720'; // İndirilecek çözünürlüğü buraya girin
downloadYouTubeVideo($youtubeUrl, $resolution);

?>
