<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Throwable;

trait Logger
{
    public function log_exception(Throwable $e,array $details,$channel ='app'){
        //remove unneeded lines
        $stackTrace = $this->filterStackTrace($e->getTraceAsString());
        $details['stackTrace'] = $stackTrace;
        $this->log_error($e->getMessage(),$details,$channel);
    }

    public function log_error($message,array $details,$channel = "app"){
        $detailsString = "";
        foreach($details as $key => $value){
            $value=is_array($value)?json_encode($value):$value;
            $detailsString .= "#$key \n$value".PHP_EOL.PHP_EOL;
        }
        Log::channel($channel)->error(
            $message.PHP_EOL.$detailsString
        );
    }

    private function filterStackTrace($stackTrace){
        // Split the stack trace into lines
        $stackTraceLines = explode("\n", $stackTrace);
        // // Filter out lines containing Laravel's files
        $filteredStackTraceLines = array_filter($stackTraceLines, function($line) {
            return !strpos($line, "laravel\\framework\\src");
        });
        // Reconstruct the filtered stack trace string
        $filteredStackTrace = implode("\n", $filteredStackTraceLines);
        return $filteredStackTrace;
    }

}
