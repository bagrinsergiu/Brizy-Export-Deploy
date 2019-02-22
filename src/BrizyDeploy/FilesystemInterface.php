<?php

namespace BrizyDeploy;

interface FilesystemInterface
{
    /**
     * @param array $patterns
     */
    public function deleteFilesByPattern(array $patterns);

//    public function createReserveCopy();
}