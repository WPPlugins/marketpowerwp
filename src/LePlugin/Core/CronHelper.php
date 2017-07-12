<?php

namespace LePlugin\Core;

class CronHelper {

    private $pid;
    private $lockDir;
    private $lockFileName;

    const LOCK_SUFFIX = ".lock";

    public function __construct($lockDir, $lockFileName = "") {
        $this->lockDir = $lockDir;
        $this->lockFileName = $lockFileName;
    }

    function __clone() {
        
    }

    public function is_cli() {
        if (defined('STDIN')) {
            return true;
        }

        if (empty($_SERVER['REMOTE_ADDR']) && !isset($_SERVER['HTTP_USER_AGENT']) && count($_SERVER['argv']) > 0) {
            return true;
        }

        return false;
    }

    private function isrunning() {
        $pids = explode(PHP_EOL, `ps -e | awk '{print $1}'`);
        if (in_array($this->pid, $pids)) {
            return true;
        }
        return false;
    }

    public function lock() {
        global $argv;
        if ($this->lockFileName != "") {
            $lock_file = $this->lockDir . $this->lockFileName . self::LOCK_SUFFIX;
        } else {
            $lock_file = $this->lockDir . $argv[0] . self::LOCK_SUFFIX;
        }
        if (file_exists($lock_file)) {
            //return FALSE;
            // Is running?
            $this->pid = file_get_contents($lock_file);
            if ($this->isrunning()) {
                error_log("==" . $this->pid . "== Already in progress...");
                return false;
            } else {
                error_log("==" . $this->pid . "== Previous job died abruptly...");
            }
        }

        $this->pid = getmypid();
        file_put_contents($lock_file, $this->pid);
        error_log("==" . $this->pid . "== Lock acquired, processing the job...");
        return $this->pid;
    }

    public function unlock() {
        global $argv;
        if ($this->lockFileName != "") {
            $lock_file = $this->lockDir . $this->lockFileName . self::LOCK_SUFFIX;
        } else {
            $lock_file = $this->lockDir . $argv[0] . self::LOCK_SUFFIX;
        }

        if (file_exists($lock_file)) {
            unlink($lock_file);
        }
        error_log("==" . $this->pid . "== Releasing lock...");
        return true;
    }

}

?>