<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;
class Statistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statistics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'statistics 21:00 run';

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
        //这里做任务的具体处理，可以用模型
        Log::info('任务调度'.time());
    }
}
