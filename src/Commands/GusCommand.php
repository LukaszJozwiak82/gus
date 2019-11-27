<?php
namespace Ljozwiak\Gus\Commands;

use Illuminate\Console\Command;

class GusCommand extends Command {

    protected $signature = 'make:gus';

    protected $description = 'Command description';

    public function __construct() {
        parent::__construct();
    }

    public function handle() {
        $this->info('Installing Gus package...');

        $this->call('vendor:publish', [
            '--provider' => "Ljozwiak\Gus\GusServiceProvider",
            '--tag' => "views"
        ]);

        $this->info('Publishing configuration...');

        $this->call('vendor:publish', [
            '--provider' => "Ljozwiak\Gus\GusServiceProvider",
            '--tag' => "config"
        ]);

        $this->info('Installed Gus package!');
    }

}
