<?php

namespace App\Console\Commands;

use App\Beer;
use Illuminate\Console\Command;
use Larabeers\Utils\NormalizeString;

class GenerateNormalizedNames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'beer:normalize_names';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a normalized name for the beers without one';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach (Beer::all() as $beer) {
            if (!$beer->normalized_name) {
                $beer->normalized_name = NormalizeString::execute($beer->name);
                $beer->save();
                $this->info("Normalized name for {$beer->name}: {$beer->normalized_name}");
            }
        }
    }
}
