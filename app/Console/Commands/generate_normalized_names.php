<?php

namespace App\Console\Commands;

use App\Beer;
use App\Helpers\StringHelper;
use Illuminate\Console\Command;

class generate_normalized_names extends Command
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
                $beer->normalized_name = StringHelper::normalize($beer->name);
                $beer->save();
                $this->info("Normalized name for {$beer->name}: {$beer->normalized_name}");
            }
        }
    }
}
