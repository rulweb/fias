<?php

namespace marvin255\fias\transport;

/**
 * Загрузка файла через сокет небольшими чанками.
 */
class Chunked implements ITransport
{
    /**
     * @inheritdoc
     */
    public function load($from, \marvin255\fias\file\IFile $file)
    {
        $chunksize = 8192;

        $arUrl = parse_url($from);

        $hLocal = fopen($file->getPath(), 'wb');
        $hRemote = fsockopen(
            $arUrl['host'],
            empty($arUrl['port']) ? 80 : $arUrl['port'],
            $errstr,
            $errcode,
            50
        );
        if ($hRemote === false) {
            throw new Exception("Can't open socket to remote {$errstr}($errcode)");
        }

        $request = "GET {$arUrl['path']}" . (empty($arUrl['query']) ? '' : "?{$arUrl['query']}") . " HTTP/1.1\r\n";
        $request .= "Host: {$arUrl['host']}\r\n";
        $request .= "User-Agent: Mozilla/5.0\r\n";
        $request .= "Keep-Alive: 115\r\n";
        $request .= "Connection: keep-alive\r\n\r\n";
        fwrite($hRemote, $request);

        $headers = [];
        while(!feof($hRemote)) {
            $line = fgets($hRemote);
            if ($line == "\r\n") break;
            if (preg_match('/HTTP\S+\s(\d{3})\s\S+/', $line, $matches)) {
                $headers['status'] = $matches[1];
            } else {
                $arExplode = array_map('trim', explode(':', $line));
                $headers[$arExplode[0]] = $arExplode[1];
            }
        }

        if ($headers['status'] !== '200') {
            throw new Exception("Socket responses with status {$headers['status']}");
        } elseif (!isset($headers['Content-Length']) || intval($headers['Content-Length']) === 0) {
            throw new Exception("Socket returns an empty Content-Length header");
        }

        $start = microtime(true);

        $cnt = 0;
        while (!feof($hRemote)) {
            $buf = '';
            $buf = fread($hRemote, $chunksize);
            $bytes = fwrite($hLocal, $buf);
            if ($bytes === false) {
                throw new Exception("Can't write bytes to file on byte {$cnt}");
            }
            $cnt += $bytes;
            if ($cnt >= $headers['Content-Length']) {
                break;
            }
        }

        fclose($hLocal);
        fclose($hRemote);
    }
}
