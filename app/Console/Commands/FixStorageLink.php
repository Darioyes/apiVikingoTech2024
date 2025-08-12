<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixStorageLink extends Command
{
    protected $signature = 'storage:fix-link';
    protected $description = 'Verifica y repara el enlace simbólico de storage';

    public function handle()
    {
        $linkPath = public_path('storage');
        $targetPath = storage_path('app/public');

        if (File::exists($linkPath)) {
            if (is_link($linkPath)) {
                $this->info('El enlace simbólico ya existe y es válido.');
                return;
            } else {
                $this->warn('Existe una carpeta llamada "storage" en public/, pero no es un enlace simbólico. Eliminando...');
                File::deleteDirectory($linkPath);
            }
        }

        $this->info('Creando enlace simbólico...');
        $this->call('storage:link');
        $this->info('✅ Enlace simbólico reparado correctamente.');
    }
}
