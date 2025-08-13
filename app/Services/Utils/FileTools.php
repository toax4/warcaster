<?php

namespace App\Services\Utils;

class FileTools
{
    /**
     * Retourne le checksum d'un fichier
     *
     * @param string $filePath  Chemin absolu ou relatif du fichier
     * @param string $algo      Algorithme de hash (ex: sha256, sha1, md5)
     * @return string|null      Le checksum ou null si fichier introuvable
     */
    public static function getFileChecksum(string $filePath, string $algo = 'sha256'): ?string
    {
        if (!file_exists($filePath)) {
            return null;
        }

        return hash_file($algo, $filePath);
    }
}