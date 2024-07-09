<?php

namespace App\Console\Commands;

use App\Http\Helpers\Helper;
use App\Models\Chat;
use App\Models\Divar;
use App\Models\Group;
use App\Models\Tab;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;
use PhpParser\Node\Expr\AssignOp\Div;

class TabCreator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tab:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make automatic tab list and send to channels';

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

        $divars = Divar::where('validated', true)->where('blocked', false)->inRandomOrder()/*->whereIn('chat_username', ['perspoliswallpapers', 'esteghlalwallpapers'])*/
        ->get();
        $tabCounts = Tab::where('processed', false)->count();
        if (count($divars) == 0 && $tabCounts == 0) return; //all tabs   created and send


        Helper::sendMessage(Helper::$logs[0], "Divar To Tab " . count($divars), null);


        foreach ($divars as $d) {
            if (!Chat::where('chat_id', "$d->chat_id")->where('auto_tab', true)->exists())
                continue;
//            $count = $this->getChatMembersCount("$d->chat_id");
//            if ($count >= 20 && $this->botIsAdminAndHasPrivileges($d->chat_id)) {
            $g = Group::where('id', $d->group_id)->first();

            Tab::create(['chat_id' => "$d->chat_id", 'chat_title' => $d->chat_description, 'chat_type' => $d->chat_type, 'group' => " $g->emoji " . "#$g->name",
                'chat_username' => $d->chat_username, 'members' => $d->members, 'user_id' => $d->user_id,
                'created_at' => Carbon::now(), 'message_id' => null, 'processed' => false]);
//            }
//            $d->processed = true;
//            $d->save();
        }

        $c = Tab::where('processed', false)->count();
        if ($c == 0)
            return; //all tab lists created and send to sendmessages
        Helper::sendMessage(Helper::$logs[0], "Tabs To Channels " . $c, null);


        Helper::$tabListLimit = $this->calculateListsCount(Tab::where('processed', false)->count());

        while (Tab::where('processed', false)->count() > 0) {
            $tabs = Tab::where('processed', false)->inRandomOrder()
                /*->orderBy('group')*/
                ->take(Helper::$tabListLimit)->get();
            $txt = "🔗 🔗 قفل کانال تا 8 صبح 🔗 🔗 " . PHP_EOL;
            $txt .= "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
            $txt .= "🅼🅰🅶🅽🅴🆃 🅶🆁🅰🅼" . PHP_EOL;
            $txt .= "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;

            foreach ($tabs as $tab) {
                $dsc = explode("\n", $tab->chat_title)[0];
                $txt .= $tab->group . PHP_EOL;
//                $txt .= "🌍 " . $tab->chat_title . PHP_EOL;
                $txt .= " 💬 " . (mb_strlen($dsc) < 100 ? $dsc : mb_substr($dsc, 0, 100) . " ... ") . PHP_EOL;
                $txt .= ("🔗 " . $tab->chat_username) . PHP_EOL;

//                $txt .= "〰️〰️〰️〰️〰️〰️〰️〰️〰️〰️〰️" . PHP_EOL;
                $txt .= "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
            }

            $adv = "🟣🔵🟢🔴محل تبلیغ شما🔴🟢🔵🟣" . PHP_EOL . PHP_EOL;// "🔵🟣تبلیغات ارزان با مگنت گرام🟣🔵";
//            $adv = "💄محصولات آرایشی و مراقبتی💄
//👑با دیبادخت ، بهترینِ خودت باش👑
//🛒💌ارسال به سراسر ایران💌🛒
//💻مشاوره رایگان و سفارش:💻
//📺 instagram.com/diba_cosmetic72
//💰کسب درآمد از بازاریابی محصولات💰
//@dibadokhtonline";
            $adv .= "🛍 بازارچه اینترنتی ورتا 🛍
🎁 محصولات خودتو رایگان ثبت کن 🎁
@vartashopbot

        💙❤️ ثبت تبلیغات شما  ❤️💙
💙❤️ در اپلیکیشن استقلال پرسپولیس  ❤️💙
@vartastudiobot
";
            $adv = "\xD8\x9C" . "➕➖➖➖➖➖➖➖➖➖➖➖➕" . PHP_EOL . $adv . PHP_EOL . "\xD8\x9C" . "➕➖➖➖➖➖➖➖➖➖➖➖➕" . PHP_EOL;
            $txt .= $adv;
            $txt .= Jalalian::forge('today')->format('%A, %d %B %Y') . PHP_EOL;
            $txt .= "💫 ربات لینکدونی، فروشگاه و تبادل مگنت گرام 💫" . PHP_EOL . Helper::$bot . PHP_EOL;
            $txt .= "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
            $txt .= "🅼🅰🅶🅽🅴🆃 🅶🆁🅰🅼" . PHP_EOL;
            $txt .= "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
            $txt .= "🔗 🔗 قفل کانال تا 8 صبح 🔗 🔗 " . PHP_EOL . " ";
            $res = Helper::sendMessage(Helper::$divarChannel, $txt, null);

            if ($res && $res->ok == true) {
                $message_id = $res->result->message_id;

                foreach ($tabs as $tab) {
                    $tab->processed = true;
                    $tab->save();
                    DB::table('queue')->insert(['from_id' => Helper::$divarChannel, 'message_id' => $message_id,
                        'id' => "$tab->chat_id", 'for' => 't']);
                }

            }

        }
        Helper::sendMessage(Helper::$logs[0], "Tab Lists Added To Queue Success ! " . $c, null);

        \Illuminate\Support\Facades\Artisan::call('send:messages');

    }

    private
    function getChatMembersCount($chat_id)
    {
        $res = Helper::creator('getChatMembersCount', ['chat_id' => $chat_id,]);
        if ($res->ok == true)
            return (int)$res->result; else return 0;
    }

    private
    function botIsAdminAndHasPrivileges($chat_id)
    {


        $res = Helper::creator('getChatMember', [
            'chat_id' => "$chat_id",
            'user_id' => Helper::$bot_id
        ]);
//        Helper::sendMessage(Helper::$logs[0], json_encode($res), null);

        if ($res->ok == false)
            return false;// $res->description;
        elseif ($res->result->status != "administrator" ||
            !$res->result->can_post_messages ||
            !$res->result->can_edit_messages ||
            !$res->result->can_delete_messages)
            return false;
        else return true;


    }

    private function calculateListsCount($count)
    {
        $min = 5;
        $max = 10;


        $cluster = [5 => null, 6 => null, 7 => null, 8 => null, 9 => null, 10 => null];

        $res = null;
        for ($test = $max; $test >= $min; $test--) {
//
            $cluster[$test] = $test * ceil($count / $test) - $count;


        }
        $num = $max;
        $m = min($cluster);
        foreach ($cluster as $idx => $item) {
            if ($item == $m) {
                $num = $idx;
//                break;
            }
        }

        return $num; //100;
    }
}
