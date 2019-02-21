<?php

class Filesystem
{
    /**
     * @param array $patterns
     */
    public function deleteFilesByPattern(array $patterns)
    {
        foreach ($patterns as $pattern) {
            $files = glob($pattern);
            foreach($files as $file){
                if(is_file($file)){
                    unlink($file);
                }
            }
        }
    }
}