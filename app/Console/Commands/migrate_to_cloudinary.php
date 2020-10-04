<?php
namespace App\Console\Commands;

use App\Sticker;
use Cloudinary\Uploader;
use Illuminate\Console\Command;

class migrate_to_cloudinary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'image:migrate_to_cloudinary {batch_size=100}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrates images from google drive to cloudinary';

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
        \Cloudinary::config([
            'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
            'api_key' => env('CLOUDINARY_API_KEY'),
            'api_secret' => env('CLOUDINARY_API_SECRET'),
            'secure' => true
        ]);

        $batch_size = $this->argument('batch_size');
        $stickers = Sticker::where('path', 'like', '%google%')->limit($batch_size)->get();
        echo " - " . count($stickers) . " Will be migrated\n";
        foreach($stickers as $i => $sticker) {
            $upload = Uploader::upload($sticker->path);
            $sticker->path = $upload['url'];
            $sticker->save();
        }
    }
}
