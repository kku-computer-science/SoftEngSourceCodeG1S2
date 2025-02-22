<?php

namespace App\Console\Commands;

use App\Http\Utility\UserUtility;
use Illuminate\Console\Command;

use App\Models\User;

class demo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo';

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
     * @return int
     */
    public function handle()
    {
        $teacher = User::role('teacher')->get();

        echo "Teacher\n";

        foreach($teacher as $t){
            echo $t->fname_en . " " . $t->lname_en . "\n";
        }
        echo "";

        return 0;
    }
}
