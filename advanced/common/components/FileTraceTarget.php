<?php
/**
 * Created by PhpStorm.
 * User: yuyj
 * Date: 11/5
 * Time: 14:37
 */

namespace common\components;


use yii\log\FileTarget;

class FileTraceTarget extends FileTarget
{
    public function formatMessage($message)
    {

        list($text, $level, $category, $timestamp) = $message;
        $level = Logger::getLevelName($level);
        if (!is_string($text)) {
            // exceptions may not be serializable if in the call stack somewhere is a Closure
            if ($text instanceof \Exception) {
                $text = (string) $text;
            } else {
                $text = VarDumper::export($text);
            }
        }
        $traces = [];
        if (isset($message[4])) {
            foreach ($message[4] as $trace) {
                $traces[] = "in {$trace['file']}:{$trace['line']}";
            }
        }

        $prefix = $this->getMessagePrefix($message);
        exit('ddddddd');
        if (count($traces) == 1) {
            $result = date(
                    'Y-m-d H:i:s',
                    $timestamp
                ) . " {$prefix}[$level][$category] $text" . (empty($traces) ? '' : $traces[0]);
        } else {
            $result = date(
                    'Y-m-d H:i:s',
                    $timestamp
                ) . " {$prefix}[$level][$category] $text" . (empty($traces) ? '' : "\n    " . implode(
                        "\n    ",
                        $traces
                    ));
        }

        return $result;
    }
}