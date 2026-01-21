<?php

namespace App\Enums;

enum ProgramStatus: string
{
    case TERDAFTAR = 'TERDAFTAR';
    case DIBAHAS_PU = 'DIBAHAS_PU';
    case CATATAN_KL = 'CATATAN_KL';
    case KONSOLIDASI_PEMDA = 'KONSOLIDASI_PEMDA';
    case BERITA_ACARA = 'BERITA_ACARA';
}
