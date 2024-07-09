<?php

namespace App\Console\Commands;

use App\Http\Helpers\Helper;
use App\Models\Chat;
use App\Models\Divar;
use App\Models\Group;
use App\Models\User;
use Illuminate\Console\Command;
use Carbon\Carbon;

class RandomDivar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:randomdivar';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'رندوم در دیوار میزاره';

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
        while (true) {
            $user = User::where('score', '>=', 0 /*Helper::$vip_limit*/)->inRandomOrder()->first();
            $chat = Chat::where('user_id', $user->id)->where('active', true)->inRandomOrder()->first();
            if (!$chat)
                continue;
            $divar = Divar::where('chat_id', $chat->chat_id)->first();

            $info = $this->getChatInfo($chat->chat_id);
            if ($info == null || !isset($info->username) || $info->username == null) {
                Helper::sendMessage(Helper::$admin, "کانال شما:" . " $chat->chat_username " . PHP_EOL . "برنده قرعه کشی ثبت در دیوار شد ولی ربات کانال شما را پیدا نکرد. لطفا ربات را در کانال خود قرار داده و اطلاعات کانال را بروزرسانی کنید و یا این پیام را نادیده بگیرید.", null);
                Helper::sendMessage($user->telegram_id, "کانال شما:" . " $chat->chat_username " . PHP_EOL . "برنده قرعه کشی ثبت در دیوار شد ولی ربات کانال شما را پیدا نکرد. لطفا ربات را در کانال خود قرار داده و اطلاعات کانال را بروزرسانی کنید و یا این پیام را نادیده بگیرید.", null);
                $chat->delete();
                if ($divar)
                    $divar->delete();
                continue;
            }
            if ('@' . $info->username != $chat->chat_username) {
                $chat->chat_username = '@' . $info->username;
                $chat->chat_title = $info->title;
                $chat->chat_description = $info->description;
                $chat->save();
            }

            $line = array(
                "➖➖➖➖➖➖➖➖➖➖➖",
                "🕳️🕳️🕳️🕳️🕳️🕳️🕳️🕳️🕳️🕳️🕳️",
                "〰️〰️〰️〰️〰️〰️〰️〰️〰️〰️〰️",
                // "🔸🔸🔸🔸🔸🔸🔸🔸🔸🔸",
                "🕶🕶🕶🕶🕶🕶🕶🕶🕶🕶",
                "🚥🚥🚥🚥🚥🚥🚥🚥🚥🚥",
                // "▪️▪️▪️▪️▪️▪️▪️▪️▪️▪️",
                "🧈🧈🧈🧈🧈🧈🧈🧈🧈🧈",
                "🛹🛹🛹🛹🛹🛹🛹🛹🛹🛹",
                "🛶🛶🛶🛶🛶🛶🛶🛶🛶🛶",
                "⌨️⌨️⌨️⌨️⌨️⌨️⌨️⌨️⌨️⌨️",
                "💳💳💳💳💳💳💳💳💳💳",
                "♾♾♾♾♾♾♾♾♾♾"
            );

            $idx = array_rand($line);
            $line = $line[$idx];


            $g = Group::where('id', $chat->group_id)->first();
            if (!$g)
                continue;
            $caption = (" $g->emoji " . "#$g->name") . PHP_EOL;
            $caption .= /*PHP_EOL .*/
                "\xD8\x9C" . "$line" . PHP_EOL /*. PHP_EOL*/
            ;
            $caption .= "🌍 " . $chat->chat_title . PHP_EOL;
            $caption .= ("🔗 " . "$chat->chat_username") . PHP_EOL;
            $caption .= '👤Admin: ' . ($user->telegram_username != "" && $user->telegram_username != "@" ? "$user->telegram_username" :
                    "[$user->name](tg://user?id=$user->telegram_id)") . PHP_EOL;
            $caption .= /*PHP_EOL .*/
                "$line" . PHP_EOL /*. PHP_EOL*/
            ;
            $caption .= "💬 " . (mb_strlen($chat->chat_description) < 150 ? $chat->chat_description : mb_substr($chat->chat_description, 0, 150)) . " ... " . PHP_EOL;
            $caption .= /*PHP_EOL .*/
                "\xD8\x9C" . "$line" . PHP_EOL /*. PHP_EOL*/
            ;

            $caption .= "✅جایزه عضویت: " . Helper::$follow_score . PHP_EOL;

            $r = Helper::$remain_member_day_limit;

            $caption .= "⛔جریمه لفت دادن ($r روز): " . Helper::$follow_score * 2 . PHP_EOL;
            $caption .= /*PHP_EOL . */
                "$line" . PHP_EOL /*. PHP_EOL*/
            ;
            $caption .= "👔گروه تخصصی ادمین های تلگرام👔" . PHP_EOL;
            $caption .= '@magnetgram_admins' . PHP_EOL;
            $caption .= "💫ربات دیوار، فروشگاه و تبادل 💫" . PHP_EOL . PHP_EOL;
            $caption .= Helper::$bot . PHP_EOL;
            $caption .= PHP_EOL . "🅼🅰🅶🅽🅴🆃 🅶🆁🅰🅼" . PHP_EOL . PHP_EOL;
//            $caption .= "🔥💰🎭🥇🏆🎁🎈🎉🎀🎊" . PHP_EOL;
//            $caption .= "ادمین این کانال حداقل " . Helper::$vip_limit . " امتیاز دارد و جایزه عضویت از طرف مگنت گرام است" . PHP_EOL;
//            $caption .= "🔥💰🎭🥇🏆🎁🎈🎉🎀🎊" . PHP_EOL;

            $cell_button = json_encode(['inline_keyboard' => [

                [['text' => "👈 ورود 👉", 'url' => "https://t.me/" . str_replace('@', '', $chat->chat_username)],
                    ['text' => "✅ عضو شدم(" . Helper::$follow_score . "امتیاز)", 'callback_data' => "divar_i_followed_vip$$chat->chat_id"]],

            ], 'resize_keyboard' => true]);

            $txt = "🎉🎀🎊" . " تبریک! " . PHP_EOL . "کانال شما در دیوار قرار گرفت و جایزه عضویت آن از طرف مگنت گرام است!" . PHP_EOL . "لطفا ربات را حتما در کانال خود قرار دهید" . PHP_EOL . " پشتیبانی " . Helper::$admin;


            $res = Helper::sendMessage($user->telegram_id, $txt, null);
//            if ($res && $res->ok == false)

            $message = Helper::sendPhoto(Helper::$divarChannel, asset("https://qr-image-creator.com/magnetgram/storage/chats/$chat->image.jpg"), Helper::MarkDown($caption), null, $cell_button);
            $message = Helper::sendPhoto('@lamassaba', asset("https://qr-image-creator.com/magnetgram/storage/chats/$chat->image.jpg"), Helper::MarkDown($caption), null, $cell_button);

            break;
        }
    }

    private
    function getChatInfo($chat_id)
    {
        $res = Helper::creator('getChat', ['chat_id' => $chat_id]);
        if (isset($res->result) && $res->ok == true)
            return $res->result;
        else
            return null;
    }


}
