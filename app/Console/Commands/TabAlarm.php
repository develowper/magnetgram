<?php

namespace App\Console\Commands;

use App\Chat;
use App\Divar;
use App\Group;
use App\Tab;
use Carbon\Carbon;
use Helper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;
use PhpParser\Node\Expr\AssignOp\Div;

class TabAlarm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tab:alarm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'allowed channels until now';

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
//create tabs from divar bot is admin and member >20


//        $tabs = DB::table('queue')->whereNotNull('divar_to_tab')->get();

        $divars = Divar::where('validated', true)->where('blocked', false)/*->whereIn('chat_username', ['perspoliswallpapers', 'esteghlalwallpapers'])*/
        ->get();
        if (count($divars) == 0) return; //all tabs   created and send
        Helper::sendMessage(Helper::$logs[0], "tab alarm " . count($divars), null);


        $txt = "" . PHP_EOL;
        $txt .= "🚥لیست کانال های مجاز در تبادل امشب🚥" . PHP_EOL;
        $txt .= "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
        $txt .= "🅼🅰🅶🅽🅴🆃 🅶🆁🅰🅼" . PHP_EOL;
        $txt .= "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;


        foreach ($divars as $d) {
            if (!Chat::where('chat_id', "$d->chat_id")->where('auto_tab', true)->exists())
                continue;
//            $count = $this->getChatMembersCount("$d->chat_id");

//            if ($count >= 20 && $this->botIsAdminAndHasPrivileges("$d->chat_id")) {
            $txt .= "🌍 " . $d->chat_username . PHP_EOL;
            $txt .= "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;

//            }
//            $d->processed = true;
//            $d->save();
        }
        $txt .= "🅼🅰🅶🅽🅴🆃 🅶🆁🅰🅼" . PHP_EOL;
        $txt .= "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
        $txt .= "🔗 🔗 کانال ها از 12 تا 8 صبح قفل خواهند شد 🔗 🔗" . PHP_EOL;
        $txt .= "🚥 تا 12 شب می تونید کانالتون رو به لیست اضافه کنید یا از لیست حذف کنید 🚥" . PHP_EOL;
        $txt .= "1⃣ درج کانال در دیوار (دیوار📈->ثبت در دیوار)" . PHP_EOL;
        $txt .= "2⃣ ادمین کردن ربات در کانال" . PHP_EOL;
        $txt .= "3⃣ فعال سازی تب اتوماتیک (مدیریت کانال ها📣->انتخاب کانال->تب اتوماتیک)" . PHP_EOL;
        $txt .= " ⛔️حذف ربات در بازه تبادل  = بلاک شدن کانال⛔️" . PHP_EOL;
        $txt .= "💫 ربات لینکدونی، فروشگاه و تبادل مگنت گرام 💫" . PHP_EOL . Helper::$bot . PHP_EOL;

        Helper::sendMessage(Helper::$divarChannel, $txt, null);
//        Divar::query()->update(['processed' => false]);
    }


}
