<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 19-11-13
 * Time: 上午11:00
 */

namespace App\Logger;

use EasySwoole\Component\Singleton;

class Logger
{
    use Singleton;

    /**
     * 自定义的日志写入函数
     * @param $text
     * @param null $fileName
     * @param string $level
     * @param string $dataType
     * @return bool
     */
    public function log($text, $fileName = null, $level = 'info', $dataType = "json")
    {
        $filePath = EASYSWOOLE_LOG_DIR . "/" . date("Y-m-d") . "/";
        if ($fileName == null) {
            $fileName = "log.log";
        }
        $fileName = $filePath . $fileName;

        if ($dataType == "json") {
            $text     = (is_object($text) || is_array($text)) ? json_encode($text) : $text;
            $contents = "[--" . $level . "--]";
            $contents .= "[" . date("Y-m-d H:i:s", time()) . "]";
            $contents .= "  " . $text;
            $contents .= "\r\n";
            $path     = dirname($fileName);
            !is_dir($path) && mkdir($path, 0755, true);

            $filesize = abs(filesize($path));
            if ($filesize >= (1024 * 1024 * 300)) {
                return false;
            }
            if (is_file($fileName) && floor(209715200) <= filesize($fileName)) {
                rename($fileName, dirname($fileName) . "/" . time() . '-' . basename($fileName));
                // 删除源文件
                unlink($fileName);
            }

            file_put_contents($fileName, $contents, FILE_APPEND);

            unset($text);
            unset($path);
            unset($contents);
        } else {
            file_put_contents($fileName, date("Y-m-d H:i:s", time()) . "  " . var_export($text, true) . "\r\n", FILE_APPEND);
        }
        return true;
    }
}
