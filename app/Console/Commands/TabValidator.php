<?php

namespace App\Console\Commands;

use App\Http\Helper;
use App\Models\Divar;
use App\Models\Group;
use App\Models\Tab;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;
use PhpParser\Node\Expr\AssignOp\Div;

class TabValidator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tab:validate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'validate member counts and bot is admin';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Tehran');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
// check tab validate every 15 min


//        $tabs = DB::table('queue')->whereNotNull('divar_to_tab')->get();

        $divars = Divar::where('blocked', false)->where('processed', false)->get();

        if (count($divars) <= 1) {
            Divar::query()->update(['processed' => false]);
            $divars = Divar::where('blocked', false)->where('processed', false)->get();
        }
//        Helper::sendMessage(Helper::$logs[0], "tab validate " . count($divars), null);


        foreach ($divars as $d) {
            $count = Helper::getChatMembersCount("$d->chat_id");
            $d->members = $count;
            sleep(1);
            if ($count >= 20 && Helper::botIsAdminAndHasPrivileges("$d->chat_id")) {
                $d->validated = true;
            } else {
                $d->validated = false;
            }
            $d->processed = true;
            $d->save();
        }
//        Helper::sendMessage(Helper::$logs[0], "tab validate finished" . count($divars), null, null, null, true);

    }


}
