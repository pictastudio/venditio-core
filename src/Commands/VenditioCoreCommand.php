<?php

namespace PictaStudio\VenditioCore\Commands;

use Illuminate\Console\Command;

class VenditioCoreCommand extends Command
{
    public $signature = 'venditio-core';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
