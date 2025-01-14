<?php

namespace App\Jobs;

use App\Models\FileHistory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateFileComparisonJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public FileHistory $fileHistory
    )
    {
        //
    }
    public function handle(): void
    {
        $history = $this->fileHistory;
        $file = $this->fileHistory->file;
        $prevVersion = $history->version-0.1;
        $prevVersion =$file->histories()->where('version',$prevVersion)->first();
        if(!$prevVersion)return;
        
    }
}
