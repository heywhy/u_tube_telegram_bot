<?php

namespace App\Console\Commands\Telegram;

use App\Support\App;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class PollBotUpdates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'poll:bot-updates {--sleep=10}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        while (true) {
            $this->line('<info>[' . Carbon::now()->format('Y-m-d H:i:s') . ']</info> Calling telegram for updates');

            // $this->call('schedule:run');
            App::process(false);

            sleep($this->option('sleep'));
        }
    }
}
