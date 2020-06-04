<?php

// sample
// php write_prac.php [start or end or total]

class PracLog
{
    /** ファイル名 */
    const FILE_NAME = 'prac.log';

    /** レコードタイプ
     * @var string $recordType start or end
     */
    private $recordType;

    function __construct($argv) {
        $this->recordType = $argv[1];
    }

    public function exec()
    {
        $currentTime = date('Y-m-d H:i:s');

        if (in_array($this->recordType, ['start', 'end'])) {
            $this->_writeFile(self::FILE_NAME, $this->recordType, $currentTime);
        } elseif($this->recordType === 'total') {
            print_r('TotalTime: ' . $this->_getTotalTime(self::FILE_NAME, $currentTime) . PHP_EOL);
        }
    }

    /**
     * 最後の行の時間と現在の時間の差分を返す
     * 
     * @param string $filePath ファイル名
     * @param string $currentTime 現在日時
     * @return string H:i:s
     */
    private function _getTotalTime($filePath, $currentTime)
    {
        $from = $this->_getFinalLine($filePath);

        return $this->_getTimeInterval($from, $currentTime);
    }

    /**
     * 二つの引数の日時の差分を返す
     *      
     * @param string $from
     * @param string $to 現在日時
     * @return string H:i:s
     */
    private function _getTimeInterval($from, $to) 
    {
        //DateTimeクラス
        $objFrom = new DateTime($from);
        $objTo = new DateTime($to);

        //diffメソッド
        //ふたつの日付の差をあらわす DateInterval オブジェクトを返します。
        $objInterval = $objFrom->diff($objTo);

        $diffTime = $objInterval->format('%H:%I:%S');

        //結果を返す
        return $diffTime;
    }

    /**
     * 指定ファイルの最後の行を返す
     * 
     * @param string $filePath ファイルパス
     * @return string 最期の行の文字列 [2019-12-31 12:15:18]
     */
    private function _getFinalLine($filePath)
    {
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        return array_pop($lines);
    }

    /**
     * 指定した引数のファイルに書き込む
     * 
     * @param string $fileName ファイル名
     * @param string $recordType 記録のタイプ
     * @param string $currentTime 現在の日時
     */
    private function _writeFile($fileName, $recordType, $currentTime)
    {
        // 追加モードでファイルを開く
        $fp = fopen($fileName, "a");

        if ($recordType == 'start') {
            $text = <<< EOM
$currentTime
EOM;
        } elseif ($recordType == 'end') {
            $totalTime = $this->_getTotalTime($fileName, $currentTime);
            $current_time = substr($currentTime, -8);
            $text = <<< EOM
 ~ $current_time ( $totalTime )\n\n
EOM;
        }

        // 書き込む
        fwrite($fp, $text);

        // ファイルを閉じる
        fclose($fp);
    }
}

if (empty($argv[1]) || !in_array($argv[1], ['start', 'end', 'total'])) {
    print_r('正しい引数を入力してください。' . PHP_EOL);

    return true;
}

$prac = new PracLog($argv);
$prac->exec();