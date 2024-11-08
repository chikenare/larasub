<?php

namespace Err0r\Larasub\Commands;

use Illuminate\Console\Command;

class LarasubCommand extends Command
{
    public $signature = 'larasub';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
