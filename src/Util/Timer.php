<?php namespace Pauldro\Minicli\Util;

class Timer {
    public $startTime = 0;
    public $endTime = 0;


    public function start() {
        $this->startTime = time();
    }

    public function end() {
        $this->endTime = time();
    }

    public function getDuration() : string
    {
        $duration = abs($this->endTime - $this->startTime);

        $units = [
            'y' => $duration / 31556926 % 12,
            'w' => $duration / 604800 % 52,
            'd' => $duration / 86400 % 7,
            'h' => $duration / 3600 % 24,
            'm' => $duration / 60 % 60,
            's' => $duration % 60
        ];
        $parts = [];

        foreach ($units as $unit => $value) {
            if ($value < 1) {
                continue;
            }
            $parts[] = $value . $unit;
        }
        if (empty($parts)) {
            return "0s";
        }
        return implode(" ", $parts);
    }
}