<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;

class FileComparisonService
{
    public function compare($path1, $path2)
    {
        $file1 = Storage::disk('public')->get($path1);
        $file2 = Storage::disk('public')->get($path2);
        $differ = new Differ(new UnifiedDiffOutputBuilder());
        $diff = $differ->diff($file1, $file2);
        // file_put_contents(public_path('diff.txt'), $diff);
        return $diff;
    }
}
