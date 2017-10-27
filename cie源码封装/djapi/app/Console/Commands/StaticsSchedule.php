<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Repositories\V0\PubRepository;

class StaticsSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'statics_pmatch';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'pmatch result statics at 1:00 every day';
	protected $pub_repo;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(PubRepository $pub_repo)
    {
		$this->pub_repo	= $pub_repo;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		/**
		 * 约战胜率每日统计[每天凌晨1点更新]
		 */
		$data = $this->pub_repo->getPmatchResultStatics();
		echo($data);
    }


}
