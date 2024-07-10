<?php

namespace App\Http\Controllers;


use App\Http\Helpers\Helper;
use App\Models\Chat;
use App\Models\Divar;
use App\Models\Follower;
use App\Models\Group;
use App\Models\Image;
use App\Models\InstaNeed;
use App\Models\Need;
use App\Models\Product;
use App\Models\Queue;
use App\Models\Ref;
use App\Models\Setting;


use App\Models\Shop;
use App\Models\Tab;
use App\Models\User;
use App\Models\UserChat;
use App\Models\Waiting;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Morilog\Jalali\Jalalian;
use PhpParser\Node\Expr\AssignOp\Div;
use PhpParser\Node\Stmt\Else_;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Traits\Http;
use function App\Http\Helpers\simple_color_thief;


class BotController extends Controller
{
    protected $Dev, $logs, $channel, $info, $user, $init_score, $ref_score, $install_chat_score,
        $follow_score, $add_score, $left_score, $divar_show_items, $divar_scores, $bot_id, $tut_link;
    //user selected  game type and click on find gamer
    //try to find gamer else connect to a bot

    public function __construct()
    {
        error_reporting(1);
        set_time_limit(-1);
        header("HTTP/1.0 200 OK");
        date_default_timezone_set('Asia/Tehran');
//--------[Your Config]--------//
        $this->Dev = Helper::$Dev; // آیدی عددی ادمین را از بات @userinfobot بگیرید
        $this->logs = Helper::$logs;
        $this->ref_score = Helper::$ref_score;
        $this->init_score = Helper::$init_score;
        $this->divar_show_items = Helper::$divar_show_items;
        $this->left_score = Helper::$left_score;
        $this->follow_score = Helper::$follow_score;
        $this->add_score = Helper::$add_score;
        $this->install_chat_score = Helper::$install_chat_score;
        $this->divar_scores = Helper::$divar_scores; //min
        $this->bot = Helper::$bot;
        $this->channel = Helper::$channel; // ربات را ادمین کانال کنید
        $this->info = Helper::$info;
        $this->bot_id = Helper::$bot_id;
        $this->tut_link = "https://www.aparat.com/playlist/449893";
//-----------------------------//
        define('API_KEY', env('TELEGRAM_BOT_TOKEN', 'YOUR-BOT-TOKEN')); // توکن ربات
    }


    public function getupdates(Request $request)
    {
        $update = json_decode(file_get_contents('php://input'));
        if (isset($update->message)) {
            $message = $update->message;
            $chat_id = $message->chat->id;
            $chat_username = '@' . $message->chat->username;
            $text = $message->text;
            $message_id = $message->message_id;
            $from_id = $message->from->id;
            $tc = $message->chat->type;
            $title = isset($message->chat->title) ? $message->chat->title : "";
            $first_name = isset($message->from->first_name) ? $message->from->first_name : "";
            $last_name = isset($message->from->last_name) ? $message->from->last_name : "";
            $username = isset($message->from->username) ? '@' . $message->from->username : "";
            //            $reply = isset($message->reply_to_message->forward_from->id) ? $message->reply_to_message->forward_from->id : "";
//            $reply_id = isset($message->reply_to_message->from->id) ? $message->reply_to_message->from->id : "";
            $reply = isset($message->reply_to_message) ? $message->reply_to_message : "";
            $new_chat_member = $update->message->new_chat_member; #id,is_bot,first_name,last_name,username
            $new_chat_members = $update->message->new_chat_members; #id,is_bot,first_name,last_name,username]
            $left_chat_member = $update->message->left_chat_member; #id,is_bot,first_name,username
            $new_chat_participant = $update->message->new_chat_participant; #id,username

            $animation = $update->message->animation;  #file_name,mime_type,width,height,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,
            $sticker = $update->message->sticker;  #width,height,emoji,set_name,is_animated,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,
            $photo = $update->message->photo; #file_id,file_unique_id,file_size,width,height] array of different photo sizes
            $document = $update->message->document; #file_name,mime_type,thumb[file_id,file_unique_id,file_size,width,height]
            $video = $update->message->video; #duration,width,height,mime_type,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,height]
            $audio = $update->message->audio; #duration,mime_type,title,performer,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,height]
            $voice = $update->message->voice; #duration,mime_type,file_id,file_unique_id,file_size
            $video_note = $update->message->video_note; #duration,length,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,height]
            $caption = $message->caption;
            $phone_number = isset($message->contact) ? $message->contact->phone_number : null;

        }
        if (isset($update->callback_query)) {
            $Data = $update->callback_query->data;
            $data_id = $update->callback_query->id;
            $chat_id = $update->callback_query->message->chat->id;
            $caption = $update->callback_query->message->caption;
            $from_id = $update->callback_query->from->id;
            $first_name = $update->callback_query->from->first_name;
            $last_name = $update->callback_query->from->last_name;
            $username = '@' . $update->callback_query->from->username;
            $tc = $update->callback_query->message->chat->type;
            $message_id = $update->callback_query->message->message_id;

        }
        if (isset($update->channel_post)) {
            $tc = $update->channel_post->chat->type;
            $text = $update->channel_post->text;
            $chat_id = $update->channel_post->chat->id;
            $chat_username = '@' . $update->channel_post->chat->username;
            $chat_title = $update->channel_post->chat->title;

            $message_id = $update->channel_post->message_id;


            $caption = $update->channel_post->caption;
            $photo = $update->channel_post->photo;
            $document = $update->channel_post->document;
            $video = $update->channel_post->video;
            $audio = $update->channel_post->audio;
            $voice = $update->channel_post->voice;
            $video_note = $update->channel_post->video_note;

            if (isset($update->channel_post->reply_to_message)) {
                $reply = $update->channel_post->reply_to_message->message_id;

            }
            if (isset($update->channel_post->forward_from_chat)) {
                $from_chat_id = $update->channel_post->forward_from_chat->id;
                $from_message_id = $update->channel_post->forward_from_message_id;
            }
            if (isset($update->channel_post->forward_from)) {
                $from_chat_id = $update->channel_post->forward_from->id;
//                $from_message_id = $update->channel_post->forward_from_id;
            }


//            $from_id = $this->Me($chat_id);
        }
//        return json_encode($from_id);
        // if ($new_chat_members || $new_chat_member || $left_chat_member){
        //     Storage::prepend('file.log', json_encode($update->message));

        //   $this->sendMessage("871016407", "$message", "Markdown", null, null);

        // }


//------------------------------------------------------------------------------
//        $rank = $this->user_in_chat($this->channel, $from_id, $tc);// $get['result']['status'];

//        $this->bot_id = Helper::creator('GetMe', [])->result->id;
//        $INSTALL_ICON = '🥒';
//        $ABOUT_ICON = '🤖';
//        $USER_EDIT_ICON = "✏";
//        $USER_REGISTER_ICON = "✅";
//        $CANCEL_REGISTER_ICON = "❌";
//
//        $INSTALL_BOT = " نصب ربات";
//        $ABOUT_BOT = " درباره ربات";
//        $USER_EDIT = "ویرایش اطلاعات";
//        $USER_REGISTER = " ثبت نام ";
//        $CANCEL_REGISTER = "لغو ثبت نام";

//        Helper::sendMessage(Helper::$logs[0], json_encode($update), null);

        if ($tc == 'private') {
            $this->getUserOrRegister($first_name, $last_name, $username, $from_id);

            if (!in_array($from_id, $this->Dev)) {
                $rank = $this->user_in_chat(Helper::$divarChannel, $from_id, $tc);// $get['result']['status'];
                if ($rank != 'creator' && $rank != 'administrator' && $rank != 'member') {
                    $this->sendMessage($from_id, "🌟 برای استفاده از امکانات ربات ابتدا در لینکدونی عضو شوید و مجدد /start را بزنید 🌟" . PHP_EOL . PHP_EOL . " 📌لینکدونی: " . Helper::$divarChannel, null, null, null);
                    return;
                }
            }


//            return (string)($USER_REGISTER . "\xE2\x9C\x85" == $text);
//            return (string)(0 == null);
//            return $this->user_in_channel("@lamassaba", $from_id);// == 'administrator' or 'creator'
//            return $this->user_in_channel("@twitterfarsi", $from_id);// Bad Request: user not found
//            return $this->user_in_channel("@twitteddrfarsi", $from_id);// Bad Request: chat not found

//            return json_encode($this->inviteToChat($this->channel));
            $buy_button = json_encode(['inline_keyboard' => [
                [['text' => "📪 ارتباط با ما 📪", 'url' => "telegram.me/" . 'develowper']],
                [['text' => "📌 دریافت بنر تبلیغاتی 📌", 'callback_data' => "بنر"]],
            ], 'resize_keyboard' => true]);

            $insta_button = json_encode(['keyboard' => [
                [['text' => '🧿ثبت پست اینستاگرام🧿']],
                [['text' => '👀 مشاهده دیوار 👀']],
                [['text' => 'سکه های من💰']],
                [['text' => 'منوی اصلی⬅']],
            ], 'resize_keyboard' => true]);
            $divar_button = json_encode(['keyboard' => [
                [['text' => '📌ثبت کانال در دیوار📌']],
                [['text' => '📍ثبت تبلیغ در دیوار📍']],
                [['text' => '👀 مشاهده دیوار 👀']],
                [['text' => 'سکه های من💰']],
                [['text' => 'منوی اصلی⬅']],
            ], 'resize_keyboard' => true]);
            $button = json_encode(['keyboard' => [
                in_array($from_id, $this->Dev) ? [['text' => 'پنل مدیران🚧']] : [],
                [['text' => '📈 دیوار 📈']],
                [
                    ['text' => '🛒 بازار 🛒'],
                    ['text' => '🧿تبادل اینستاگرام🧿'],
                    ['text' => '🎯تبادل یاب🎯']],
                [['text' => 'تگ اتوماتیک🏁'], ['text' => 'تبادل لیستی🔃']],

//                [/*['text' => 'ثبت گروه💥'],*/
//                    ['text' => 'ثبت کانال💥']
//                ],
                [/*['text' => 'مدیریت گروه ها📢'],*/
                    ['text' => 'مدیریت کانال ها📣']],
                [['text' => "🎴 ساخت دکمه شیشه ای 🎴"], ['text' => "📌 دریافت بنر تبلیغاتی 📌"]],
                [['text' => "📱 خرید شارژ 📱"], ['text' => "📱 خرید اینترنت 📱"], ['text' => "🙏 حمایت از ما 🙏"]],

                [['text' => 'سکه های من💰'], ['text' => $this->user ? "ویرایش اطلاعات✏" : "ثبت نام✅"] /*['text' => 'جریمه افراد لفت داده📛']*/],

                [['text' => 'درباره ربات🤖']],
            ], 'resize_keyboard' => true]);
            $cancelBazarButton = json_encode(['keyboard' => [
                [['text' => "خروج از بازار ❌"]],
            ], 'resize_keyboard' => true]);
            $cancel_button = json_encode(['keyboard' => [
                [['text' => "لغو ❌"]],
            ], 'resize_keyboard' => true]);
            $cancel_register_button = json_encode(['keyboard' => [
                [['text' => "لغو ثبت نام❌"]],
            ], 'resize_keyboard' => true]);
            $return_button = json_encode(['inline_keyboard' => [
                [['text' => "بازگشت⬅", 'callback_data' => "edit_cancel"]],
            ], 'resize_keyboard' => true]);
            $edit_button = json_encode(['inline_keyboard' => [
                [['text' => 'ویرایش نام', 'callback_data' => "edit_name"], ['text' => 'ویرایش گذرواژه', 'callback_data' => "edit_password"],],
            ], 'resize_keyboard' => true]);
            $admin_button = json_encode(['inline_keyboard' => [
                [['text' => "📬 ارسال همگانی به کاربران", 'callback_data' => 'send_to_users']],
                [['text' => "📬 ارسال همگانی به گروه ها", 'callback_data' => 'send_to_chats']],
                [['text' => "🚶 مشاهده کاربران", 'callback_data' => 'see_users']],
                [['text' => "🚶 مشاهده فالورها", 'callback_data' => 'see_followers']],
                [['text' => "❓ راهنمای دستورات", 'callback_data' => 'admin_help']],
                [['text' => "📊 آمار", 'callback_data' => 'statistics']],
            ], 'resize_keyboard' => true]);
            $send_cancel_button = json_encode(['inline_keyboard' => [
                [['text' => "لغو ارسال⬅", 'callback_data' => "send_cancel"]],
            ], 'resize_keyboard' => true]);


            if (preg_match('/^\/(start)$/i', $text)) {

                if (!$this->user) $this->sendMessage($chat_id, "■ سلام $first_name خوش آمدید\n\n■ برای ثبت کانال/گروه خود ابتدا در ربات ثبت نام کنید :" . " پشتیبانی: " . Helper::$admin, null, $message_id, $button);
                else  $this->sendMessage($chat_id, "■ سلام $first_name به مگنت گرام خوش اومدی✋\n  " . "⚡ توسط این ربات میتونی گروه و کانالتو در 📈دیوار (لینکدونی) ثبت کنی و یا 💫تبادل چرخشی شبانه اتوماتیک انجام بدی! برای شروع دکمه دیوار و سپس ثبت در دیوار (لینکدونی) رو بزن و کانالتو ثبت کن" . PHP_EOL . " لینکدونی (دیوار): " . Helper::$divarChannel . PHP_EOL . " پشتیبانی: " . Helper::$admin, null, $message_id, $button);
//                $first_name = $this->MarkDown($first_name);
//                $this->sendMessage($chat_id, " \n آموزش ربات\n" . $this->tut_link, null, $message_id, null);

                foreach ($this->logs as $log)
                    $this->sendMessage($log, "■  کاربر [$first_name](tg://user?id=$from_id) ربات مگنت گرام را استارت کرد.", 'MarkDown');

            }
//            elseif ($from_id == Helper::$logs[0]) {
//
//                Helper::sendMessage($from_id, $tags, null, null, null);
//            }
//            elseif ($rank != 'creator' && $rank != 'administrator' && $rank != 'member') {
//                $this->sendMessage($chat_id, "■ برای استفاده از ربات و همچنین حمایت از ما ابتدا وارد کانال\n● $this->channel\n■ شده سپس به ربات برگشته و /start را بزنید.", null, $message_id, json_encode(['KeyboardRemove' => [], 'remove_keyboard' => true]));
//
//            }
            elseif ($text == 'منوی اصلی⬅' || $Data == "mainmenu$") {
                $this->sendMessage($chat_id, "منوی اصلی", 'MarkDown', $message_id, $button);


            } elseif ($text == 'تبادل لیستی🔃') {
                $txt = "🚨  لطفا قبل از استفاده، *یکبار قوانین را مطالعه کنید*" . PHP_EOL . PHP_EOL;
                $txt .= "1⃣ *کانال خود را در دیوار ثبت کنید (دکمه دیوار)،از قسمت مدیریت کانال ها، تب اتوماتیک را فعال کنید و ربات را ادمین کانال خود کنید. شما در لیست تبادل خواهید بود*" . PHP_EOL;
                $txt .= "2⃣ کانال شما حداقل 20 پست و 20 عضو واقعی داشته باشد." . PHP_EOL;
                $txt .= "3⃣ ربات لیست تبادل را ساعت شروع تبادل به کانال های شما ارسال می کند و ساعت پایان تبادل آن را پاک می کند. تب روزانه ۲ تا ۳ ظهر و تب شبانه ۱۲ شب تا ۸ صبح است" . PHP_EOL . PHP_EOL;
                $txt .= "با انجام موارد زیر *در بازه های تبادل*، کانال شما برای همیشه از تبادل حذف خواهد شد:" . PHP_EOL . PHP_EOL;
                $txt .= "4⃣ *حذف پست تبادل* از کانال و یا *جابجایی آن* و *درج پست جدید* بعد از پست تبادل" . PHP_EOL;
                $txt .= "5⃣ *عکس و فیلم غیر اخلاقی*، *تعویض اسم و محتوای کانال*" . PHP_EOL;
                $txt .= "6⃣ لیستی که به کانال شما ارسال می شود برای کانال های آن لیست هم ارسال خواهد شد. در صورت مشاهده تخلف به پشتیبانی اطلاع دهید" . PHP_EOL;
                $txt .= " پشتیبانی: " . Helper::$admin . PHP_EOL;
                $this->sendMessage($chat_id, $txt, "Markdown", null, null);

            } elseif ($text == 'تگ اتوماتیک🏁') {
                $txt = "🏆 *با فعالسازی این قابلیت، هنگام کپی یا فورواد مطالب در کانال خود، تگ های اضافی حذف و تگ کانال شما زیر هر پست قرار میگیرد  *" . PHP_EOL . PHP_EOL;
                $txt .= "1⃣ *ابتدا ربات را ادمین کانال خود کنید*" . PHP_EOL;
                $txt .= "2⃣ *کانال خود را حداقل یک بار در دیوار ثبت کنید (دکمه دیوار->ثبت در دیوار)*" . PHP_EOL;
                $txt .= "3⃣ *از قسمت مدیریت کانال ها، کانال خود را انتخاب و دکمه تگ اتوماتیک را بزنید*" . PHP_EOL . PHP_EOL;

                $txt .= "در صورت مشاهده ایراد در عملکرد ربات،  به پشتیبانی اطلاع دهید" . PHP_EOL;
                $txt .= " پشتیبانی: " . Helper::$admin . PHP_EOL;
                $this->sendMessage($chat_id, $txt, "Markdown", null, null);

            } elseif ($text == "📱 خرید شارژ 📱" || $text == "📱 خرید اینترنت 📱") {
                $this->sendMessage($chat_id, "از طریق ربات دیگر ما، به راحتی و به سرعت شارژ و اینترنت تهیه کنید" . PHP_EOL . "@vartastudiobot", "Markdown", $message_id, null);

            } elseif ($text == "🙏 حمایت از ما 🙏") {
                $this->sendMessage($chat_id, "در صورت رضایت از ربات و در جهت رایگان ماندن خدمات ما، می توانید مبلغی را بعنوان حمایت از ربات پرداخت نماید." . PHP_EOL . "🙏 این کار را کاملا اختیاری و تنها در صورت رضایت انجام دهید 🙏" . "https://idpay.ir/vartastudio", "Markdown", $message_id, null);

            } elseif ($text == '🎯تبادل یاب🎯' || strpos($Data, "tabFinder$") !== false) {

                if ($text == '🎯تبادل یاب🎯' || $Data == 'tabFinder$') {
                    $group_id_button[] = [['text' => "همه", 'callback_data' => 'tabFinder$_']];
                    foreach (Group::where('id', '<', 30)->get() as $g) {
                        $group_id_button[] = [['text' => "$g->name $g->emoji", 'callback_data' => "tabFinder$$g->id"]];
                    }
                    $group_id_button = json_encode(['inline_keyboard' => $group_id_button, 'resize_keyboard' => true]);
                    Helper::sendMessage($from_id, "🎯 لیست تبادل از میان کانال های ثبت شده در دیوار که ربات در آن ها ادمین باشد انتخاب می شوند." . PHP_EOL . "موضوع کانال تبادل را انتخاب کنید:", null, null, $group_id_button);
                } elseif ($Data) {
                    $input = explode('$', $Data);
                    $query = Divar::query();
                    $query->whereNotNull('members')->where('members', '>', 0);
                    if ($input[1] != '_')
                        $query->where('group_id', $input[1]);
//                    Helper::sendMessage($from_id, count($input), null);
                    if (count($input) == 2) {

                        $c = [0, 0, 0, 0, 0, 0, 0, 0, 0];

                        foreach ($query->pluck('members') as $co) {

                            switch ($co) {
                                case $co <= 100:
                                    $c[0]++;
                                    break;
                                case $co > 100 && $co <= 500:
                                    $c[1]++;
                                    break;
                                case $co > 500 && $co <= 1000:
                                    $c[2]++;
                                    break;
                                case $co > 1000 && $co <= 2000:
                                    $c[3]++;
                                    break;
                                case $co > 2000 && $co <= 5000:
                                    $c[4]++;
                                    break;
                                case $co > 5000 && $co <= 10000:
                                    $c[5]++;
                                    break;
                                case $co > 10000 && $co <= 20000:
                                    $c[6]++;
                                    break;
                                case $co > 20000 && $co <= 50000:
                                    $c[7]++;
                                    break;
                                case $co > 50000:
                                    $c[8]++;
                                    break;
                            }
                        }

//                    $max = Divar::whereNotNull('members')->select('members')->orderBy('members', 'Desc')->first();

                        $members_button[] = [['text' => ' ( ' . $c[0] . ' ) ' . "زیر 100", 'callback_data' => 'tabFinder$' . $input[1] . '$_$100'],
                            ['text' => "100 🧍‍♂️ 500" . ' ( ' . $c[1] . ' ) ', 'callback_data' => 'tabFinder$' . $input[1] . '$100$500']];
                        $members_button[] = [['text' => "500 👫 1000" . ' ( ' . $c[2] . ' ) ', 'callback_data' => 'tabFinder$' . $input[1] . '$500$1000'],
                            ['text' => "1000 👫 2000" . ' ( ' . $c[3] . ' ) ', 'callback_data' => 'tabFinder$' . $input[1] . '$1000$2000']];
                        $members_button[] = [['text' => "2000 👨‍👩‍👦 5000" . ' ( ' . $c[4] . ' ) ', 'callback_data' => 'tabFinder$' . $input[1] . '$2000$5000'],
                            ['text' => "5000 👨‍👩‍👦 10,000" . ' ( ' . $c[5] . ' ) ', 'callback_data' => 'tabFinder$' . $input[1] . '$5000$10000']];
                        $members_button[] = [['text' => "10,000 👨‍👩‍👧‍👧 20,000" . ' ( ' . $c[6] . ' ) ', 'callback_data' => 'tabFinder$' . $input[1] . '$10000$20000'],
                            ['text' => "20,000 👨‍👩‍👧‍👧 50,000" . ' ( ' . $c[7] . ' ) ', 'callback_data' => 'tabFinder$' . $input[1] . '$20000$50000']];
                        $members_button[] = [['text' => "بالای 50,000" . ' ( ' . $c[8] . ' ) ', 'callback_data' => 'tabFinder$' . $input[1] . '$50000$_']];
                        $members_button[] = [['text' => "بازگشت⬅️", 'callback_data' => 'tabFinder$']];


                        $members_button = json_encode(['inline_keyboard' => $members_button, 'resize_keyboard' => true]);
                        $g = Group::where('id', $input[1])->first();
                        $this->EditMessageText($from_id, $message_id, "گروه انتخابی شما: " . ($g ? "$g->name $g->emoji" : " همه ") . PHP_EOL . "محدوده تعداد عضو را انتخاب کنید:", null, $members_button);

//                        $this->EditKeyboard($from_id, $message_id, $members_button);
                    } elseif (count($input) == 4) {
                        if ($input[2] != '_' && $input[3] != '_') {
                            $query->whereBetween('members', [$input[2], $input[3]]);
                        } elseif ($input[2] != '_') {
                            $query->where('members', '>', $input[2]);
                        } elseif ($input[3] != '_') {
                            $query->where('members', '<', $input[3]);
                        }
                        $txt = "🅼🅰🅶🅽🅴🆃 🅶🆁🅰🅼" . PHP_EOL . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
                        $i = 0;
                        $c = $query->count();
//                        Helper::sendMessage($from_id, $c, null);
                        foreach ($query->get(['user_id', 'chat_username']) as $d) {
                            $i++;
                            $user = User::where('id', $d->user_id)->first();
                            $txt .= ("🔗 " . $d->chat_username) . PHP_EOL;
                            $txt .= '👤Admin: ' . ($user->telegram_username != "" && $user->telegram_username != "@" ? "$user->telegram_username" :
                                    "[$user->name](tg://user?id=$user->telegram_id)") . PHP_EOL;
                            $txt .= "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
                            if ($i == 20 || $c == $i) {
                                $i = 0;
                                $txt .= "💫ربات دیوار و تبادل لیستی💫" . PHP_EOL;
                                $txt .= Helper::$bot . PHP_EOL . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
                                $now = Jalalian::forge('now');
                                $txt .= PHP_EOL . $now->format('%A, %d %B %Y ⏰ H:i');
                                $txt .= PHP_EOL . "🅼🅰🅶🅽🅴🆃 🅶🆁🅰🅼" . PHP_EOL;
                                Helper::sendMessage($from_id, Helper::MarkDown($txt), 'Markdown', null, null);
                                $txt = "🅼🅰🅶🅽🅴🆃 🅶🆁🅰🅼" . PHP_EOL . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
                            }
                        }


                    }
                }

            } elseif ($text == '📌ثبت کانال در دیوار📌' || $Data == "insert_divar") {

                if (!$this->user) {
                    $this->sendMessage($chat_id, "ابتدا از قسمت منوی اصلی در ربات ثبت نام نمایید.", "Markdown", $message_id, null);
                    return;
                }
                $groups_channels = array();
                foreach (Chat::where('user_id', $this->user->id)->get(['chat_id', 'chat_username']) as $gc) {
//                    $res = $this->user_in_chat($gc, $this->bot_id);
//                    if ($res == 'administrator' || $res == 'creator')
                    array_push($groups_channels, [['text' => $gc->chat_username, 'callback_data' => 'divar$' . $gc->chat_id]]);
                }
//                array_push($groups_channels, [['text' => '➕ثبت کانال/گروه جدید➕', 'callback_data' => 'divar$' . 'new']]);


                $help = json_encode(['inline_keyboard' => [[['text' => 'راهنمای تبدیل کانال به حالت public', 'callback_data' => 'help_public_channel']], [['text' => 'راهنمای اضافه کردن ربات به کانال', 'callback_data' => 'help_add_bot_channel']],], 'resize_keyboard' => true]);
//                    $this->sendMessage($chat_id, "🔹کانال شما باید در حالت  *public* باشد و با یک نام قابل شناسایی باشد. (مثال:$this->bot)\n🔹ربات را به کانال اضافه کنید.\n    در صورت داشتن هر گونه سوال به قسمت *درباره ربات* مراجعه نمایید. \n $this->bot ", 'Markdown', $message_id, $help);

                $cancelbutton = json_encode(['keyboard' => [
                    [['text' => "لغو ❌"]],
                ], 'resize_keyboard' => true]);

                $this->user->step = 2; // for register channel
                $this->user->save();
                $this->sendMessage($chat_id, "❓راهنمای ثبت کانال" . PHP_EOL .
                    "🚩 خط اول توضیحات کانال شما (بیو) به همراه تگ کانال شما، در لیست تبادل نمایش داده خواهد شد" . PHP_EOL .
                    "🚩در صورتی که می خواهید کاربران را تشویق به عضو شدن کنید ربات باید ادمین کانال شما باشد(اختیاری)" . PHP_EOL .
                    "🚩کانال خود را انتخاب کرده و گزینه مدیران (Administrators) را انتخاب کنید" . PHP_EOL .
                    "🚩گزینه جستجو را زده و نام ربات را سرچ کنید ( " . Helper::$bot . " ) و آن را انتخاب کنید تا به کانال اضافه شود" . PHP_EOL .
                    "🚧در صورت هر گونه راهنمایی پیام خود را ارسال کنید " . Helper::$admin
                    ,
                    null, $message_id, $cancelbutton);


                //***********

                if (count($groups_channels) == 0) {
                    $this->sendMessage($chat_id, "نام کانال خود را با @ وارد کنید \n مثال: " . PHP_EOL . "@vartastudio", 'MarkDown', $message_id, $cancelbutton);

//                    if ($text) $this->sendMessage($chat_id, "گروه/کانال ثبت شده ای ندارید\nابتدا از منوی اصلی *ثبت گروه یا کانال* را بزنید", null, $message_id, $divar_button);
                } else {
                    $groups_channels = json_encode(['inline_keyboard' => $groups_channels, 'resize_keyboard' => true]);
                    if ($Data) $this->EditMessageText($chat_id, $message_id, "🔥گزینه مورد نظر خود را برای درج در دیوار انتخاب کنید و یا اگر در دکمه های زیر نیست " . "نام کانال خود را با @ وارد کنید \n مثال: ", "Markdown", $groups_channels);
                    else $this->sendMessage($chat_id, "🔥گزینه مورد نظر خود را برای درج در دیوار انتخاب کنید و یا اگر در دکمه های زیر نیست " . "نام کانال خود را با @ وارد کنید \n مثال: " . PHP_EOL . "@vartastudio", "Markdown", $message_id, $groups_channels);
                }


            } elseif ($text == '📍ثبت تبلیغ در دیوار📍' || $Data == "insert_need") {

                $group_id_button = [];
                foreach (Group::where('id', '>=', 20)->where('id', '<', 30)->get() as $g) {
                    $group_id_button[] = [['text' => "$g->name $g->emoji", 'callback_data' => "add_need$$g->id"]];
                }
                $group_id_button = json_encode(['inline_keyboard' => $group_id_button, 'resize_keyboard' => true]);

                $txt = "📌 با استفاده از این بخش می توانید خدمات تلگرامی خود را در میان ادمین های تلگرام تبلیغ کنید." . PHP_EOL .
                    "  💳 هزینه هر ثبت: " . Helper::$add_needing_score . " سکه " . PHP_EOL . "  💰 سکه های شما: " . $this->user->score;
                $this->sendMessage($from_id, $txt, null, null, $group_id_button);

            } elseif (strpos($Data, "add_need$") !== false) {

                $splitter = explode("$", $Data);
                $groupId = count($splitter) > 1 ? $splitter[1] : null;
                $time = count($splitter) > 2 ? $splitter[2] : null;

                if ($time == null) {

                    if ($this->user->score < Helper::$add_needing_score) {
                        $this->popupMessage($data_id, "📛 سکه کافی برای این کار ندارید." . PHP_EOL . "💰 حداقل سکه مورد نیاز:" . Helper::$add_needing_score . PHP_EOL . "برای دریافت سکه، می توانید دریافت بنر تبلیغاتی را انتخاب کرده و سکه جمع کنید");
                        $this->DeleteMessage($chat_id, $message_id);
                        return;
                    }

                    $key = json_encode(['inline_keyboard' => [
                        [['text' => '🕐 ۶ ساعت:  ' . $this->divar_scores['6'] . 'سکه💰', 'callback_data' => "add_need$$groupId$6"]],
                        [['text' => '🕐 ۱۲ ساعت:  ' . $this->divar_scores['12'] . 'سکه💰', 'callback_data' => "add_need$$groupId$12"]],
                        [['text' => '🕐 ۲٤ ساعت: ' . $this->divar_scores['24'] . 'سکه💰', 'callback_data' => "add_need$$groupId$24"]],
                        [['text' => "بازگشت⬅", 'callback_data' => "insert_need"]],

                    ], 'resize_keyboard' => true]);
                    $this->EditMessageText($chat_id, $message_id, "مدت زمان نمایش در لینکدونی را وارد کنید", null, $key);
                } else {

                    $this->user->step = 12; // for register channel
                    $this->user->remember_token = $Data;
                    $this->user->save();
                    $this->DeleteMessage($chat_id, $message_id);
                    $this->sendMessage($from_id, "متن تبلیغ خود را ارسال کنید. (حداکثر ۲۵۵ کلمه)", null, null, $cancel_button);

                }
            } elseif (strpos($Data, "add_divar$") !== false) {


                $splitter = explode("$", $Data);
                $time = $splitter[1];
                $id = $splitter[2];
                $f_score = count($splitter) > 3 ? $splitter[3] : 0;
                $r_score = count($splitter) > 4 ? $splitter[4] : 0;
                $end = count($splitter) > 5 ? true : false;
                if ($end) {


                    if ($this->user->score < ($this->divar_scores[$time] + $f_score + $r_score)) {
                        $s = $this->divar_scores[$time] . '+' . $f_score . '+' . $r_score . ' = ';
                        $sc = $this->divar_scores[$time] + $f_score + $r_score;
                        $this->popupMessage($data_id, "📛 سکه کافی برای این کار ندارید." . PHP_EOL . "💰 حداقل سکه مورد نیاز:" . $s . $sc . PHP_EOL . "برای دریافت سکه، در کانال/گروه های دیگران عضو شوید و یا از قسمت  سکه های من  اقدام کنید");

                    } elseif ($f_score == 0 && $r_score > 0) {
                        $this->popupMessage($data_id, "📛 در صورت تعیین جایزه عضو گیری، جایزه عضویت هم ضروری است.");

                    } else {
                        $info = $this->getChatInfo($id);
                        if ($info == null || $info->username == null) {
                            $this->popupMessage($data_id, "کانال/گروهی با این نام کاربری وجود ندارد و یا ربات ادمین آن نیست!");
                            return;
                        }
                        $info_id = $info->id;
                        $divar_ids = Divar::pluck('chat_id')->toArray();
                        $queue_ids = Queue::pluck('chat_id')->toArray();

                        $divar = Divar::where('chat_id', "$info_id")->first();

                        $expireTime = Carbon::parse($divar->expire_time);
                        if (in_array($info_id, $divar_ids)) {

                            if ($expireTime > Carbon::now('Asia/Tehran')) {
                                $this->popupMessage($data_id, "📛این گروه/کانال از قبل در دیوار وجود دارد !" . PHP_EOL . "پس از اتمام زمان نمایش:" . PHP_EOL . Jalalian::fromCarbon($expireTime->setTimezone('Asia/Tehran')) . PHP_EOL . "می توانید مجدد آن را در دیوار قرار دهید");
                                return;
                            } elseif ($divar->blocked == true) {
                                $this->popupMessage($data_id, "📛این گروه/کانال به علت عدم رعایت قوانین بلاک شده است !" . PHP_EOL . "پس از اتمام زمان نمایش:" . PHP_EOL . Jalalian::fromCarbon($expireTime->setTimezone('Asia/Tehran')) . PHP_EOL . "می توانید مجدد آن را در دیوار قرار دهید");
                                return;
                            } else {
                                $divar->delete();
                                $this->DeleteMessage(Helper::$divarChannel, $divar->message_id);

                            }
                        }
                        if (in_array($info_id, $queue_ids)) {
                            $this->popupMessage($data_id, "📛این گروه/کانال در صف است و به محض خالی شدن دیوار ثبت خواهد شد!");
                            return;
                        }
//                    if (!$this->user_in_chat($id, $this->bot_id) == 'administrator') {
//                        $this->popupMessage($data_id, "ابتدا ربات را در گروه/کانال ادمین کنید!");
//                        return;
//                    }

                        if (Divar::count() < $this->divar_show_items) {

                            if (!Helper::addChatToDivar($info, $time, $f_score, $r_score)) return;


                            //                        Helper::addChatToDivar($info, $first_name, $last_name);
                            $this->DeleteMessage($chat_id, $message_id - 1);
                            Helper::sendMessage($chat_id, "🌹کانال شما با موفقیت در دیوار ثبت شد!" . PHP_EOL . "🚧پشتیبانی: " . Helper::$admin, 'MarkDown', null, $button);

                            $txt = "✅*گروه/کانال شما با موفقیت به دیوار اضافه شد!*";
//                        $this->sendMessage($from_id, $txt, 'MarkDown', null, null);

                            foreach ($this->logs as $log)
                                $this->sendMessage($log, "■ کاربر  [$first_name](tg://user?id=$from_id) کانال/گروه @$info->username را وارد دیوار کرد", null, null, null);

                            $ref = Ref::where('new_telegram_id', $from_id)->first();
                            if ($ref) {
                                $user = User::where('telegram_id', $ref->invited_by)->first();
                                if ($user) {
                                    $user->score += $this->ref_score;
                                    $user->save();
                                    $this->sendMessage($ref->invited_by, "■  کاربر [$first_name](tg://user?id=$from_id)  یک کانال را وارد دیوار کرد و $this->ref_score سکه به شما اضافه شد!  .", 'MarkDown', null, null);
                                }
                            }

                        } else {
                            $chat_type = $info->type == 'channel' ? 'c' : ($info->type == 'group' || $info->type == 'supergroup' ? 'g' : ($info->type == 'bot' ? 'b' : null));

                            $txt = "*به علت پر بودن دیوار, کانال/گروه شما در صف قرار گرفت و به محض خالی شدن دیوار, به آن اضافه خواهد شد.*";
                            Queue::create(['user_id' => $this->user->id, 'chat_id' => "$info_id", 'chat_type' => $chat_type, 'chat_username' => "@$info->username",
                                'chat_title' => $info->title, 'chat_description' => $info->description,
                                'chat_main_color' => Helper::simple_color_thief(storage_path("app/public/chats/$info_id.jpg")), 'show_time' => $time,]);
                            //'photo'=>small_file_id or small_file_unique_id
                        }
//                        Helper::createChatImage($info->photo, "$info_id");

                        $this->user->score -= $this->divar_scores[$time];
                        $this->user->save();
                        $return_button = json_encode(['inline_keyboard' => [
                            [['text' => "بازگشت⬅", 'callback_data' => "insert_divar"]],
                        ], 'resize_keyboard' => true]);
                        $this->sendMessage($chat_id, $txt, "Markdown", null, $divar_button);
                    }
                } else {
                    $reward_button = json_encode(['inline_keyboard' => [
                        [['text' => "👇", 'callback_data' => "add_divar$$time$$id$" . ($f_score - 1) . "$$r_score"], ['text' => "عضویت ( " . $f_score . " سکه) ", 'callback_data' => "."], ['text' => "👆", 'callback_data' => "add_divar$$time$$id$" . ($f_score + 1) . "$$r_score"]],
                        [['text' => "👇", 'callback_data' => "add_divar$$time$$id$$f_score$" . ($r_score - 1)], ['text' => "عضو گیری ( " . $r_score . " سکه) ", 'callback_data' => "."], ['text' => "👆", 'callback_data' => "add_divar$$time$$id$$f_score$" . ($r_score + 1)]],
                        [['text' => "✅تایید", 'callback_data' => "add_divar$$time$$id$$f_score$$r_score$" . "end"]],
                    ], 'resize_keyboard' => true]);
                    $this->EditMessageText($chat_id, $message_id, "می توانید برای عضویت و یا عضو گیری (بازاریابی) سکه تعیین کنید و یا صفر باقی بگذارید. مقدار سکه از سکه های شما کم خواهد شد." . PHP_EOL . "🎁 جایزه برای عضویت: $f_score" . PHP_EOL . "🔗 جایزه برای عضوگیری (بازاریابی): $r_score" . PHP_EOL . "در صورت لفت دادن افراد، زیر " . Helper::$remain_member_day_limit . " روز، سکه ها به شما باز خواهد گشت ", "Markdown", $reward_button);

                }
            } elseif (strpos($Data, "divar$") !== false) {
                $this->user->step = null;
                $this->user->save();
                $gc = explode("$", $Data)[1];

                $prices_button = json_encode(['inline_keyboard' => [
                    [['text' => '🕐 ۶ ساعت:  ' . $this->divar_scores['6'] . 'سکه💰', 'callback_data' => "add_divar$6$" . $gc]],
                    [['text' => '🕐 ۱۲ ساعت:  ' . $this->divar_scores['12'] . 'سکه💰', 'callback_data' => "add_divar$12$" . $gc]],
                    [['text' => '🕐 ۲٤ ساعت: ' . $this->divar_scores['24'] . 'سکه💰', 'callback_data' => "add_divar$24$" . $gc]],
                    [['text' => "بازگشت⬅", 'callback_data' => "insert_divar"]],

                ], 'resize_keyboard' => true]);

                $this->EditMessageText($chat_id, $message_id, "مدت زمان نمایش را انتخاب کنید:", "Markdown", $prices_button);

            } elseif ($text == 'سکه های من💰') {
                $score = $this->user->score;

                $this->sendMessage($from_id, "💰 سکه فعلی شما:$score \n  برای دریافت سکه می توانید کانال/گروه های موجود در دیوار را فالو کرده و یا بنر تبلیغاتی مخصوص خود را تولید کرده و یا از طریق دکمه ارتباط با ما اقدام به خرید سکه نمایید ", 'Markdown', $message_id, $buy_button);


            } elseif ($text == '👀 مشاهده دیوار 👀') {

                $this->sendMessage($chat_id, "t.me/" . substr(Helper::$divarChannel, 1, strlen(Helper::$divarChannel)), null, null, null);

//                $this->getDivar(1, $chat_id);

            } elseif ($text == "لغو ثبت گروه❌" || $text == "لغو ثبت کانال❌" || $text == "لغو ❌") {
                if ($this->user) {
                    $this->user->step = null; // for register channel
                    $this->user->remember_token = null;
                    $this->user->save();
                }
                $this->DeleteMessage($chat_id, $message_id - 2);
                $this->DeleteMessage($chat_id, $message_id - 1);
                $this->DeleteMessage($chat_id, $message_id);

                $this->sendMessage($chat_id, "با موفقیت لغو شد!", null, null, $button);

            } elseif ($text == '🧿تبادل اینستاگرام🧿' || $Data == 'cancel_insta') {
                if (!$this->user) {
                    $this->sendMessage($chat_id, "■  ابتدا در ربات ثبت نام کنید :", null, $message_id, $button);
                    return;
                }
                $score = $this->user->score;
                $this->user->remember_token = null;
                $this->user->save();
                if ($Data) {
                    $this->DeleteMessage($chat_id, $message_id);
                }
                $this->sendMessage($chat_id, " ⚓سکه فعلی : $score \n" . "■ ابتدا وارد پیج اینستاگرام خود شده و پست مورد نظر خود را باز کنید و گزینه Copy Link را بزنید.\n سپس دکمه 🧿ثبت پست اینستاگرام🧿 را از پایین انتخاب کنید و لینک خود را  Paste کنید.👇👇👇" . PHP_EOL . '📛 هر کاربر در هر لحظه یک نیازمندی در دیوار خواهد داشت و پیام قبلی حذف خواهد شد.' . PHP_EOL . " 👩🏻‍💻پشتیبانی: " . Helper::$admin, 'Markdown', $message_id, $insta_button);
//                $this->sendMessage($chat_id, "💥💥  قبل از اد زدن به گروهها حتما دقت کنید که *ربات در گروه مقصد باشد و خودتون در ربات ثبت نام کرده باشید* در غیر این صورت امتیاز شما ثبت نخواهد شد!💥💥 \n  💥💥اد زدن در کانال بزودی اضافه خواهد شد! 💥💥 \n $this->bot", 'Markdown', $message_id, $divar_button);


            } elseif ($text == '🧿ثبت پست اینستاگرام🧿' || strpos($this->user->remember_token, "insta$") !== false || strpos($Data, "insta$") !== false) {
                if (!$this->user) {
                    $this->sendMessage($chat_id, "■  ابتدا در ربات ثبت نام کنید :", null, $message_id, $button);
                    return;
                }
//                    $this->sendMessage($chat_id, "hi2", null, null, null);
                $cancelbutton = json_encode(['keyboard' => [
                    [['text' => "لغو ❌"]],
                ], 'resize_keyboard' => true]);

                if ($text == '🧿ثبت پست اینستاگرام🧿') {
                    $score = $this->user->score;
                    $this->user->remember_token = 'insta$link$L:0$F:0$C:0$L3:0$C3:0$CMND:0';
                    $this->user->save();
                    $this->sendMessage($chat_id, " لینک خود را وارد کنید.👇👇👇", null, $message_id, $cancelbutton);
                } elseif (strpos($this->user->remember_token, "insta$") !== false || strpos($Data, "insta$") !== false) {
                    if ($Data) {
                        $this->user->remember_token = $Data;
                        $this->user->save();

                    }
                    $params = explode('$', $this->user->remember_token);
                    if (count($params) !== 8) {
                        $this->sendMessage($chat_id, "ورودی نامعتبر است", null, null, $cancelbutton);
                        return;
                    }

                    $paramLink = $params[1];
                    $paramL = $params[2];
                    $paramF = $params[3];
                    $paramC = $params[4];
                    $paramL3 = $params[5];
                    $paramC3 = $params[6];
                    $cmnd = $params[7];

                    if ($text && $paramLink == 'link') {
                        if (!str_starts_with($text, 'https://www.instagram.com/p/') || str_contains($text, '$')) {
                            $this->sendMessage($chat_id, "📛" . PHP_EOL . 'لینک شما باید با https://www.instagram.com/p/ شروع شود', null, null, $cancelbutton);
                            return;
                        } else {
                            $link = $text;
                            $text = explode('/p/', $text); // /p/XEADw.../?utm=...
                            $postId = explode('/', $text[1])[0];
                            $link = $text[0] . '/p/' . $postId;
                            $txt = "لینک شما:" . PHP_EOL . $link . PHP_EOL . 'دکمه حمایت های مورد نظر خود را انتخاب کنید تا فعال شوند، سپس دکمه ارسال را بزنید. هر حمایت انجام شده توسط حامی شما، اعلام می شود و شما آن را متقابلا جبران میکنید. ( دایره سبز به معنی فعال بودن است ).' . PHP_EOL;
                            $txt .= ' 💖 لایک ' . ($paramL == 'L:1' ? "🟢" : "🔴") . PHP_EOL;
                            $txt .= ' 👥 فالو ' . ($paramF == 'F:1' ? "🟢" : "🔴") . PHP_EOL;
                            $txt .= ' 💬 کامنت ' . ($paramC == 'C:1' ? "🟢" : "🔴") . PHP_EOL;
                            $txt .= ' 💖💖 لایک سه پست آخر ' . ($paramL3 == 'L3:1' ? "🟢" : "🔴") . PHP_EOL;
                            $txt .= ' 💬💬 کامنت سه پست آخر ' . ($paramC3 == 'C3:1' ? "🟢" : "🔴") . PHP_EOL;
                            $txt .= "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;

                            $this->user->remember_token = "insta$$postId\$L:0\$F:0\$C:0\$L3:0\$C3:0\$CMND:0";
                            $this->user->save();
                            $paramLink = $postId;
                            $options_button = json_encode(['inline_keyboard' => [
                                [['text' => ' 💖 لایک ' . ($paramL == 'L:1' ? "🟢" : "🔴"), 'callback_data' => "insta$$paramLink$$paramL$$paramF$$paramC$$paramL3$$paramC3\$CMND:L"]],
                                [['text' => ' 👥 فالو ' . ($paramF == 'F:1' ? "🟢" : "🔴"), 'callback_data' => "insta$$paramLink$$paramL$$paramF$$paramC$$paramL3$$paramC3\$CMND:F"]],
                                [['text' => ' 💬 کامنت ' . ($paramC == 'C:1' ? "🟢" : "🔴"), 'callback_data' => "insta$$paramLink$$paramL$$paramF$$paramC$$paramL3$$paramC3\$CMND:C"]],
                                [['text' => ' 💖💖 لایک سه پست آخر ' . ($paramL3 == 'L3:1' ? "🟢" : "🔴"), 'callback_data' => "insta$$paramLink$$paramL$$paramF$$paramC$$paramL3$$paramC3\$CMND:L3"]],
                                [['text' => ' 💬💬 کامنت سه پست آخر ' . ($paramC3 == 'C3:1' ? "🟢" : "🔴"), 'callback_data' => "insta$$paramLink$$paramL$$paramF$$paramC$$paramL3$$paramC3\$CMND:C3"]],

                                [['text' => "ارسال ✅", 'callback_data' => "insta$$paramLink$$paramL$$paramF$$paramC$$paramL3$$paramC3$" . "SEND:" . $from_id]],
//                                [['text' => "لغو ثبت پست⬅", 'callback_data' => "cancel_insta"]],

                            ], 'resize_keyboard' => true]);

                            $res = $this->sendMessage($chat_id, $txt, null, null, $options_button);
                            if ($res && $res->ok == false)
                                $this->sendMessage($chat_id, $res->description, null, null, null);

                        }
                    } elseif ($cmnd === "SEND:$from_id") {

                        $user = $this->user;
                        $line = array(
                            "➖➖➖➖➖➖➖➖➖➖➖",
                            // "🕳️🕳️🕳️🕳️🕳️🕳️🕳️🕳️🕳️🕳️🕳️",
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
                        $sLine = array(
                            "➖➖➖",
                            // "🕳️🕳️🕳️",
                            "〰️〰️〰️",
                            // "🔸🔸🔸",
                            "🕶🕶🕶",
                            "🚥🚥🚥",
                            // "▪️▪️▪️",
                            "🧈🧈🧈",
                            "🛹🛹🛹",
                            "🛶🛶🛶",
                            "⌨️⌨️⌨️",
                            "💳💳💳",
                            "♾♾♾"
                        );
                        $idx = array_rand($line);
                        $line = $line[$idx];
                        $sLine = $sLine[$idx];

                        $send_button = [];
                        $c = 0;
                        if ($paramL == 'L:1') {
                            array_push($send_button, [['text' => ' 💖 لایک ', 'callback_data' => "insta$$from_id\$CMND:L"]]);
                            $c++;
                        }
                        if ($paramF == 'F:1') {
                            array_push($send_button, [['text' => ' 👥 فالو ', 'callback_data' => "insta$$from_id\$CMND:F"]]);
                            $c++;
                        }
                        if ($paramC == 'C:1') {
                            array_push($send_button, [['text' => ' 💬 کامنت ', 'callback_data' => "insta$$from_id\$CMND:C"]]);
                            $c++;
                        }
                        if ($paramL3 == 'L3:1') {
                            array_push($send_button, [['text' => ' 💖💖 لایک سه پست آخر ', 'callback_data' => "insta$$from_id\$CMND:L3"]]);
                            $c++;
                        }
                        if ($paramC3 == 'C3:1') {
                            array_push($send_button, [['text' => ' 💬💬 کامنت سه پست آخر ', 'callback_data' => "insta$$from_id\$CMND:C3"]]);
                            $c++;
                        }
                        if ($c == 0) {
                            $this->popupMessage($data_id, "📛" . PHP_EOL . 'حداقل یک حمایت را انتخاب کنید');
                            return;
                        }
                        $send_button = json_encode(['inline_keyboard' =>
                            $send_button
                            , 'resize_keyboard' => true]);


                        $txt = "#اینستاگرام" . PHP_EOL;
                        $txt .= $line . PHP_EOL;
                        $txt .= '👤Admin: ' . ($user->telegram_username != "" && $user->telegram_username != "@" ? "$user->telegram_username" :
                                "[$user->name](tg://user?id=$user->telegram_id)") . PHP_EOL;;
                        $txt .= $line . PHP_EOL;
                        $txt .= "🟢روی لینک زیر کلیک کنید" . PHP_EOL;
                        $txt .= "\xD8\x9C" . $line . PHP_EOL;
                        $txt .= PHP_EOL . 'https://www.instagram.com/p/' . $paramLink . '/' . PHP_EOL . PHP_EOL;
                        $txt .= "\xD8\x9C" . $line . PHP_EOL;
                        $txt .= "🟢حمایت های مورد نیاز کاربر را انجام داده و از دکمه های زیر آن را انتخاب کنید تا پیام جبران حمایت برای کاربر ارسال شود" . PHP_EOL;
                        $txt .= "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
                        $txt .= "🅼🅰🅶🅽🅴🆃 🅶🆁🅰🅼" . PHP_EOL;
                        $txt .= "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
                        $txt .= Helper::$bot . PHP_EOL;

                        $res = $this->sendMessage(Helper::$divarChannel, Helper::MarkDown($txt), 'MarkDown', null, $send_button);

                        if ($res && $res->ok == true) {
                            $insta = InstaNeed::where('user_id', $this->user->id)->first();
                            if ($insta) {
                                $this->DeleteMessage(Helper::$divarChannel, $insta->message_id);
                                $insta->delete();
                            }
                            InstaNeed::create([
                                'user_id' => $this->user->id,
                                'message_id' => $res->result->message_id,
                            ]);
                            $this->user->remember_token = null;
                            $this->user->save();
                            $this->DeleteMessage($chat_id, $message_id);
                            $this->sendMessage($chat_id, "✅" . PHP_EOL . 'پیام شما با موفقیت به دیوار ارسال شد. بعد از حمایت هر کاربر، پیام حمایت و پست جبران حمایت برای شما ارسال خواهد شد', null, null, $insta_button);

                        } else {
                            $this->sendMessage($chat_id, "📛" . PHP_EOL . 'مشکلی در ارسال پیش آمد. لطفا مجدد تلاش کنید یا به پشتیبانی اطلاع دهید.', null);
                            $this->sendMessage(Helper::$logs[0], "📛 insta need error!" . PHP_EOL . json_encode($res), null);

                        }
                    } elseif ($Data && $cmnd !== 'CMND:0') {

                        switch ($cmnd) {
                            case   'CMND:L':
                                $paramL = ($paramL == 'L:0' ? 'L:1' : 'L:0');
                                break;
                            case   'CMND:F':
                                $paramF = ($paramF == 'F:0' ? 'F:1' : 'F:0');
                                break;
                            case   'CMND:C':
                                $paramC = ($paramC == 'C:0' ? 'C:1' : 'C:0');
                                break;
                            case   'CMND:L3':
                                $paramL3 = ($paramL3 == 'L3:0' ? 'L3:1' : 'L3:0');
                                break;
                            case   'CMND:C3':
                                $paramC3 = ($paramC3 == 'C3:0' ? 'C3:1' : 'C3:0');
                                break;
                            default:

                        }
                        $options_button = json_encode(['inline_keyboard' => [
                            [['text' => ' 💖 لایک ' . ($paramL == 'L:1' ? "🟢" : "🔴"), 'callback_data' => "insta$$paramLink$$paramL$$paramF$$paramC$$paramL3$$paramC3\$CMND:L"]],
                            [['text' => ' 👥 فالو ' . ($paramF == 'F:1' ? "🟢" : "🔴"), 'callback_data' => "insta$$paramLink$$paramL$$paramF$$paramC$$paramL3$$paramC3\$CMND:F"]],
                            [['text' => ' 💬 کامنت ' . ($paramC == 'C:1' ? "🟢" : "🔴"), 'callback_data' => "insta$$paramLink$$paramL$$paramF$$paramC$$paramL3$$paramC3\$CMND:C"]],
                            [['text' => ' 💖💖 لایک سه پست آخر ' . ($paramL3 == 'L3:1' ? "🟢" : "🔴"), 'callback_data' => "insta$$paramLink$$paramL$$paramF$$paramC$$paramL3$$paramC3\$CMND:L3"]],
                            [['text' => ' 💬💬 کامنت سه پست آخر ' . ($paramC3 == 'C3:1' ? "🟢" : "🔴"), 'callback_data' => "insta$$paramLink$$paramL$$paramF$$paramC$$paramL3$$paramC3\$CMND:C3"]],

                            [['text' => "ارسال ✅", 'callback_data' => "insta$$paramLink$$paramL$$paramF$$paramC$$paramL3$$paramC3$" . "SEND:" . $from_id]],
//                            [['text' => "لغو ثبت پست⬅", 'callback_data' => "cancel_insta"]],

                        ], 'resize_keyboard' => true]);

                        $txt = "لینک شما:" . PHP_EOL . 'https://www.instagram.com/p/' . $paramLink . PHP_EOL . 'دکمه حمایت های مورد نظر خود را انتخاب کنید تا فعال شوند، سپس دکمه ارسال را بزنید. هر حمایت انجام شده توسط حامی شما، اعلام می شود و شما آن را متقابلا جبران میکنید. ( دایره سبز به معنی فعال بودن است ).' . PHP_EOL;
                        $txt .= ' 💖 لایک ' . ($paramL == 'L:1' ? "🟢" : "🔴") . PHP_EOL;
                        $txt .= ' 👥 فالو ' . ($paramF == 'F:1' ? "🟢" : "🔴") . PHP_EOL;
                        $txt .= ' 💬 کامنت ' . ($paramC == 'C:1' ? "🟢" : "🔴") . PHP_EOL;
                        $txt .= ' 💖💖 لایک سه پست آخر ' . ($paramL3 == 'L3:1' ? "🟢" : "🔴") . PHP_EOL;
                        $txt .= ' 💬💬 کامنت سه پست آخر ' . ($paramC3 == 'C3:1' ? "🟢" : "🔴") . PHP_EOL;
                        $txt .= "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
                        $this->EditMessageText($chat_id, $message_id, $txt, null, $options_button);
                    } else {
                        $this->sendMessage($chat_id, "ورودی نامعتبر است", null, null, $cancelbutton);
                        return;
                    }

                }
                return;
            } elseif ($text == '📈 دیوار 📈' || $text == 'دیوار📈') {
                if (!$this->user) {
                    $this->sendMessage($chat_id, "■  ابتدا در ربات ثبت نام کنید :", null, $message_id, $button);
                    return;
                }
                $score = $this->user->score;
                $this->sendMessage($chat_id, " ⚓سکه فعلی : $score \n" . "گزینه مورد نظر را انتخاب کنید.👇👇👇", 'Markdown', $message_id, $divar_button);
//                $this->sendMessage($chat_id, "💥💥  قبل از اد زدن به گروهها حتما دقت کنید که *ربات در گروه مقصد باشد و خودتون در ربات ثبت نام کرده باشید* در غیر این صورت امتیاز شما ثبت نخواهد شد!💥💥 \n  💥💥اد زدن در کانال بزودی اضافه خواهد شد! 💥💥 \n $this->bot", 'Markdown', $message_id, $divar_button);


            } elseif ($text == 'ثبت گروه💥') {
                return;
                if (!$this->user) $this->sendMessage($chat_id, "■   \n\n■ برای ثبت گروه خود ابتدا در ربات ثبت نام کنید :", null, $message_id, $button);
                else if ($this->user->score < $this->install_chat_score) {
                    $score = $this->user->score;
                    $this->sendMessage($chat_id, "🔹 برای ثبت گروه نیاز به $this->install_chat_score سکه دارید.\n💰 سکه فعلی شما: $score \n  برای دریافت سکه می توانید کانال/گروه های موجود در دیوار را فالو کرده و یا از طریق دکمه ارتباط با ما اقدام به خرید سکه نمایید ", 'Markdown', $message_id, $buy_button);

                } else {
                    $help = json_encode(['inline_keyboard' => [[['text' => 'راهنمای تبدیل گروه به حالت public', 'callback_data' => 'help_public_group']]], 'resize_keyboard' => true]);
                    $bot = str_replace("@", "", $this->bot);
                    $this->sendMessage($chat_id, "  \n🔹ابتدا از طریق لینک زیر ربات را در گروهتان اضافه کنید:\nTelegram.me/$bot?startgroup=start\n🔹سپس ربات را ادمین گروه کنید\n 🔹گروه شما باید در حالت  *public* باشد و با یک نام قابل شناسایی باشد. (مثال:$this->bot)\n  ", 'Markdown', $message_id, $help);

//                    $this->sendMessage($chat_id, "\n  *راهنمای تبدیل گروه به حالت public* \n \n 🔸وارد گروه خود شده و روی نام گروه در بالای آن کلیک کنید\n 🔸 در تلگرام موبایل از قسمت بالا *آیکن مداد* را انتخاب کنید.\n 🔸در تلگرام دسکتاپ از گزینه سه نقطه بالا گزینه  *Manage group* را انتخاب کنید \n\n 🔸 قسمت  *Group type*  را به حالت *public*  تغییر دهید.\n 🔸سپس یک نام عمومی به گروه خود تخصیص دهید. *ربات گروه شما را توسط این نام شناسایی می کند*. \n 🔼 در صورت داشتن هر گونه سوال به قسمت *درباره ربات* مراجعه نمایید. \n $this->bot ", 'Markdown', $message_id);

                    $cancel_button = json_encode(['keyboard' => [
                        [['text' => "لغو ثبت گروه❌"]],
                    ], 'resize_keyboard' => true]);
                    $this->user->step = 3; // for register channel
                    $this->user->save();
                    $this->sendMessage($chat_id, "*نام گروه خود را با @ وارد کنید* \n (مثال: vartastudio@)", 'MarkDown', $message_id, $cancel_button);

                }

            } elseif ($text == "منوی اصلی💬") {

                $this->sendMessage($chat_id, "منوی اصلی", null, $message_id, $button);

            } elseif ($text == "لغو ثبت تگ ❌") {
                if ($this->user) {
                    $this->user->step = null; // for register channel
                    $this->user->save();
                }
                $this->sendMessage($chat_id, "با موفقیت لغو شد!", null, null, $button);

            } elseif ($text == "خروج از بازار ❌") {
                if ($this->user) {
                    $this->user->step = null; // for register channel
                    $this->user->remember_token = null;
                    $this->user->save();
                }
                $this->sendMessage($chat_id, "با موفقیت خارج شدید!", null, null, $button);

            } elseif (strpos($Data, "group_details$") !== false) {
                return;
                if (!$this->user) $this->sendMessage($chat_id, "   \n\n برای ثبت  گروه خود ابتدا در ربات ثبت نام کنید :", null, $message_id, $button);
                else {
                    $return_button = json_encode(['inline_keyboard' => [
                        [['text' => "بازگشت⬅", 'callback_data' => 'مدیریت گروه ها📢']],
                    ], 'resize_keyboard' => false]);
                    $idx = (int)explode("$", $Data)[1];

                    $group = $this->user->groups[$idx];
                    $followers = Follower::where('chat_username', $group)->pluck('left');
                    $left = 0;
                    foreach ($followers as $f)
                        if ($f) $left++;
                    $this->EditMessageText($chat_id, $message_id, "گروه : " . $group . "\n\n" . " فالورهای جذب شده 👈 " . count($followers) . "\n" . " فالورهای لفت داده 👈 " . $left . "\n\n $this->bot", null, $return_button);

                }
            } elseif (strpos($Data, "channel_details$") !== false) {
                if (!$this->user) $this->sendMessage($chat_id, "■ سلام $first_name خوش آمدید\n\n■ برای ثبت کانال/گروه خود ابتدا در ربات ثبت نام کنید :", null, $message_id, $button);
                else {

                    $chatId = explode("$", $Data)[1];
                    $chat = Chat::where('chat_id', "$chatId")->first();

                    $btns = [];
                    $btns[] = [['text' => " راهنما ", 'callback_data' => 'help$auto_tab'], ['text' => " تب اتوماتیک روزانه " . ($chat->auto_tab_day ? "🟢" : "🔴"), 'callback_data' => 'settings$auto_tab_day$' . "$chatId"],];
                    $btns[] = [['text' => " راهنما ", 'callback_data' => 'help$auto_tab'], ['text' => " تب اتوماتیک شبانه" . ($chat->auto_tab ? "🟢" : "🔴"), 'callback_data' => 'settings$auto_tab$' . "$chatId"],];
                    $btns[] = [['text' => " راهنما ", 'callback_data' => 'help$auto_tag'], ['text' => " تگ اتوماتیک " . ($chat->auto_tag ? "🟢" : "🔴"), 'callback_data' => 'settings$auto_tag$' . "$chatId"],];
                    $btns[] = [['text' => " راهنما ", 'callback_data' => 'help$auto_msg_day'], ['text' => " جمله انگیزشی روزانه " . ($chat->auto_msg_day ? "🟢" : "🔴"), 'callback_data' => 'settings$auto_msg_day$' . "$chatId"],];
                    $btns[] = [['text' => " راهنما ", 'callback_data' => 'help$auto_msg_night'], ['text' => " جمله انگیزشی شبانه " . ($chat->auto_msg_night ? "🟢" : "🔴"), 'callback_data' => 'settings$auto_msg_night$' . "$chatId"],];
                    $btns[] = [['text' => " راهنما ", 'callback_data' => 'help$auto_fun'], ['text' => " پست طنز و سرگرمی " . ($chat->auto_fun ? "🟢" : "🔴"), 'callback_data' => 'settings$auto_fun$' . "$chatId"],];
                    $btns[] = [['text' => "✍️ تغییر تگ (" . Helper::$tag_score . " سکه) ✍️", 'callback_data' => 'settings$change_tag$' . "$chatId"]];
                    $btns[] = [['text' => "بازگشت⬅", 'callback_data' => 'مدیریت کانال ها📣']];

                    $setting_button = json_encode(['inline_keyboard' => $btns, 'resize_keyboard' => true]);

                    $followers = Follower::where('chat_id', "$chatId");

                    $g = Group::where('id', $chat->group_id)->first();


                    $txt = "📣 کانال:";
                    $txt .= $chat->chat_username . PHP_EOL;

                    $txt .= "موضوع:" . " $g->emoji " . "$g->name" . PHP_EOL;
                    $txt .= "تگ کانال (انتهای هر پست):" . PHP_EOL . PHP_EOL;
                    $txt .= $chat->tag ?? "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL . $chat->chat_username;

                    $this->EditMessageText($chat_id, $message_id, $txt, null, $setting_button);

                }
            } elseif (strpos($Data, 'settings$change_tag$') !== false) {
                $chatId = explode("$", $Data)[2];
                if (!$this->user || $this->user->score < (Helper::$vip_limit + Helper::$tag_score)) {
                    $return = json_encode(['inline_keyboard' => [[['text' => "بازگشت⬅", 'callback_data' => "channel_details$" . $chatId]]], 'resize_keyboard' => false]);
                    $this->popupMessage($data_id, "برای فعالسازی این قابلیت حداقل " . (Helper::$vip_limit + Helper::$tag_score) . " سکه در حساب شما وجود داشته باشد.(سکه فعلی شما: " . $this->user->score . ")" . PHP_EOL . "با دریافت بنر تبلیغاتی و فوروارد آن، می توانید سکه دریافت کنید");
                } else {
                    $this->user->step = 11;
                    $this->user->remember_token = "$chatId";
                    $this->user->save();
                    $return = json_encode(['inline_keyboard' => [[['text' => "لغو ثبت تگ ❌", 'callback_data' => "channel_details$" . $chatId]]], 'resize_keyboard' => true]);
                    $this->EditMessageText($chat_id, $message_id, "متن تگی که میخواهید زیر پست هایتان درج شود را بنویسید", null, $return);
                }

            } elseif (strpos($Data, "settings$") !== false) {
                if (!$this->user) $this->sendMessage($chat_id, "\n\n■ برای ثبت کانال/گروه خود ابتدا در ربات ثبت نام کنید :", null, $message_id, $button);
                else {

                    $cmnd = explode("$", $Data)[1];
                    $chatId = explode("$", $Data)[2];
                    $chat = Chat::where('chat_id', "$chatId")->first();

                    if ($from_id != $chat->user_telegram_id) {
                        $this->popupMessage($data_id, "📣" . PHP_EOL . "این کانال متعلق به شما نیست!");
                        return;
                    }
                    $d = Divar::where('chat_id', "$chatId")->first();
                    if ($chat->$cmnd == false) {
                        if (!$d) {
                            $this->popupMessage($data_id, "📛" . PHP_EOL . "ابتدا کانال خود را در دیوار ثبت کنید. (دکمه دیوار -> ثبت کانال در دیوار)");
                            return;
                        }

                        $validate = Helper::botIsAdminAndHasPrivileges("$d->chat_id");
                        $showMessage = $d->validated == false && $validate == true;
                        $d->validated = $validate;
                        $d->save();
                        if (!$validate) {
                            $this->popupMessage($data_id, "📛" . PHP_EOL . "ابتدا ربات را ادمین کانال خود کنید و تمام اجازه دسترسی ها به جز ادمین جدید را به آن بدهید");
                            return;
                        }

                        $count = Helper::getChatMembersCount("$chat->chat_id");
                        $d->members = $count;
                        $d->save();
                        if ($count < 20) {
                            $this->popupMessage($data_id, "📛" . PHP_EOL . "کانال شما حداقل ۲۰ عضو داشته باشد");
                            return;
                        }

                    }
                    if ($showMessage) {
                        if ($cmnd == 'auto_tab')
                            $msg = "❇️ کانال  $chat->chat_username  به لیست تبادل شبانه مگنت گرامی ها اضافه شد!" . PHP_EOL;
                        if ($cmnd == 'auto_tab_day')
                            $msg = "❇️ کانال  $chat->chat_username  به لیست تبادل روزانه مگنت گرامی ها اضافه شد!" . PHP_EOL;

                        Helper::sendMessage(Helper::$divarChannel, $msg, null, null, null);
                    }

                    $chat->$cmnd = !$chat->$cmnd;
                    $chat->save();

                    $faCmnd = ($chat->$cmnd) ? "با موفقیت فعال شد" : "با موفقیت غیر فعال شد";
                    $this->popupMessage($data_id, "📣" . PHP_EOL . $faCmnd);

                    $btns = [];
                    $btns[] = [['text' => " راهنما ", 'callback_data' => 'help$auto_tab'], ['text' => " تب اتوماتیک روزانه " . ($chat->auto_tab_day ? "🟢" : "🔴"), 'callback_data' => 'settings$auto_tab_day$' . "$chatId"],];
                    $btns[] = [['text' => " راهنما ", 'callback_data' => 'help$auto_tab'], ['text' => " تب اتوماتیک شبانه " . ($chat->auto_tab ? "🟢" : "🔴"), 'callback_data' => 'settings$auto_tab$' . "$chatId"],];
                    $btns[] = [['text' => " راهنما ", 'callback_data' => 'help$auto_tag'], ['text' => " تگ اتوماتیک " . ($chat->auto_tag ? "🟢" : "🔴"), 'callback_data' => 'settings$auto_tag$' . "$chatId"],];
                    $btns[] = [['text' => " راهنما ", 'callback_data' => 'help$auto_msg_day'], ['text' => " جمله انگیزشی روزانه " . ($chat->auto_msg_day ? "🟢" : "🔴"), 'callback_data' => 'settings$auto_msg_day$' . "$chatId"],];
                    $btns[] = [['text' => " راهنما ", 'callback_data' => 'help$auto_msg_night'], ['text' => " جمله انگیزشی شبانه " . ($chat->auto_msg_night ? "🟢" : "🔴"), 'callback_data' => 'settings$auto_msg_night$' . "$chatId"],];
                    $btns[] = [['text' => " راهنما ", 'callback_data' => 'help$auto_fun'], ['text' => " پست طنز و سرگرمی " . ($chat->auto_fun ? "🟢" : "🔴"), 'callback_data' => 'settings$auto_fun$' . "$chatId"],];
                    $btns[] = [['text' => "✍️ تغییر تگ (" . Helper::$tag_score . " سکه) ✍️", 'callback_data' => 'settings$change_tag$' . "$chatId"]];
                    $btns[] = [['text' => "بازگشت⬅", 'callback_data' => 'مدیریت کانال ها📣']];

                    $setting_button = json_encode(['inline_keyboard' => $btns, 'resize_keyboard' => true]);

                    $followers = Follower::where('chat_id', "$chatId");
                    $g = Group::where('id', $chat->group_id)->first();


                    $txt = "📣 کانال:";
                    $txt .= $chat->chat_username . PHP_EOL;

                    $txt .= "موضوع:" . " $g->emoji " . "$g->name" . PHP_EOL;
                    $txt .= "تگ کانال (انتهای هر پست):" . PHP_EOL . PHP_EOL;
                    $txt .= $chat->tag ?? "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL . $chat->chat_username;

                    $this->EditMessageText($chat_id, $message_id, $txt, null, $setting_button);
                }
            } elseif (strpos($Data, "help$") !== false) {
                $cmnd = explode("$", $Data)[1];
                switch ($cmnd) {
                    case "auto_tab"  :
                        $this->sendMessage($from_id, "📣 تب اتوماتیک" . PHP_EOL . "1⃣ توسط این قابلیت می توانید در لیست تبادل اتوماتیک کانال ها قرار بگیرید. تب روزانه ۲ تا ۳ ظهر و تب شبانه ۱۲ شب تا ۸ صبح است." . PHP_EOL . "2⃣ برای استفاده، این گزینه باید سبز باشد و ربات ادمین کانال شما باشد." . PHP_EOL . "3⃣ قبل از استفاده حتما در صفحه اصلی ربات دکمه تبادل لیستی🔃 را زده و قوانین را بخوانید", null);

                        break;
                    case "auto_tag":
                        $this->sendMessage($from_id, "📣 تگ اتوماتیک" . PHP_EOL . "1⃣ با فعال سازی این گزینه، بعد از گذاشتن پست در کانال خود یا فوروارد کردن پست از کانال های دیگر، تگ کانال شما انتهای پست گذاشته می شود و تگ کانال مبدا حذف خواهد شد!" . PHP_EOL . "2⃣ برای استفاده، این گزینه باید سبز باشد و ربات ادمین کانال شما باشد.", null);
                        break;
                    case "auto_msg_day":
                        $this->sendMessage($from_id, "📣 جمله انگیزشی روزانه" . PHP_EOL . "1⃣ با فعال سازی این گزینه، هر روز ساعت 8، یک استیکر و یک جمله انگیزشی به کانال شما ارسال می شود!" . PHP_EOL . "2⃣ برای استفاده، این گزینه باید سبز باشد و ربات ادمین کانال شما باشد.", null);
                        break;
                    case "auto_msg_night":
                        $this->sendMessage($from_id, "📣 جمله انگیزشی شبانه" . PHP_EOL . "1⃣ با فعال سازی این گزینه، هر شب ساعت 12، یک استیکر و یک جمله انگیزشی به کانال شما ارسال می شود!" . PHP_EOL . "2⃣ برای استفاده، این گزینه باید سبز باشد و ربات ادمین کانال شما باشد.", null);
                        break;
                    case "auto_fun":
                        $this->sendMessage($from_id, "📣 ارسال پست های طنز" . PHP_EOL . "1⃣ با فعال سازی این گزینه، هر چند ساعت، مطالب فان و طنز به کانال شما ارسال می شود!" . PHP_EOL . "2⃣ برای استفاده، این گزینه باید سبز باشد و ربات ادمین کانال شما باشد.", null);
                        break;
                }
            } elseif ($Data == 'مدیریت گروه ها📢' || $text == 'مدیریت گروه ها📢') {
                return;

                if (!$this->user) $this->sendMessage($chat_id, "$this->bot \n\n  ابتدا در ربات ثبت نام کنید", null, $message_id, $button);
                else {
                    $group_buttons = array();
                    foreach ($this->user->groups as $idx => $ch) {

                        if ($this->user_in_chat($ch, $this->bot_id) == 'administrator')
                            array_push($group_buttons, [['text' => $ch, 'callback_data' => "group_details$" . $idx]]);
                    }
                    $buttons = json_encode(['inline_keyboard' => $group_buttons, 'resize_keyboard' => true]);
                    $msg = count($group_buttons) > 0 ? "لیست گروه های ثبت شده شما" : "گروه ثبت شده ای ندارید";
                    if ($text) $this->sendMessage($chat_id, "$msg \n ", null, $message_id, $buttons);
                    else if ($Data) $this->EditMessageText($chat_id, $message_id, "$msg \n ", null, $buttons);

                }
            } elseif ($Data == 'مدیریت کانال ها📣' || $text == 'مدیریت کانال ها📣') {
                if (!$this->user) $this->sendMessage($chat_id, " $this->bot \n\n برای ثبت کانال خود ابتدا در ربات ثبت نام کنید ", null, $message_id, $button);
                else {
                    $channel_buttons = array();
                    //remove channels that kicked bot

//                    $this->user->channels = $tmp;
//                    $this->user->save();

                    foreach (Chat::where('user_id', $this->user->id)->where('chat_type', 'c')->get() as $ch) {
//                        if ($this->user_in_chat($ch->chat_id, $this->bot_id) == 'administrator')
                        array_push($channel_buttons, [['text' => $ch->chat_username, 'callback_data' => "channel_details$" . "$ch->chat_id"]]);
                    }
                    $buttons = json_encode(['inline_keyboard' => $channel_buttons, 'resize_keyboard' => true]);
                    $msg = count($channel_buttons) > 0 ? "لیست کانال های ثبت شده شما" : "کانال ثبت شده ای ندارید";
                    if ($text) $this->sendMessage($chat_id, " \n $msg", null, $message_id, $buttons);
                    else if ($Data) $this->EditMessageText($chat_id, $message_id, "$msg \n ", null, $buttons);

                }
            } elseif ($text == 'ثبت کانال💥') {
                if (!$this->user) $this->sendMessage($chat_id, "■  $first_name \n\n■ برای ثبت کانال خود ابتدا در ربات ثبت نام کنید :", null, $message_id, $button);
                else if ($this->user->score < $this->install_chat_score) {
                    $score = $this->user->score;
                    $this->sendMessage($chat_id, "🔹 برای ثبت کانال نیاز به $this->install_chat_score سکه دارید.\n💰 سکه فعلی شما: $score \n  برای دریافت سکه می توانید کانال/گروه های موجود در دیوار را فالو کرده و یا از طریق دکمه ارتباط با ما اقدام به خرید سکه نمایید ", 'Markdown', $message_id, $buy_button);

                } else {
                    $help = json_encode(['inline_keyboard' => [[['text' => 'راهنمای تبدیل کانال به حالت public', 'callback_data' => 'help_public_channel']], [['text' => 'راهنمای اضافه کردن ربات به کانال', 'callback_data' => 'help_add_bot_channel']],], 'resize_keyboard' => true]);
//                    $this->sendMessage($chat_id, "🔹کانال شما باید در حالت  *public* باشد و با یک نام قابل شناسایی باشد. (مثال:$this->bot)\n🔹ربات را به کانال اضافه کنید.\n    در صورت داشتن هر گونه سوال به قسمت *درباره ربات* مراجعه نمایید. \n $this->bot ", 'Markdown', $message_id, $help);

                    $cancel_button = json_encode(['keyboard' => [
                        [['text' => "لغو ❌"]],
                    ], 'resize_keyboard' => true]);
                    $this->user->step = 2; // for register channel
                    $this->user->save();
                    $this->sendMessage($chat_id, "❓راهنمای ثبت کانال" . PHP_EOL .
//                        "🚩شما یک بار کانال را ثبت می کنید وبدون ثبت مجدد در درج در دیوار و یا تبادل چرخشی استفاده خواهید کرد" . PHP_EOL .
                        "🚩در صورتی که می خواهید کاربران را تشویق به عضو شدن کنید ربات باید ادمین کانال شما باشد(اختیاری)" . PHP_EOL .
                        "🚩کانال خود را انتخاب کرده و گزینه مدیران (Administrators) را انتخاب کنید" . PHP_EOL .
                        "🚩گزینه جستجو را زده و نام ربات را سرچ کنید ( " . Helper::$bot . " ) و آن را انتخاب کنید تا به کانال اضافه شود" . PHP_EOL .
                        "🚧در صورت هر گونه راهنمایی پیام خود را ارسال کنید " . Helper::$admin
                        ,
                        'MarkDown', $message_id, $cancel_button);
                    $this->sendMessage($chat_id, "نام کانال خود را با @ وارد کنید \n مثال: " . PHP_EOL . "@vartastudio", 'MarkDown', $message_id, $cancel_button);

                }
//                $this->sendMessage($chat_id, "\nنصب ربات در کانال :\n ابتدا روی اسم کانال خود کلیک کرده تا اطلاعات آن نمایش داده شود\nدر نسخه دسکتاپ گزینه add member و در نسخه ویندوز روی  subscribers کلیک کنید.\n در این مرحله اسم ربات (magnetgrambot) را جستجو نموده و به گروه اضافه کنید\n ربات در کانال حتما باید به عنوان ادمین اضافه شود.\n سپس در کانال دستور 'نصب' را وارد کنید تا کانال شما ثبت شود🌹", null, $message_id);
            } elseif ($text == 'جریمه افراد لفت داده📛') {
                if (!$this->user) {
                    $this->sendMessage($chat_id, "■  $first_name \n\n■  ابتدا در ربات ثبت نام کنید :", null, $message_id, $button);
                    return;
                }
                $loading_sticker_id = Helper::creator('getStickerSet', [
                    "name" => "DaisyRomashka",

                ])->result->stickers[7]->file_id;
                Helper::creator('sendSticker', [
                    'chat_id' => $chat_id,
                    'sticker' => $loading_sticker_id,
                    'reply_to_message_id' => null,
                    'reply_markup' => null
                ]);

                if (in_array($this->user->telegram_id, $this->Dev)) {

                    $user_chats = Chat::get()->pluck('chat_id');

                } else
                    $user_chats = Chat::where('user_id', $this->user->id)->pluck('chat_id');
                $left = 0;
                foreach ($user_chats as $user_chat)
                    foreach (Follower::where('chat_id', $user_chat)->get() as $f) {
                        $role = $this->user_in_chat($f->chat_id, $f->telegram_id);
//                        usleep(rand(500, 1000));
                        if (isset($role) && $role != 'member' && $role != 'creator' && $role != 'administrator') {
                            $u = User::where('id', $f->user_id)->first();
                            if ($u) {
                                $u->score -= ($this->left_score * ($f->in_vip ? 2 : 1));
                                $u->save();
                                $left++;
                                $this->sendMessage($u->telegram_id, "🚨 متاسفانه به علت خروج از  " . "$f->chat_username" . " تعداد " . " $this->left_score " . " سکه جریمه شدید ", null, null);
                            }
//                            $f->left = true;
                            $f->delete();
                        }

                    }
                if ($left > 0)
                    $txt = "تعداد $left کاربر شناسایی و جریمه شدند";
                else
                    $txt = "کاربر خارج شده ای یافت نشد.";
                $this->DeleteMessage($chat_id, $message_id + 1);


                $this->sendMessage($chat_id, $txt, 'MarkDown', null);
            } elseif ($text == 'درباره ربات🤖') {
                $this->sendMessage($chat_id, " \nربات عضو گیر مگنت گرام\n توسط این ربات تبادل چرخشی اتوماتیک داشته باشید، *عضو واقعی* جذب کنید و اعضای لفت دهنده را 📛*جریمه*📛 کنید!   $this->bot " . PHP_EOL . "لینکدونی (دیوار) :" . Helper::$divarChannel . PHP_EOL . " پشتیبانی: " . Helper::$admin, 'MarkDown', $message_id);
//                $this->sendMessage($chat_id, " \n 📗این ربات گروه/کانال ثبت شده شما را در دیوار خود قرار می دهد\n📘افراد فالو کننده شما امتیاز کسب کرده و می توانند گروه/کانال خود را تبلیغ کنند\n📙 توسط این چرخه همه افراد می توانند گروه/کانال خود را تبلیغ نموده و از گروه/کانال دیگران استفاده نمایند.   $this->bot", 'MarkDown', $message_id);
                $this->sendMessage($chat_id, "$this->info", 'MarkDown', $message_id);
            } elseif ($text == "لغو ثبت نام❌") {
                $button = json_encode(['keyboard' => [
                    [['text' => "ثبت نام✅"]],
                    [['text' => 'درباره ربات🤖']],
                ], 'resize_keyboard' => true]);# user is registering

                if ($this->user) {
                    $this->user->step = null;
                    $this->user->save();
//                        $this->user->destroy();
                }
                $this->sendMessage($chat_id, "ثبت نام شما لغو شد", 'MarkDown', $message_id, $button);

            } elseif ($text == "ویرایش اطلاعات✏") {

                if (!$this->user) $this->sendMessage($chat_id, "شما  ثبت نام نکرده اید", 'MarkDown', $message_id, $button);
                else {


                    $this->sendMessage($chat_id, "■ برای مدیریت تنظیمات از کلید های زیر استفاده کنید :", null, $message_id, $edit_button);
//                    $this->user->step = 0;
//                    $this->user->save();
//                    $this->sendMessage($chat_id, "نام کاربری را وارد کنید", 'MarkDown', $message_id, $button);
                }
            } elseif (strpos($Data, 'add_channel$') !== false) {
                $channel = explode('$', $Data)[1];
                $group_id = explode('$', $Data)[2];
                $from = explode('$', $Data)[3]; //divar:tab
                if ($this->check('channel', $channel, $chat_id, $message_id, $cancel_button)) {

                    $info = $this->getChatInfo($channel);
                    $this->user->step = null;
                    $this->user->score -= $this->install_chat_score;
                    $this->user->save();
                    $timestamp = Helper::createChatImage($info->photo, "$info->id");
                    $chat = Chat::create([
                        'image' => $timestamp,
                        'user_id' => $this->user->id,
                        'group_id' => $group_id,
                        'user_telegram_id' => $this->user->telegram_id,
                        'chat_id' => "$info->id",
                        'chat_type' => 'c',
                        'chat_username' => "@" . $info->username,
                        'chat_title' => $info->title,
                        'chat_description' => $info->description,
                        'chat_main_color' => Helper::simple_color_thief(storage_path("app/public/chats/$timestamp.jpg"))
                    ]);

                    if ($from == 'divar') {
                        $prices_button = json_encode(['inline_keyboard' => [
                            [['text' => '🕐 ۶ ساعت:  ' . $this->divar_scores['6'] . 'سکه💰', 'callback_data' => "add_divar$6$" . "$info->id"]],
                            [['text' => '🕐 ۱۲ ساعت:  ' . $this->divar_scores['12'] . 'سکه💰', 'callback_data' => "add_divar$12$" . "$info->id"]],
                            [['text' => '🕐 ۲٤ ساعت: ' . $this->divar_scores['24'] . 'سکه💰', 'callback_data' => "add_divar$24$" . "$info->id"]],
                            [['text' => "بازگشت⬅", 'callback_data' => "insert_divar"]],

                        ], 'resize_keyboard' => true]);

                        $this->EditMessageText($chat_id, $message_id, "مدت زمان نمایش را انتخاب کنید:", "Markdown", $prices_button);

                    } elseif ($from == 'tab') {
                        Helper::addChatToTab($info, $first_name, $last_name);
                        Helper::sendMessage($chat_id, "🌹کانال شما با موفقیت در تبادل ثبت شد!" . PHP_EOL . "🚧پشتیبانی: " . Helper::$admin, 'MarkDown', $message_id, $button);
                    }
                }
            } elseif ($Data == "help_public_channel") {
                $txt = "\n*تبدیل کانال به حالت public: *\n 🔸وارد کانال خود شده و روی نام کانال در بالای آن کلیک کنید\n 🔸 در تلگرام موبایل از قسمت بالا *آیکن مداد* را انتخاب کنید.\n 🔸در تلگرام دسکتاپ از گزینه سه نقطه بالا گزینه  *Manage Channel* را انتخاب کنید \n\n 🔸 قسمت  *Channel type*  را به حالت *public*  تغییر دهید.\n 🔸سپس یک نام عمومی (تگ) به کانال خود تخصیص دهید. *ربات کانال شما را توسط این نام شناسایی می کند*. \n ";
                $this->sendMessage($chat_id, $txt, 'MarkDown', null);

            } elseif ($Data == "help_add_bot_channel") {
                $txt = "\n*اضافه کردن ربات در کانال :*\n🔸 ابتدا وارد کانال خود شده و روی اسم آن کلیک کرده تا اطلاعات آن نمایش داده شود\n🔸 در نسخه دسکتاپ روی گزینه سه نقطه و سپس گزینه *add members* کلیک کنید.\n🔸 در نسخه موبایل روی  *subscribers* و سپس *add subscriber* کلیک کنید . \n در این مرحله اسم ربات($this->bot) را جستجو نموده و به کانال اضافه کنید\n 🔸 *ربات در کانال حتما باید به عنوان ادمین اضافه شود* . \n 🔸سپس در کانال دستور 'نصب' را وارد کنید تا کانال شما ثبت شود🌹";
                $this->sendMessage($chat_id, $txt, 'MarkDown', null);

            } elseif ($Data == "help_public_group") {
                $txt = "\n  *راهنمای تبدیل گروه به حالت public* \n \n 🔸وارد گروه خود شده و روی نام گروه در بالای آن کلیک کنید\n 🔸 در تلگرام موبایل از قسمت بالا *آیکن مداد* را انتخاب کنید.\n 🔸در تلگرام دسکتاپ از گزینه سه نقطه بالا گزینه  *Manage group* را انتخاب کنید \n\n 🔸 قسمت  *Group type*  را به حالت *public*  تغییر دهید.\n 🔸سپس یک نام عمومی به گروه خود تخصیص دهید. *ربات گروه شما را توسط این نام شناسایی می کند*. \n 🔼 در صورت داشتن هر گونه سوال به قسمت *درباره ربات* مراجعه نمایید. \n $this->bot ";
                $this->sendMessage($chat_id, $txt, 'MarkDown', null);

            } elseif ($Data == "edit_name") {
                $name = $this->user->name;
                $this->user->step = 4;
                $this->user->save();
                $this->sendMessage($chat_id, "نام  فعلی: $name \n  نام  جدید را وارد کنید:", 'MarkDown', null, $return_button);

            } elseif ($Data == "edit_password") {

                $this->user->step = 5;
                $this->user->save();
                $this->sendMessage($chat_id, "    \n  گذرواژه جدید را وارد کنید:", 'MarkDown', null, $return_button);

            } elseif ($Data == "edit_cancel") {
                $this->user->step = null;
                $this->user->save();
                $this->sendMessage($chat_id, "■ برای مدیریت تنظیمات از کلید های زیر استفاده کنید :", null, null, $edit_button);


            } elseif ($text == "پنل مدیران🚧") {
//
                $this->sendMessage($chat_id, "🚧فقط مدیران ربات به این پنل دسترسی دارند. گزینه مورد نظر خود را انتخاب کنید:", "Markdown", null, $admin_button);
            } elseif ($Data == "send_to_users") {
                $this->user->step = 6;
                $this->user->save();
                $this->sendMessage($chat_id, "■ متن یا فایل ارسالی را وارد کنید :", null, null, $send_cancel_button);

            } elseif ($Data == "send_to_chats") {
                $this->user->step = 7;
                $this->user->save();
                $this->sendMessage($chat_id, "■ متن یا فایل ارسالی را وارد کنید :", null, null, $send_cancel_button);


            } elseif ($Data == "send_cancel") {
                $this->user->step = null;
                $this->user->save();
                $this->sendMessage($chat_id, "با موفقیت لغو شد ", null, null, null);


            } elseif ($Data == "see_users") {
                $txt = "";
                $txt .= "\n-------- لیست کاربران-----\n";
                if (in_array($from_id, $this->Dev))

                    foreach (User::get(['id', 'name', 'telegram_username', 'telegram_id', 'channels', 'groups', 'score']) as $idx => $user) {

                        $txt .= "id: $user->id\n";
                        $txt .= "name: $user->name\n";
                        $txt .= "telegram_username: $user->telegram_username\n";
                        $txt .= "telegram_id: $user->telegram_id\n";
                        $txt .= "channels:" . json_encode($user->channels) . "\n";
                        $txt .= "groups: " . json_encode($user->groups) . "\n";
                        $txt .= "score: $user->score\n";
                        $txt .= "\n-----------------------\n";
                        if ($idx % 3 == 0) {
                            $this->sendMessage($chat_id, $txt, null, null, null);
                            $txt = "";
                        }
                    }


            } elseif ($Data == "see_followers") {
                $txt = "";
                $txt .= "\n-------- لیست فالورها-----\n";
                if (in_array($from_id, $this->Dev))
                    foreach (Follower::get(['telegram_id', 'chat_id', 'chat_username']) as $chat) {
                        $txt .= "telegram_id: $chat->telegram_id\n";
                        $txt .= "chat_id: $chat->chat_id\n";
                        $txt .= "chat_username: $chat->chat_username\n";

                        $txt .= "\n-----------------------\n";
                    }
                $this->sendMessage($chat_id, $txt, null, null, null);


            } elseif (strpos($Data, "send_to_funs_ok$") !== false || ($reply && strpos($text, "fun") !== false && in_array($from_id, Helper::$logs))) {


                if ($Data) {
                    $id = explode("$", $Data)[1];
                    DB::table('funs')->where('id', $id)->delete();
                    $res = Helper::DeleteMessage($chat_id, $message_id);


                } elseif ($reply) {


                    if ($reply->text) {
                        $txt = $reply->text;
                        $txt = preg_replace("/@\w+/", "", $txt);
                        $txt .= PHP_EOL;
                        $txt .= "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL . "🅼🅰🅶🅽🅴🆃 🅶🆁🅰🅼" . PHP_EOL . Helper::$bot;
                        $reply->text = $txt;
                        $res = $this->sendFile("$chat_id", json_encode($reply), null, false);

                        if (!$res || $res->ok == false) {
                            Helper::sendMessage($from_id, $res->description, null);
                            return;
                        }
                        try {
                            $id = DB::table('funs')->insertGetId(['msg' => json_encode($res->result)]);
                            $send_or_cancel = json_encode(['inline_keyboard' => [
                                [['text' => "حذف شود✨", 'callback_data' => "send_to_funs_ok$" . $id]],

                            ], 'resize_keyboard' => true]);
                            $this->sendMessage($chat_id, "پست ذخیره شد. در صورت نیاز به اصلاح، آن را حذف کنید", null, $res->result->message_id, $send_or_cancel);
                            if ($from_id == Helper::$logs[1])
                                $res = $this->sendFile(Helper::$logs[0], json_encode($reply), null, false);
                            else
                                $res = $this->sendFile(Helper::$logs[1], json_encode($reply), null, false);
                        } catch (\Exception $e) {
                            Helper::sendMessage($from_id, $e->getMessage(), null);
                            return;
                        }


                    } elseif ($reply->caption || $reply->photo || $reply->document || $reply->video || $reply->audio || $reply->voice || $reply->video_note) {
                        $caption = $reply->caption;
                        $caption = preg_replace("/@\w+/", "", $caption);
                        $caption = $caption . PHP_EOL;
                        $caption .= "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL . "🅼🅰🅶🅽🅴🆃 🅶🆁🅰🅼" . PHP_EOL . Helper::$bot;
                        $reply->caption = $caption;

                        $res = $this->sendFile("$chat_id", json_encode($reply), null, false);
                        if (!$res || $res->ok == false) {
                            Helper::sendMessage($from_id, $res->description, null);
                            return;
                        }

                        try {
                            $id = DB::table('funs')->insertGetId(['msg' => json_encode($res->result)]);
                            $send_or_cancel = json_encode(['inline_keyboard' => [
                                [['text' => "حذف شود✨", 'callback_data' => "send_to_funs_ok$" . $id]],

                            ], 'resize_keyboard' => true]);
                            $this->sendMessage($chat_id, "پست ذخیره شد. در صورت نیاز به اصلاح، آن را حذف کنید", null, $res->result->message_id, $send_or_cancel);
                            if ($from_id == Helper::$logs[1])
                                $res = $this->sendFile(Helper::$logs[0], json_encode($reply), null, false);
                            else
                                $res = $this->sendFile(Helper::$logs[1], json_encode($reply), null, false);

                        } catch (\Exception $e) {
                            Helper::sendMessage($from_id, $e->getMessage(), null);
                            return;
                        }
                    }
                }


            } elseif ($Data == "send_to_users_ok" || ($reply && strpos($text, "send") !== false && in_array($from_id, Helper::$logs))) {
                set_time_limit(0);
//                DB::table('queue')->truncate();
                if ($Data) {
                    $group_id = explode("$", $Data)[1];
                    $message_id = $message_id - 1;
                } elseif (strpos($text, "send") !== false) {
                    $group_id = explode("d", $text)[1];
                    $message_id = $reply->message_id;
                }

                $this->user->step = null;
                $this->user->save();
                if (DB::table('queue')->where('message_id', $message_id)->count() == 0) {

                    $ids = User::where('telegram_id', '!=', null)->get('telegram_id AS id', 'app_id');
                    $ids = $ids->map(function ($item) use ($message_id, $from_id) {
                        $item['message_id'] = $message_id;
                        $item['from_id'] = $from_id;
                        return $item;
                    })->toArray();
                    DB::table('queue')->insert($ids);
                    $co = count($ids);
                    $this->sendMessage($chat_id, "■ تعداد $co پیام به صف اضافه شد!", null, null, null);

                } else {
                    $this->sendMessage($chat_id, "صف این پیام پر است", null, null, null);
                }

            } elseif ($Data == "statistics") {


                if (!in_array($from_id, $this->Dev)) return;
                $success_chats = 0;
                $success_member_count = 0;
                foreach (Chat::pluck('chat_id')->toArray() as $id) {
                    $tmp = $this->getChatMembersCount($id);


                    if ($this->user_in_chat($id, $this->bot_id) == 'administrator' && $tmp > 0) {
                        $success_chats++;
                        $success_member_count += $tmp;

                    }
                }

                $txt = "";
                $txt .= "تعداد کاربران" . PHP_EOL;
                $txt .= User::count() . PHP_EOL;
                $txt .= "-------------------" . PHP_EOL;
                $txt .= "گروه/کانال های فعال" . PHP_EOL;
                $txt .= $success_chats . PHP_EOL;
                $txt .= "-------------------" . PHP_EOL;
                $txt .= "اعضای گروه/کانال های فعال" . PHP_EOL;
                $txt .= $success_member_count . PHP_EOL;

//                $this->DeleteMessage($chat_id, $message_id);
                $this->sendMessage($chat_id, $txt, null, null, null);


            } elseif ($Data == "send_to_chats_ok") {
                $this->user->step = null;
                $this->user->save();
                $success_chats = 0;
                $success_member_count = 0;


                if (in_array($from_id, $this->Dev)) {
                    foreach (Chat::pluck('chat_id')->toArray() as $id) {
                        $tmp = $this->getChatMembersCount($id);
                        if ($this->user_in_chat($id, $this->bot_id) == 'administrator' && $tmp > 0) {
                            $success_chats++;
                            $success_member_count += $tmp;
                            $this->sendFile($id, Storage::get('message.txt'), null);
                        }
                    }
                    $this->DeleteMessage($chat_id, $message_id);
                    foreach ($this->logs as $d)
                        $this->sendMessage($d, "💹 با موفقیت به $success_chats گروه و $success_member_count عضو ارسال شد! ", null, null, null);
                }
            } elseif ($Data == "admin_help") {
                $txt = "اضافه کردن امتیاز به کاربر" . "\n";
                $txt .= "<user_id>:score:<score>" . "\n";
                $txt .= "اضافه کردن به دیوار" . "\n";
                $txt .= "<@chat_username>:divar:<hours>" . "\n";
                $txt .= "حذف از دیوار" . "\n";
                $txt .= "<@chat_username>:divar:delete" . "\n";
                $txt .= "ساخت بنر" . "\n";
                $txt .= "banner:<متن پیام>" . "\n";
                $txt .= "ساخت متن با کلید شیشه ای" . "\n";
                $txt .= "inline:<متن پیام>\nمتن1\nلینک1\n ..." . "\n";
                $txt .= 'C:command:chat_username' . "\n" . 'commands=' . "\n" . "distag" . "\n" . "distab" . "\n" . "block" . "\n" . "unblock" . "\n" . "delete" . "\n" . "alarm" . "\n";
                $txt .= "تبلیغ انتهای پیام ارسالی" . "\n";
                $txt .= "banner=name=link" . "\n";
                $this->sendMessage($chat_id, $txt, null, null, null);

            } elseif ((strpos($text, ":score:") !== false)) {


                $id = explode(":", $text)[0];
                $score = explode(":", $text)[2];
                if (in_array($from_id, $this->Dev)) {
                    $u = User::where('id', $id)->orWhere('telegram_username', $id)->first();
                    if ($u) {
                        $u->score += $score;
                        $u->save();
                        $this->sendMessage($u->telegram_id, "🙌 تبریک! \n $score  سکه به شما اضافه شد!  \n  سکه فعلی : $u->score", null, null, null);
                        $this->sendMessage($chat_id, "$score  سکه به $u->telegram_username  اضافه شد.", null, null, null);
                    }
                }

            } elseif ((strpos($text, "banner:") !== false)) {
                if (!in_array($from_id, $this->Dev)) return;
                $txt = " سلام   \n *مگنت گرام* هستم . با من میتونی برای گروه یا کانال خودت *فالور جذب کنی*. \n *من یه ربات شبیه دیوارم که گروه/کانال تو رو تبلیغ میکنم و بقیه از فالو کردن اون امتیاز میگیرند و میتونن کانال/گروه خودشونو تبلیغ کنن*  \n آموزش ربات\n  $this->tut_link  $this->bot ";
                $buttons = [[['text' => '👈 دانلود اپلیکیشن 👉', 'url' => Helper::$app_link]]];
                $tmp = explode(":", $text);
                if (count($tmp) >= 2 && $tmp[1] != '')
                    $txt = $tmp[1];

                $this->sendMessage($chat_id, $txt, "Markdown", null, json_encode(['inline_keyboard' => $buttons, 'resize_keyboard' => true]));


            } elseif ((strpos($text, "C:") !== false || strpos($text, "c:") !== false)) {
                if (!in_array($from_id, $this->Dev)) return;

                $inputs = explode(":", $text);

                $command = $inputs[1];
                if (count($inputs) > 2)
                    $what = $inputs[2];

                switch ($command) {
                    case "block":
                        $d = Divar::where('chat_username', $what)->first();
                        $c = Chat::where('chat_username', $what)->first();
                        if ($d) {
                            $d->blocked = true;
                            $d->save();
                            if ($c) {
                                $c->active = false;
                                $c->save();
                            }
                            $this->sendMessage($from_id, $what . "🟢 blocked successfully !", null, null, null);
                            Helper::DeleteMessage(Helper::$divarChannel, $d->message_id);
                        } else {
                            $this->sendMessage($from_id, $what . "🔴 not found !", null, null, null);

                        }
                        break;
                    case "unblock":
                        $d = Divar::where('chat_username', $what)->first();
                        $c = Chat::where('chat_username', $what)->first();
                        if ($d) {
                            $d->blocked = false;
                            $d->save();
                            if ($c) {
                                $c->active = true;
                                $c->save();
                            }
                            $this->sendMessage($from_id, $what . "🟢 ublocked successfully !", null, null, null);

                        } else {
                            $this->sendMessage($from_id, $what . "🔴 not found !", null, null, null);

                        }
                        break;
                    case "delete":
                        $d = Divar::where('chat_username', $what)->delete();
                        $c = Chat::where('chat_username', $what)->delete();
                        if ($d || $c) {

                            $this->sendMessage($from_id, $what . "🟢 deleted successfully !", null, null, null);

                        } else {
                            $this->sendMessage($from_id, $what . "🔴 not found !", null, null, null);

                        }
                        break;
                    case "distab":

                        $c = Chat::where('chat_username', $what)->first();
                        if ($c) {
                            $c->auto_tab = false;
                            $c->auto_tab_day = false;
                            $c->save();
                            $this->sendMessage($from_id, $what . "🟢 disabled successfully !", null, null, null);

                        } else {
                            $this->sendMessage($from_id, $what . "🔴 not found !", null, null, null);

                        }
                        break;
                    case "distag":

                        $c = Chat::where('chat_username', $what)->first();
                        if ($c) {
                            $c->auto_tag = false;
                            $c->save();
                            $this->sendMessage($from_id, $what . "🟢 disabled successfully !", null, null, null);

                        } else {
                            $this->sendMessage($from_id, $what . "🔴 not found !", null, null, null);

                        }
                        break;
                    case "alarm":
                        \Illuminate\Support\Facades\Artisan::call('tab:alarm');
                        break;
                    case "alarmday":
                        \Illuminate\Support\Facades\Artisan::call('tab:alarmday');
                        break;
                    case "validate":
                        \Illuminate\Support\Facades\Artisan::call('tab:validate');
                        break;
                    case "alarmprods":
                        \Illuminate\Support\Facades\Artisan::call('send:productsdaily');
                        break;
                    case "fun":
                        \Illuminate\Support\Facades\Artisan::call('send:messagesfun');
                        break;
                }

            } elseif ((strpos($text, "inline:") !== false)) {
                if (!in_array($from_id, $this->Dev)) return;
                $buttons = [];
                $inlines = explode("\n", $text);
                $txt = explode(":", array_shift($inlines))[1]; //remove first (inline)
                $len = count($inlines);
                foreach ($inlines as $idx => $item) {

                    if ($idx % 2 == 0 && $idx + 1 < $len)
                        array_push($buttons, [['text' => $inlines[$idx], 'url' => $inlines[$idx + 1]]]);

                }


                $this->sendMessage($chat_id, $txt, null, null, json_encode(['inline_keyboard' => $buttons, 'resize_keyboard' => true]));


            } elseif ((strpos($text, ":divar:") !== false)) {
                if (!in_array($from_id, $this->Dev)) return;

                $chat_id = explode(":", $text)[0];
                $hours = explode(":", $text)[2];
                $info = $this->getChatInfo($chat_id)->result;
                if ($hours == "delete") {
                    Divar::where('chat_id', "$info_id")->delete();
                    Queue::where('chat_id', "$info_id")->delete();
                    $this->sendMessage($from_id, "با موفقیت حذف شد!", null, null, null);
                    return;
                }
                if (!$info) {
                    $this->sendMessage($from_id, "کانال/گروه وجود ندارد", null, null, null);
                    return;
                }
                $info_id = "$info->id";
                $divar_ids = Divar::pluck('chat_id')->toArray();
                $queue_ids = Queue::pluck('chat_id')->toArray();

                if (in_array($info_id, $divar_ids) || in_array($info_id, $queue_ids)) {

                    $this->sendMessage($from_id, "این گروه/کانال از قبل در دیوار وجود دارد!", null, null, null);
                    return;
                }
                $u = User::where('telegram_id', $from_id)->first();


                Divar::create(['user_id' => $u->id, 'chat_id' => "$info_id", 'chat_type' => $info->type, 'chat_username' => '@' . $info->username,
                    'chat_title' => $info->title, 'chat_description' => $info->description, 'expire_time' => Carbon::now()->addHours($hours), 'start_time' => Carbon::now()]);

                $this->sendMessage($from_id, "*گروه/کانال  با موفقیت به دیوار اضافه شد!*", "Markdown", null, null);
                Helper::createChatImage($info->photo, "$info_id");

            } elseif ($text == "ثبت نام✅") {
                return;
                if ($this->user) $this->sendMessage($chat_id, "شما قبلا ثبت نام کرده اید", 'MarkDown', $message_id, $button);
//                else if ($username == "@" || $username == "") $this->sendMessage($chat_id, "لطفا قبل از ثبت نام, از منوی تنظیمات تلگرام خود, یک نام کاربری به اکانت خود تخصیص دهید!", 'MarkDown', $message_id, $button);
                else {
                    $name = "";
                    if ($first_name != "") {
                        if (mb_strlen($first_name) > 50)
                            $name = mb_substr($first_name, 0, 49);
                    } else if ($last_name != "") {
                        if (mb_strlen($last_name) > 50)
                            $name = mb_substr($last_name, 0, 49);
                    } else if ($username != "" && $username != "@") {
                        if (mb_strlen($username) > 50)
                            $name = mb_substr($username, 1, 49);
                    } else
                        $name = "$from_id";

                    $this->user = User::create(['telegram_id' => $from_id, 'telegram_username' => $username, 'score' => $this->init_score, 'step' => 0, 'name' => $name]);

                    $this->sendMessage($chat_id, "نام خود را وارد کنید \n(حداقل 5 حرف)", 'MarkDown', $message_id, $cancel_button);
                }
            } elseif ($text == "🎴 ساخت دکمه شیشه ای 🎴") {
                if (!$this->user) $this->sendMessage($chat_id, "■  $first_name \n\n■  ابتدا در ربات ثبت نام کنید :", null, $message_id, $button);

                else {
                    $cancel_button = json_encode(['keyboard' => [
                        [['text' => "لغو ❌"]],
                    ], 'resize_keyboard' => true]);
                    $this->user->step = 8;

                    $this->user->save();

                    $this->sendMessage($chat_id, "متن یا فایل خود را وارد کنید", 'MarkDown', $message_id, $cancel_button);
                }
            } elseif (!$Data && $this->user && $this->user->step !== null && $this->user->step >= 0) {
                # user is registering

                switch ($this->user->step) {
                    case  0:
                        if ($this->check('name', $text, $chat_id, $message_id, $cancel_button)) {
                            $this->user->step++;
                            $this->user->name = $text;
                            $this->user->save();
                            $this->sendMessage($chat_id, "رمز عبور را وارد کنید\n(حداقل 5 حرف)", 'MarkDown', $message_id);

                        }
                        break;
                    case  1:
                        if ($this->check('password', $text, $chat_id, $message_id, $cancel_button)) {

                            $this->user->password = Hash::make($text);
                            $this->user->step = null;
                            $this->user->save();
                            $this->createUserImage($this->user->telegram_id);
                            $this->sendMessage($chat_id, "با موفقیت ثبت نام شدید!\n اکنون با دکمه ثبت گروه / کانال میتوانید گروه یا کانال خود را ثبت نمایید", 'MarkDown', $message_id, $button);
                        }
                        break;
//                        case 2 is ثبت کانال
                    case  2:
                        $cancel_button = json_encode(['keyboard' => [
                            [['text' => "لغو ❌"]],
                        ], 'resize_keyboard' => true]);

                        if ($this->check('channel', $text, $chat_id, $message_id, $cancel_button)) {

//                            $tmp = $this->user->channels;
//                            array_push($tmp, $text);
//                            $this->user->channels = $tmp;
//                            $this->user->step = null;


                            $group_id_button = [];
                            foreach (Group::where('id', '<', 20)->get() as $g) {
                                $group_id_button[] = [['text' => "$g->name $g->emoji", 'callback_data' => "add_channel$$text$$g->id$" . 'divar']];
                            }
                            $group_id_button = json_encode(['inline_keyboard' => $group_id_button, 'resize_keyboard' => true]);
                            $this->sendMessage($chat_id, "موضوع کانال خود را انتخاب کنید", 'MarkDown', $message_id, $group_id_button);


                        }
                        break;
                    case  3:

                        $cancel_button = json_encode(['keyboard' => [
                            [['text' => "لغو ثبت گروه❌"]],
                        ], 'resize_keyboard' => true]);

                        if ($this->check('group', $text, $chat_id, $message_id, $cancel_button)) {

                            $tmp = $this->user->groups;
                            array_push($tmp, $text);
                            $this->user->groups = $tmp;
                            $this->user->step = null;
                            $this->user->score -= $this->install_chat_score;
                            $this->user->save();


                            $info = $this->getChatInfo($text);
                            if ($info == null) {
                                $this->sendMessage($from_id, "■ گروه با موفقیت ثبت شد.\n🔹 اکنون وارد گروه شده و دستور 'نصب' را وارد کنید\n🔹سپس می توانید گروه را در ربات تبلیغ نمایید!\n\n🔹 در صورت ادمین نبودن ربات در گروه, گروه نمایش داده نمی شود . \n🌹  ", 'Markdown', $message_id, $button);

                            }
                            $timestamp = Helper::createChatImage($info->photo, "$info->id");

                            Chat::create([
                                'image' => $timestamp,
                                'user_id' => $this->user->id,
                                'user_telegram_id' => $this->user->telegram_id,
                                'chat_id' => "$info->id",
                                'chat_type' => 'channel',
                                'chat_username' => "@" . $info->username,
                                'chat_title' => $info->title,
                                'chat_description' => $info->description,
                                'chat_main_color' => Helper::simple_color_thief(storage_path("app/public/chats/$timestamp.jpg"))

                            ]);
                            $this->sendMessage($chat_id, "■ گروه با موفقیت ثبت شد.\n🔹 اکنون وارد گروه شده و دستور 'نصب' را وارد کنید\n🔹سپس می توانید گروه را در ربات تبلیغ نمایید!\n\n🔹 در صورت ادمین نبودن ربات در گروه, گروه نمایش داده نمی شود . \n🌹  ", 'Markdown', $message_id, $button);

//                            $this->sendMessage($chat_id, "\nاضافه کردن ربات در کانال :\n ابتدا وارد کانال خود شده و روی اسم آن کلیک کرده تا اطلاعات آن نمایش داده شود\nدر نسخه موبایل گزینه add member و در نسخه ویندوز روی  subscribers کلیک کنید . \n در این مرحله اسم ربات($this->bot) را جستجو نموده و به گروه اضافه کنید\n *ربات در کانال حتما باید به عنوان ادمین اضافه شود* . \n سپس در کانال دستور 'نصب' را وارد کنید تا کانال شما ثبت شود🌹", 'Markdown', $message_id, $button);

                        }
                        break;
                    case  4:
                        if ($this->check('name', $text, $chat_id, $message_id, $return_button)) {
                            $this->user->step = null;
                            $this->user->name = $text;
                            $this->user->save();
                            $this->sendMessage($chat_id, "با موفقیت ویرایش شد!", 'MarkDown', $message_id, $edit_button);

                        }
                        break;
                    case  5:
                        if ($this->check('password', $text, $chat_id, $message_id, $return_button)) {

                            $this->user->password = Hash::make($text);
                            $this->user->step = null;
                            $this->user->save();
                            $this->sendMessage($chat_id, "با موفقیت ویرایش شد!", 'MarkDown', $message_id, $edit_button);

                        }
                        break;
                    //send to users
                    case  6:
//                        if (!in_array($from_id, $this->Dev))
//                    return;
                        $send_or_cancel = json_encode(['inline_keyboard' => [
                            [['text' => "ارسال شود✨", 'callback_data' => "send_to_users_ok"]],
                            [['text' => "لغو ارسال⬅", 'callback_data' => "send_cancel"]],
                        ], 'resize_keyboard' => true]);
                        $this->user->step = null;
                        $this->user->save();
                        Storage::put('message.txt', json_encode($message));
                        $this->sendMessage($chat_id, "*از ارسال به کاربران اطمینان دارید؟*", 'MarkDown', $message_id, $send_or_cancel);

                        break;
                    //send to groups
                    case  7:
                        $send_or_cancel = json_encode(['inline_keyboard' => [
                            [['text' => "ارسال شود✨", 'callback_data' => "send_to_chats_ok"]],
                            [['text' => "لغو ارسال⬅", 'callback_data' => "send_cancel"]],
                        ], 'resize_keyboard' => true]);
                        $this->user->step = null;
                        $this->user->save();
                        Storage::put('message.txt', json_encode($message));
                        $this->sendMessage($chat_id, "*از ارسال به گروه ها اطمینان دارید؟*", 'MarkDown', $message_id, $send_or_cancel);

                        break;
                    //get banner button link
                    case  8:
                        $cancel_button = json_encode(['keyboard' => [
                            [['text' => "لغو ❌"]],
                        ], 'resize_keyboard' => true]);
                        if ($text && strlen($text) > 1000) {
                            $this->sendMessage($chat_id, "*طول پیام از 1000 حرف کمتر باشد*", 'MarkDown', $message_id, $cancel_button);
                            return;
                        }
                        $this->user->step = 9;
                        $this->user->save();
                        Storage::put("$from_id.txt", json_encode($message));
                        $this->sendMessage($chat_id, "لینک دکمه را وارد کنید (باید با  //:https شروع شود)", 'MarkDown', $message_id, $cancel_button);

                        break;
                    //get banner button name
                    case  9:
                        $cancel_button = json_encode(['keyboard' => [
                            [['text' => "لغو ❌"]],
                        ], 'resize_keyboard' => true]);
                        if ($text && (strlen($text) > 50 || strpos($text, "https://"))) {
                            $this->sendMessage($chat_id, "*طول لینک از 50 حرف کمتر باشد و با  //:https شروع شود*", 'MarkDown', $message_id, $cancel_button);
                            return;
                        }
                        $this->user->step = 10;
                        $this->user->save();
                        $txt = Storage::get("$from_id.txt");
                        Storage::put("$from_id.txt", json_encode(['message' => $txt, 'link' => $text]));
                        $this->sendMessage($chat_id, "متن دکمه را وارد کنید", 'MarkDown', $message_id, $cancel_button);

                        break;
                    //send banner
                    case  10:
                        $cancel_button = json_encode(['keyboard' => [
                            [['text' => "لغو ❌"]],
                        ], 'resize_keyboard' => true]);
                        if ($text && strlen($text) > 50) {
                            $this->sendMessage($chat_id, "*متن دکمه از 50 حرف کمتر باشد*", 'MarkDown', $message_id, $cancel_button);
                            return;
                        }
                        $this->user->step = null;
                        $this->user->save();
                        $txt = json_decode(Storage::get("$from_id.txt"));
                        Storage::put("$from_id.txt", json_encode(['message' => $txt->message, 'link' => $txt->link, 'name' => $text,]));
                        $this->sendBanner($from_id, Storage::get("$from_id.txt"));
                        $this->sendMessage($chat_id, "با موفقیت تولید شد!", 'MarkDown', $message_id, $button);
                        break;

                    //change tag
                    case  11:
                        $return = json_encode(['inline_keyboard' => [[['text' => "لغو ثبت تگ ❌", 'callback_data' => "channel_details$" . $this->user->remember_token]]], 'resize_keyboard' => true]);


                        if ($text && strlen($text) > 200) {
                            $this->sendMessage($chat_id, " متن تگ از 200 حرف کمتر باشد " . PHP_EOL . "تعداد فعلی:" . strlen($text), null, null, $return);

                            $this->DeleteMessage($chat_id, $message_id);
                            return;
                        }
                        if (!$this->user || $this->user->remember_token == null) {
                            $this->sendMessage($chat_id, " کانال یافت نشد.لطفا مجدد اقدام کنید ", null, null, $return);
                            $this->DeleteMessage($chat_id, $message_id);
                            return;
                        }
//
//
                        Chat::where('chat_id', $this->user->remember_token)->update(['tag' => $text]);
                        $details = json_encode(['inline_keyboard' => [[['text' => "بازگشت⬅", 'callback_data' => "channel_details$" . $this->user->remember_token]]], 'resize_keyboard' => false]);
                        $this->user->remember_token = null;
                        $this->user->step = null;
                        $this->user->score = $this->user->score - Helper::$tag_score;
                        $this->user->save();
                        $this->DeleteMessage($chat_id, $message_id);
                        $this->DeleteMessage($chat_id, $message_id - 1);
                        $this->DeleteMessage($chat_id, $message_id - 2);

                        $this->sendMessage($from_id, "✅تگ با موفقیت تغییر یافت", null, null, $details);
                        break;

                    case 12:

//                        $regex = "@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?)?)@";
                        $regex = '/\b(https?):\/\/[-A-Z0-9+&@#\/%?=~_|$!:,.;]*[A-Z0-9+&@#\/%=~_|$]/i';
//                        $text = preg_replace($regex, ' ', $text);

                        if ($text && mb_strlen($text) > 250) {
                            $this->sendMessage($from_id, "⛔ متن از 250 حرف کمتر باشد " . PHP_EOL . "تعداد حرف فعلی: " . mb_strlen($text), null, $message_id, $cancel_button);
                            return;
                        }
                        if (!$this->user || $this->user->remember_token == null) {
                            $this->popupMessage($data_id, "⛔ لطفا مجدد اقدام کنید ");
                            return;
                        }

                        $input = explode('$', $this->user->remember_token);
                        $groupId = count($input) > 1 ? $input[1] : null;
                        $time = count($input) > 2 ? $input[2] : null;

                        if (!$groupId || !$time) {
                            $this->popupMessage($data_id, "⛔ موضوع یا زمان نامعتبر است. لطفا به ادمین گزارش دهید ");
                            return;
                        }

                        $res = Helper::addNeedToDivar($groupId, $time, $this->user, $text);

                        if ($res) {

                            $this->user->score = $this->user->score - Helper::$add_needing_score;

                            $this->sendMessage($from_id, "✅ تبلیغ شما با موفقیت به دیوار اضافه شد!", null, null, $divar_button);
                        }
                        $this->user->remember_token = null;
                        $this->user->step = null;
                        $this->user->save();
                        break;

                    //shop section
                    case 13:

                }

            } elseif ($text == '🛒 بازار 🛒' || $text == '❌لغو ساخت فروشگاه' || $Data == 'bazar$main' || strpos($Data, 'bazar$delete$') !== false) {
//
                $bazar_button = [];

                $bazar_button[] = [['text' => 'ورود⬅', 'url' => 'https://t.me/vartashopbot']];

                $bazar_button = json_encode(['inline_keyboard' => $bazar_button
                    , 'resize_keyboard' => true]);
                Helper::sendMessage($chat_id, "🛍برای ساخت فروشگاه و یا ورود به بازارچه به ربات زیر مراجعه کنید🛍" . PHP_EOL . '@vartashopbot'
                    , null, null, $bazar_button);

                return;


            }


        } elseif ($tc == 'channel') {


            //search button in channel
//            if ($text == "🔍 جست و جو 🔎") {

            if ($Data && strpos($Data, 'insta$') !== false) {
                $this->user = User::where('telegram_id', "$from_id")->first();
                $params = explode('$', $Data);
                if (count($params) < 3) return;
                $owner_id = $params[1];
                $cmnd = $params[2];
                if ($from_id == $owner_id) {
                    $this->popupMessage($data_id, '⛔' . PHP_EOL . 'شما مالک این پیام هستید!');
                    return;
                }
                $owner = User::where('telegram_id', $owner_id)->first();
                if (!$owner) {
                    $this->popupMessage($data_id, '⛔' . PHP_EOL . 'کاربر این پست یافت نشد!');
                    return;
                }
                if (!$this->user) {
                    $this->popupMessage($data_id, '⛔' . PHP_EOL . 'شما در ربات ثبت نام نکرده اید!' . PHP_EOL . "برای ثبت نام، یک بار ربات را استارت بزنید.(لینک ربات آخر همین پست قرار دارد)");
                    return;
                }
                $insta = InstaNeed::where('user_id', $this->user->id)->first();
                if (!$insta) {
                    $this->popupMessage($data_id, '⛔' . PHP_EOL . 'ابتدا نیازمندی خود را (برای جبران متقابل) از طریق دکمه اینستاگرام در ربات ثبت کنید.(لینک ربات آخر همین پست قرار دارد)');
                    return;
                }
                $user = $this->user;
                $admin = ($user->telegram_username != "" && $user->telegram_username != "@" ? "$user->telegram_username" :
                    "[👤$user->name](tg://user?id=$user->telegram_id)");

                switch ($cmnd) {
                    case 'CMND:L':
                        $txt = "✅ کاربر $admin پست شما را لایک کرده است!";
                        break;
                    case 'CMND:F':
                        $txt = "✅ کاربر $admin شما را فالو کرده است.";
                        break;
                    case 'CMND:C':
                        $txt = "✅ کاربر $admin برای پست شما کامنت گذاشته است.";
                        break;
                    case 'CMND:L3':
                        $txt = "✅ کاربر $admin سه پست شما را لایک کرده است.";
                        break;
                    case 'CMND:C3':
                        $txt = "✅ کاربر $admin برای سه پست شما کامنت گذاشته است.";
                        break;

                    default:
                        return;
                }
                if ($txt) {
                    $txt .= PHP_EOL . "🔎 پست جبران حمایت(در صورتی که قبلا جبران نکرده اید):" . PHP_EOL . "https://t.me/magnetgramwall/" . $insta->message_id;
                    $this->sendMessage($owner_id, Helper::MarkDown($txt), 'Markdown');
                    $this->sendMessage(Helper::$logs[0], Helper::MarkDown($txt), 'Markdown');
                    $this->popupMessage($data_id, '✅' . PHP_EOL . 'پیام حمایت شما به کاربر ارسال شد!' . PHP_EOL . '');

                }
                return;
            }


            if ($text == "🔍" || $text == "🔎") {


                $line = array(

                    "➖➖➖➖➖➖➖➖➖➖➖",
                    "🕳️🕳️🕳️🕳️🕳️🕳️🕳️🕳️🕳️🕳️🕳️",
                    "〰️〰️〰️〰️〰️〰️〰️〰️〰️〰️〰️",
                    "🔸🔸🔸🔸🔸🔸🔸🔸🔸🔸",
                    "🕶🕶🕶🕶🕶🕶🕶🕶🕶🕶",
                    "🚥🚥🚥🚥🚥🚥🚥🚥🚥🚥",
                    "▪️▪️▪️▪️▪️▪️▪️▪️▪️▪️",
                );

                $idx = array_rand($line);
                $line = $line[$idx];


                $this->DeleteMessage($chat_id, $message_id);
                $group_search = "🔍 روی موضوع مورد نظر خود برای جست و جو کلیک کنید" . PHP_EOL . PHP_EOL;
                foreach (Group::get() as $g) {
                    $group_search .= "#$g->name $g->emoji" . "  ";
                }
                $res = Helper::sendMessage($chat_id, $group_search . PHP_EOL . "🅼🅰🅶🅽🅴🆃 🅶🆁🅰🅼" . PHP_EOL . Helper::$divarChannel, null, null, null, true);

//                sleep(5);
//
//                if ($res && $res->ok == true)
//                    $this->DeleteMessage($chat_id, $res->result->message_id);
                return;
            }

            //lock tab channel between 0 AM and 8 AM (night tab)
            //lock tab channel between 2 PM and 3 PM (night tab)


            date_default_timezone_set('Asia/Tehran');

            $now = Carbon::now()->format('H:i:s');
            $start = '00:10:00';
            $end = '07:50:00';
            if ($now >= $start && $now <= $end && Tab::where('chat_id', "$chat_id")->exists()) {
                $this->DeleteMessage($chat_id, $message_id);
                return;
            }
            //
            $start = '14:05:00';
            $end = '15:55:00';
            if ($now >= $start && $now <= $end && Tab::where('chat_id', "$chat_id")->exists()) {
                $this->DeleteMessage($chat_id, $message_id);
                return;
            }


            //remove tag for active auto_tag channels

            if (!$Data && $ch = Chat::where('chat_id', "$chat_id")->where('auto_tag', true)->where('active', true)->first()) {

                if (($text && (strpos($text, Helper::$bot) !== false || strpos($text, $ch->chat_username) !== false)) ||
                    ($caption && (strpos($caption, Helper::$bot) !== false || strpos($caption, $ch->chat_username) !== false)))
                    return;

//                Helper::sendMessage(Helper::$logs[0], $update, null);
                $this->user = User::where('id', "$ch->user_id")->first();
                if (!$this->user || $this->user->score < Helper::$vip_limit)
                    $ch->tag = null;

                if ($text) {
                    $text = preg_replace("/@\w+/", "", $text);

//                    $text .= PHP_EOL . "〰️〰️〰️〰️" . "\xD8\x9C" . "〰️〰️〰️〰️";

                    $text = $text . PHP_EOL;


                    $text .= ($ch->tag) ?? "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL . $ch->chat_username;

                    $update->channel_post->text = $text;
//                    Helper::sendMessage(Helper::$logs[0], json_encode($update), null);

                    $res = Helper::DeleteMessage($chat_id, $message_id);
                    if ($res->ok != true) {
                        Helper::sendMessage(Helper::$logs[0], $ch->chat_username . PHP_EOL . $res->result->description . PHP_EOL . json_encode($update), null);
//                        $ch->auto_tag = false;
                        $ch->save();
                        return;
                    }
                    $this->sendFile("$chat_id", json_encode($update->channel_post), $reply, false);


//                    if ($from_message_id) {
//                        Helper::sendMessage($chat_id, $text, null);
//
//                    } else {
//
//                        $this->EditMessageText($chat_id, $message_id, $text);
//                    }
//                $this->copyMessage($chat_id,);

                } elseif ($caption || $photo || $document || $video || $audio || $voice || $video_note || $this->user->remember_token) {

//                    if ($ch->chat_username == '@magnetgramwall') {
//                    if ($ch->chat_username == '@magnetgramwall')
//                        Helper::sendMessage(Helper::$logs[0], json_encode($update->channel_post), null);
                    if ($update->channel_post->media_group_id) {
                        Helper::DeleteMessage($chat_id, $message_id);
                        $isNew = false;
                        if ($this->user->remember_token) {
                            $remember = json_decode($this->user->remember_token);
                            if ($remember && $remember->mdgid) {
                                if ($remember->mdgid == $update->channel_post->media_group_id) {

                                    if ($remember->msgid)
                                        Helper::DeleteMessage($chat_id, $remember->msgid);
                                    if ($remember->mdmsgid)
                                        Helper::DeleteMessage($chat_id, $remember->mdmsgid);
                                    $type = 'document';
                                    $file_id = '';
                                    if ($photo) {
                                        $type = 'photo';

                                        $file_id = is_array($photo) ? $photo[count($photo) - 1]->file_id : $photo->file_id;
                                    } elseif ($video) {
                                        $type = 'video';
                                        $file_id = is_array($video) ? $video[count($video) - 1]->file_id : $video->file_id;


                                    } elseif ($audio) {
                                        $type = 'audio';
                                        $file_id = is_array($audio) ? $audio[count($audio) - 1]->file_id : $audio->file_id;
                                    } elseif ($document) {
                                        $type = 'document';
                                        $file_id = is_array($document) ? $document[count($document) - 1]->file_id : $document->file_id;

                                    }


                                    $media = [];
                                    foreach ($remember->files as $file) {
                                        $media[] = $file;
                                    }
                                    $media[] = ['parse_mode' => 'Markdown', 'type' => $type, 'media' => $file_id];


                                    $res = Helper::sendMediaGroup($chat_id, $media);
//                                        Helper::sendMessage(Helper::$logs[0], json_encode($res), null);
                                    if ($res && $res->ok == true) {
                                        $mediaMsgId = $res->result[0]->message_id;
//                                        Helper::sendMessage(Helper::$logs[0], " $remember->msgid < $mediaMsgId ", null);

                                        while ($remember->msgid < $mediaMsgId) {

                                            Helper::DeleteMessage($chat_id, $remember->msgid);
                                            $remember->msgid++;
                                        }
                                    }

                                    $this->user->remember_token = json_encode(['mdgid' => $remember->mdgid, 'msgid' => $update->channel_post->message_id, 'mdmsgid' => $mediaMsgId,
                                        'files' => $media]);
                                    $this->user->save();

                                } else {
                                    $isNew = true;
                                    $this->user->remember_token = null;
                                }
                            } else {
                                $this->user->remember_token = null;
                            }
                        }
                        if ($this->user->remember_token == null || $isNew == true) {

                            $caption = preg_replace("/@\w+/", "", $caption);
                            $caption = $caption . PHP_EOL;
                            $caption .= $ch->tag ?? "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL . $ch->chat_username;

                            $type = 'document';
                            $file_id = '';
                            if ($photo) {
                                $type = 'photo';

                                $file_id = is_array($photo) ? $photo[count($photo) - 1]->file_id : $photo->file_id;
                            } elseif ($video) {
                                $type = 'video';
                                $file_id = is_array($video) ? $video[count($video) - 1]->file_id : $video->file_id;


                            } elseif ($audio) {
                                $type = 'audio';
                                $file_id = is_array($audio) ? $audio[count($audio) - 1]->file_id : $audio->file_id;
                            } elseif ($document) {
                                $type = 'document';
                                $file_id = is_array($document) ? $document[count($document) - 1]->file_id : $document->file_id;

                            }

                            $this->user->remember_token = json_encode(['mdgid' => $update->channel_post->media_group_id, 'msgid' => $update->channel_post->message_id,
                                'files' => [['parse_mode' => 'Markdown', 'type' => $type, 'caption' => Helper::MarkDown($caption), 'media' => $file_id]]]);
                            $this->user->save();
                        }
                        return;
                    }
//                        return;
//                    }


                    $caption = preg_replace("/@\w+/", "", $caption);
//                    if ($caption) {
//                        $caption .= PHP_EOL . "〰️〰️〰️〰️" . "\xD8\x9C" . "〰️〰️〰️〰️";
                    $caption2 = $caption . PHP_EOL;
                    $caption2 .= $ch->tag ?? "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL . $ch->chat_username;
//                    }

                    $update->channel_post->caption = $caption2;
                    $res = Helper::DeleteMessage($chat_id, $message_id);
                    if ($res->ok != true) {
                        Helper::sendMessage(Helper::$logs[0], $ch->chat_username . PHP_EOL . $res->result->description . PHP_EOL . json_encode($update), null);

//                        $ch->auto_tag = false;
//                        $ch->save();
                        return;
                    }
                    $res = $this->sendFile("$chat_id", json_encode($update->channel_post), $reply, false);
                    if ($res == null || $res->ok == false) {
                        $update->channel_post->caption = $caption;
                        $res = $this->sendFile("$chat_id", json_encode($update->channel_post), $reply, false);
                    }

//                    if ($from_message_id) {
//                        $this->copyMessage($chat_id, $from_chat_id, $from_message_id, $caption);
//                        Helper::DeleteMessage($chat_id, $message_id);
//                    } else {
//                        $this->EditMessageCaption($chat_id, $message_id, $caption);
//                    }
                }
            }


        } elseif
        ($tc == 'supergroup' || $tc == 'group') {


            if (preg_match('/^\/?(add|نصب)$/ui', $text, $match)) {

                if (!$this->Admin($chat_id, $from_id, $tc, $chat_username))
                    return;
                if (!$this->Admin($chat_id, $this->bot_id, $tc, $chat_username)) {
                    $this->sendMessage($chat_id, "🔹*ابتدا ربات را در گروه ادمین کنید و مجدد تلاش نمایید*", 'Markdown', $message_id);
                    return;
                }

                if ($chat_username == '@') {
                    $this->sendMessage($chat_id, "🔹کانال شما باید در حالت  *public* باشد.\n 🔸روی نام کانال کلیک کنید\n 🔸 در تلگرام موبایل از قسمت بالا *آیکن مداد* را انتخاب کنید.\n 🔸در تلگرام دسکتاپ از گزینه سه نقطه بالا گزینه  *Manage Channel* را انتخاب کنید \n\n 🔸 قسمت  *Channel type*  را به حالت *public*  تغییر دهید.\n 🔸سپس یک نام عمومی به کانال خود تخصیص دهید. *ربات کانال شما را توسط این نام شناسایی می کند*. \n 🔼 در صورت داشتن هر گونه سوال به قسمت *درباره ربات* مراجعه نمایید. \n $this->bot ", 'Markdown', $message_id);
                    return;
                }
                $this->user = User::where('groups', 'like', "%\"$chat_username\"%")->first();
                if (!$this->user) {
                    $this->sendMessage($chat_id, "🔸 ابتدا باید گروه را در ربات ثبت نمایید!\n🔸 *منوی اصلی ⬅ ثبت گروه💥* \n  $this->bot", 'Markdown', $message_id);
                    return;
                }

                $this->sendMessage($chat_id, "🔷 *ربات با موفقیت نصب شد. اکنون می توانید گروه خود را در دیوار ربات تبلیغ نمایید!* \n \n آموزش ربات \n $this->tut_link  $this->info", 'MarkDown', $message_id, $this->button);


            }
// elseif ($new_chat_member && ($chat_username == "@lamassaba" || $chat_username == "@magnetgramsupport")) {
//     $txt = "*سلام $first_name  عزیز . *مگنت گرام* هستم . با من میتونی برای گروه یا کانال خودت *فالور جذب کنی*. \n *من یه ربات شبیه دیوارم که گروه/کانال تو رو تبلیغ میکنم و بقیه از فالو کردن اون امتیاز میگیرند و میتونن کانال/گروه خودشونو تبلیغ کنن*.\n  نگران نباش *اگه کسی لفت داد* خودم جریمش میکنم!🚫  \n  *$this->bot ";
//     $buttons = [[['text' => '👈 ورود به ربات 👉', 'url' => "https://t.me/" . str_replace("@", "", $this->bot)]]];

//     $this->DeleteMessage($chat_id, $message_id);
//     $this->sendMessage($chat_id, $txt, "Markdown", null, json_encode(['inline_keyboard' => $buttons, 'resize_keyboard' => true]), true);


// }
            elseif
            ($new_chat_members) {

                if ($new_chat_member && ($chat_username == "@lamassaba" || $chat_username == "@magnetgramsupport")) {
                    $txt = " سلام $first_name کانال  خودت رو تو مگنت گرام رایگان تبلیغ کن! \n \n  " . PHP_EOL .
                        "کافیه وارد ربات شی و دکمه دیوار -> ثبت کانال در دیوار رو بزنی!" . PHP_EOL . $this->bot;


                    $buttons = [[['text' => '👈 ورود به ربات 👉', 'url' => 'https://t.me/' . str_replace('@', '', $this->bot)]]];

                    $this->DeleteMessage($chat_id, $message_id);

                    $res = Helper::sendMessage($chat_id, $txt, "Markdown", null, json_encode(['inline_keyboard' => $buttons, 'resize_keyboard' => true]), true);

                    if ($res && $res->ok == true) {
                        sleep(10);
                        $this->DeleteMessage($chat_id, $res->result->message_id);

                    }
                }

                return;

                $divar_item = Divar::where('chat_id', "$chat_id")->where('expire_time', '>=', Carbon::now())->first();
                if (!$divar_item) return;
                $vip = $divar_item->is_vip ? 2 : 1;

                $this->user = User::where('telegram_id', $from_id)->first();


                $score = 0;
                $count = 0;
                $adding = true;

                foreach ($new_chat_members as $member) {
                    if (!$member->is_bot && !Follower::where('chat_id', "$chat_id")->where('telegram_id', $member->id)->exists()) {
                        Follower::create(['chat_id' => "$chat_id", 'chat_username' => $chat_username, 'telegram_id' => $member->id, 'in_vip' => $divar_item->is_vip,
                            'user_id' => User::where('telegram_id', $member->id)->first()->id, 'added_by' => $from_id == $member->id ? null : "$from_id"]);

                        $count++;
//                        foreach ($this->logs as $log) {
//                            if ($from_id == $member->id)
//                                $this->sendMessage($log, " کاربر  " . " [$first_name](tg://user?id=$member->id) " . "\n $chat_username \nرا فالو کرد", 'Markdown', null, null, true);
//                            else
//                                $this->sendMessage($log, " کاربر $member->username " . " توسط " . " [$first_name](tg://user?id=$from_id) " . "به $chat_username اضافه شد", 'Markdown', null, null, true);
//
//                        }
                    }
                }
                if ($from_id == $new_chat_members[0]->id) $adding = false;
                if ($adding && $count > 0) {
                    $admin_telegram_id = User::where('groups', 'like', "%\"$chat_username\"%")->first()->telegram_id;
                    $this->sendMessage($admin_telegram_id, "💫 کاربر $username تعداد $count ممبر به $chat_username اضافه کرد!", "Markdown", null, null, false);
                    $score = $count * $this->add_score * $vip;
                }
                if (!$adding)
                    $score = $this->follow_score * $vip;
                if ($this->user && !Follower::where('chat_id', "$chat_id")->where('telegram_id', $this->user->telegram_id)->where('left', true)->exists()) {
                    $this->user->score += $score;
                    $this->user->save();
                    $score_total = $this->user->score;
                    if ($score != 0)
                        $this->sendMessage($this->user->telegram_id, "💫تبریک!\n تعداد $score سکه به شما افزوده شد!\n سکه فعلی: $score_total", "Markdown", null, null, false);
                }
            } //
            elseif ($left_chat_member) {


                $f = Follower::where('chat_id', "$chat_id")->where('telegram_id', $left_chat_member->id)->first();
                if ($f && !$f->left) {

                    $vip = $f->in_vip ? 2 : 1;

                    if ($f->added_by) {
                        $this->user = User::where('telegram_id', $f->added_by)->first();
                        $left_score = $this->add_score * $vip;
                        $from_id = $f->added_by;
                    } else {
                        $this->user = User::where('telegram_id', $f->telegram_id)->first();
                        $left_score = $this->follow_score * $vip;
                    }
                    if ($this->user) {
                        $this->user->score -= $left_score;
                        $score = $this->user->score;
                        $this->user->save();
                        if ($f->added_by)
                            $this->sendMessage($from_id, "💣متاسفانه به علت ترک گروه $chat_username تعداد $this->left_score سکه جریمه شدید!\nسکه فعلی: $score", "Markdown", null, null, false);
                        else
                            $this->sendMessage($from_id, "💣متاسفانه به علت ترک گروه یکی  اعضای اد شده توسط شما از $chat_username تعداد $this->left_score سکه جریمه شدید!\nسکه فعلی: $score", "Markdown", null, null, false);
                    }
                    $f->left = true;
                    $f->save();
                }


            }
        }
        if ($text == "/start$this->bot") {
//            $this->DeleteMessage($chat_id, $message_id);
            $buttons = [[['text' => '👈 ورود به ربات 👉', 'url' => "https://t.me/" . str_replace("@", "", $this->bot)]]];
            $this->sendMessage($chat_id, " $first_name " . "  \n برای تبلیغ گروه/کانال خود وارد ربات شوید.", "Markdown", null, json_encode(['inline_keyboard' => $buttons, 'resize_keyboard' => true]), true);
            foreach ($this->logs as $log)
                $this->sendMessage($log, "■  کاربر [$first_name](tg://user?id=$from_id) ربات مگنت گرام را استارت کرد.", 'MarkDown');

        }
        if ($text == 'بنر' || $Data == 'بنر' || $text == "📌 دریافت بنر تبلیغاتی 📌") {
            if (!$this->user) {
                $this->sendMessage($chat_id, "برای دریافت بنر مخصوص خود ابتدا در ربات ثبت نام کنید.", 'MarkDown', $message_id, null);
                return;
            }
            if ($tc == 'private') {
                $this->sendMessage($from_id, "بنر زیر را فوروارد کنید و در صورت ورود و ثبت کانال در دیوار توسط افراد دعوت شده, تعداد $this->ref_score سکه دریافت نمایید. ", "Markdown", null, null, true);

            }
            $rLink = "https://t.me/" . str_replace("@", "", $this->bot) . "?start=" . base64_encode("$from_id");
            $buttons = [[['text' => '👈 ورود به ربات 👉', 'url' => $rLink]]];
            $this->sendMessage($chat_id, "🅼🅰🅶🅽🅴🆃 🅶🆁🅰🅼" . PHP_EOL . "⭐️*ربات دستیار ادمین مگنت گرام 💫 برای کانال و گروه شما*\n📌 امکانات \n💣 لینکدونی \n💣 تبادل لیستی \n💣 تگ اتوماتیک \n💣 اپلیکیشن اندروید \nو...\n مگنت گرام 👑 دیوار تلگرام \n📌 برای تبلیغ کانال خود وارد ربات شوید.\n" . $rLink . PHP_EOL . PHP_EOL . "🅼🅰🅶🅽🅴🆃 🅶🆁🅰🅼", "MarkDown", null, json_encode(['inline_keyboard' => $buttons, 'resize_keyboard' => true]), false);

        }
        //referral
        if (strpos($text, "/start ") !== false) { // agar ebarate /start ersal shod
            $this->user = User::where('telegram_id', $from_id)->first();
//            $button = json_encode(['keyboard' => [
//                in_array($from_id, $this->Dev) ? [['text' => 'پنل مدیران🚧']] : [],
//                [['text' => 'دیوار📈']],
//                [['text' => "🎴 ساخت دکمه شیشه ای 🎴"], ['text' => "📌 دریافت بنر تبلیغاتی 📌"]],
//                [['text' => 'سکه های من💰'], ['text' => 'جریمه افراد لفت داده📛']],
//                [['text' => 'ثبت گروه💥'], ['text' => 'ثبت کانال💥']],
//                [['text' => 'مدیریت گروه ها📢'], ['text' => 'مدیریت کانال ها📣']],
//
//                [['text' => $this->user ? "ویرایش اطلاعات✏" : "ثبت نام✅"]],
//                [['text' => 'درباره ربات🤖']],
//            ], 'resize_keyboard' => true]);

//            if ($this->user) return;

            $this->sendMessage($chat_id, "■ سلام $first_name به مگنت گرام خوش اومدی✋\n  " . "⚡ توسط این ربات میتونی گروه و کانالتو در 📈دیوار (لینکدونی) ثبت کنی و یا 💫تبادل چرخشی شبانه اتوماتیک انجام بدی! برای شروع دکمه دیوار و سپس ثبت در دیوار (لینکدونی) رو بزن و کانالتو ثبت کن" . PHP_EOL . " لینکدونی (دیوار): " . Helper::$divarChannel . PHP_EOL . " پشتیبانی: " . Helper::$admin, null, $message_id, $button);
            foreach ($this->logs as $log)
                $this->sendMessage($log, "■  کاربر [$first_name](tg://user?id=$from_id) ربات مگنت گرام را استارت کرد.", 'MarkDown');
            $inviter_code = substr($text, 7); // joda kardan id kasi ke rooye linke davatesh click shode

            if (!empty($inviter_code)) {
                $res = explode("$", base64_decode($inviter_code));

                $telegram_id = $res[0];

                if (count($res) > 1) {
                    $chat_id = $res[1];
                    $this->sendChannelBanner($from_id, $telegram_id, $chat_id, false);
                }


                if (Ref::where('new_telegram_id', "$from_id")->exists())
                    return;

                try {
                    Ref::create(['new_telegram_id' => $from_id, 'invited_by' => "$telegram_id"]);
                    $this->sendMessage($telegram_id, " \n🔔\n " . " هم اکنون " . " [$first_name](tg://user?id=$from_id) " . " با لینک دعوت شما وارد ربات شد. در صورت هر بار ثبت کانال در دیوار توسط او, $this->ref_score سکه به شما اضافه خواهد شد!  " . "\n$this->bot", "Markdown", null, null, false);
                    $this->sendMessage(Helper::$logs[0], " \n🔔\n " . "  کاربر دعوت شده " . " [$first_name](tg://user?id=$from_id) " . PHP_EOL . " [کاربر دعوت کرده](tg://user?id=$telegram_id) ", "Markdown", null, null, false);

                } catch (\Exception $e) {
                    $this->sendMessage(Helper::$logs[0], $e->getMessage(), "Markdown", null, null, false);

                }


            }

        }
        // follow a chat from divar


        if (strpos($Data, "divar_i_followed_vip$") !== false) {
            $this->user = User::where('telegram_id', "$from_id")->first();
            if (!$this->user) {
                $this->popupMessage($data_id, " ⛔ برای دریافت سکه ابتدا در ربات ثبت نام کنید!\n\n$this->bot   ");
                return;
            }
            $input = explode("$", $Data);
            $chatId = $input[1];
            $follow_score = Helper::$follow_score;

            $chat = Chat::where('chat_id', "$chatId")->where('active', true)->first();
            if (!$chat) {
                $this->popupMessage($data_id, " ⛔در دیوار وجود ندارد و یا غیر فعال شده است.");
                return;
            }

            $uic = $this->user_in_chat($chatId, $this->user->telegram_id);

            if ($uic == 'administrator' || $uic == 'creator') {
                $this->popupMessage($data_id, "⛔ شما مالک یا ادمین هستید!");
            } elseif ($uic != 'member') {
                $this->popupMessage($data_id, "⛔ هنوز عضو نشده اید و یا ربات در کانال نیست!");

            } elseif ($uic == 'member') {
                if (Follower::where('telegram_id', $this->user->telegram_id)->where('chat_id', "$chatId")->exists()) {
                    $this->popupMessage($data_id, "⛔ شما قبلا امتیاز خود را دریافت کرده اید");
//                            $this->DeleteMessage($chat_id, $message_id);
                    return;
                }

                $this->user->score += $follow_score;
                $this->user->save();

                $txt = " ✅ عضو جدید به کانال $chat->chat_username اضافه شد: ";
                $txt .= "[$first_name](tg:user?id=$from_id)" . PHP_EOL;

                $owner_user = User::where('id', "$chat->user_id")->first();

                Helper::sendMessage($owner_user->telegram_id, Helper::MarkDown($txt), 'MarkDown');
                Helper::sendMessage(Helper::$logs[0], Helper::MarkDown($txt), 'MarkDown');
                $this->popupMessage($data_id, "👏با موفقیت اضافه شدید! \n $follow_score ‌سکه به شما اضافه شد!  \n تعداد سکه فعلی : $score 💰" . PHP_EOL . "✅ لفت دادن بعد از " . Helper::$remain_member_day_limit . " روز جریمه ای نخواهد داشت. ");


                Follower::create(['chat_id' => "$chatId", 'telegram_id' => $from_id,
                    'added_by' => null, 'follow_score' => $follow_score, 'ref_score' => 0,]);

            }
        }


        if (strpos($Data, "divar_i_followed$") !== false) {
            date_default_timezone_set('Asia/Tehran');
//            $this->popupMessage($data_id, " ⛔این بخش هنوز فعال نشده است");
//            return;
            $this->user = User::where('telegram_id', "$from_id")->first();
            if (!$this->user) {
                $this->popupMessage($data_id, " ⛔ برای دریافت سکه ابتدا در ربات ثبت نام کنید!\n\n$this->bot   ");
                return;
            }
            $input = explode("$", $Data);
            $chatId = $input[1];

            $divar = Divar::where('chat_id', "$chatId")->where('expire_time', '>=', Carbon::now())->first();
            if (!$divar) {
                $this->popupMessage($data_id, " ⛔در دیوار وجود ندارد و یا غیر فعال شده است.");
                return;
            }
            if (count($input) > 2) {
                $added_by = $input[2];
                $added_by_user = User::where('telegram_id', "$added_by")->first();
                if ($added_by == $from_id) {
                    $this->popupMessage($data_id, "⛔ از بنر دعوت خود نمی توانید امتیاز کسب کنید!");
                    return;
                }
            }
            $owner_user = User::where('id', "$divar->user_id")->first();
//            $order = Order::where()->first();

            if (!$owner_user || ($owner_user->score < $divar->follow_score + ($added_by_user ? $divar->ref_score : 0))) {
                $this->popupMessage($data_id, " ⌚زمان این جایزه به پایان رسیده است.");
                return;
            }


            $uic = $this->user_in_chat($chatId, $this->user->telegram_id);

            if ($uic == 'administrator' || $uic == 'creator') {
                $this->popupMessage($data_id, "⛔ شما مالک یا ادمین هستید!");
            } elseif ($uic != 'member') {
                $this->popupMessage($data_id, "⛔ هنوز عضو نشده اید و یا ربات در کانال نیست!");

            } elseif ($uic == 'member') {

                if (Follower::where('telegram_id', $this->user->telegram_id)->where('chat_id', "$chatId")->exists()) {
                    $this->popupMessage($data_id, "⛔ شما قبلا امتیاز خود را دریافت کرده اید");
//                            $this->DeleteMessage($chat_id, $message_id);
                    return;
                }

                Follower::create(['chat_id' => "$chatId", 'telegram_id' => $from_id,
                    'added_by' => $added_by, 'follow_score' => $divar->follow_score, 'ref_score' => $divar->ref_score,]);

                $this->user->score += $divar->follow_score;
                $owner_user->score -= $divar->follow_score;
                $this->user->save();
                if ($added_by_user) {
                    $added_by_user->score += $divar->ref_score;
                    $owner_user->score -= $divar->ref_score;
                    $added_by_user->save();
                    Helper::sendMessage($added_by, "🎉 کاربر دعوت شده توسط شما عضو " . $divar->chat_username . " شد و " . $divar->ref_score . " سکه دریافت کردید " . PHP_EOL . "سکه فعلی: " . $added_by_user->score, null);
                }
                $owner_user->save();
                $txt = " ✅ عضو جدید به کانال $divar->chat_username اضافه شد: ";
                $txt .= "[$first_name](tg:user?id=$from_id)" . PHP_EOL;
                if ($added_by_user) {
                    $txt .= " ☑ دعوت کننده: ";
                    $txt .= "[$added_by_user->name](tg:user?id=$added_by_user->telegram_id)";
                }
                $txt .= "💰 سکه فعلی: " . $owner_user->score . PHP_EOL;
                if ($owner_user->score < $divar->follow_score)
                    $txt .= "🚨 لطفا برای ادامه عضو گیری حساب خود را شارژ کنید";

                $score = $this->user->score;
                Helper::sendMessage($owner_user->telegram_id, Helper::MarkDown($txt), 'MarkDown');
                $this->popupMessage($data_id, "👏با موفقیت اضافه شدید! \n $divar->follow_score ‌سکه به شما اضافه شد!  \n تعداد سکه فعلی : $score 💰" . PHP_EOL . "✅ لفت دادن بعد از " . Helper::$remain_member_day_limit . " روز جریمه ای نخواهد داشت. ");

            } else {
                $this->popupMessage($data_id, "شما هنوز عضو این کانال/گروه نشده اید و یا ربات در این کانال/گروه وجود ندارد!\n دکمه نمایش را زده و عضو شده و مجددا تلاش نمایید");
            }
            return;
        }
        //advertise a chat from divar
        if (strpos($Data, "divar_i_advertise$") !== false) {

            $this->user = User::where('telegram_id', "$from_id")->first();
            if (!$this->user) {
                $this->popupMessage($data_id, " ⛔ ابتدا در ربات ثبت نام کنید!\n\n$this->bot   ");
                return;
            }
            $chatId = explode("$", $Data)[1];

            $d = Divar::where('chat_id', "$chatId")->first();
            if (!$d) {
                $this->popupMessage($data_id, "⛔ زمان جایزه این کانال به اتمام رسیده است!");
                return;
            }
            $user = User::where('id', $d->user_id)->first();
            if (!$user || $user->score < $d->ref_score + $d->follow_score) {
                $this->popupMessage($data_id, "⛔ جایزه این کانال به اتمام رسیده است!" . PHP_EOL . "ادمین می تواند آن را تمدید کند");
                return;
            }

            $uic = $this->user_in_chat($chatId, $this->user->telegram_id);

            if ($uic == 'administrator' || $uic == 'creator') {
                $this->popupMessage($data_id, "⛔ مالک یا ادمین نمی تواند برای کانال خود جایزه تبلیغ بگیرد!");
                return;
            }


            $this->sendChannelBanner($from_id, $from_id, $chatId, true);

            $this->popupMessage($data_id, "✅ بنر اختصاصی برای تبلیغ این کانال به شما ارسال شد.");
            return;
        }
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
//        unlink("error_log");
    }

    private
    function addToDivar($user_id, $chat_id, $chat_type, $chat_username, $chat_title, $chat_description, $show_time = 10)
    {
        if (!Divar::where('chat_id', $chat_id)->exists())
            Divar::create(['user_id' => $user_id, 'chat_id' => $chat_id, 'chat_type' => $chat_type, 'chat_username' => $chat_username,
                'chat_title' => $chat_title, 'chat_description' => $chat_description, 'show_time' => $show_time, 'start_time' => time(),]);
    }

    private
    function getDivar($page = 1, $chat_id)
    {
        $buttons = null;

        $divar_cell = "";
        $items = Divar::/*where('expire_time', '>=', Carbon::now())*/
        get()->inRandomOrder();


        foreach ($items as $idx => $item) {
            $uic = $this->user_in_chat($item->chat_id, $this->user->telegram_id);
            // Storage::prepend('file.log', json_encode($uic));
            if ($uic == 'creator' || $uic == 'administrator') {
                $buttons = [[['text' => '✅ مالک هستید ✅', 'url' => "https://t.me/" . str_replace('@', '', $item->chat_username) /*'https://tg://user?id=72534783'*/],
                ]];

            } else if ($uic == 'member' /*|| $uic == null*/)
                continue;

            else
                $buttons = [[['text' => $item->chat_type != 'channel' ? '👈 نمایش و عضویت👉' : '👈 نمایش 👉', 'url' => "https://t.me/" . str_replace('@', '', $item->chat_username) /*'https://tg://user?id=72534783'*/]],
                    $item->chat_type == 'channel' ? [['text' => '✅ عضو شدم ✅', 'callback_data' => "divar_i_register$" . $item->chat_id . '$' . $item->chat_username]] : []];

            $txt = str_replace('~n~', $item->chat_username, str_replace('~t~', $item->chat_title, str_replace('~d~', $item->chat_description, $divar_cell)));

            $this->sendMessage($chat_id, $txt, null, null, json_encode(['inline_keyboard' => $buttons, 'resize_keyboard' => true]));
        }
        if ($buttons == null)
            $this->sendMessage($chat_id, "هم اکنون کانال/گروهی در دیوار وجود ندارد.", "Markdown", null, null);


    }

    private
    function popupMessage2($data_id, $from_id, $message)
    {
        return Helper::creator('CallbackQuery', [
            'id' => $data_id,
            'from' => $from_id,
            'message' => $message,

        ]);
    }

    private
    function popupMessage($data_id, $text)
    {
        return Helper::creator('answerCallbackQuery', [
            'callback_query_id' => $data_id,
            'text' => $text,

            'show_alert' => true, # popup / notification
            'url' => null,# t.me/your_bot?start=XXXX,
            'cache_time' => null
        ]);
    }

    function sendMessage($chat_id, $text, $mode, $reply = null, $keyboard = null, $disable_notification = false)
    {
        return Helper::creator('sendMessage', [
            'chat_id' => $chat_id,
            'text' => $text,
            'parse_mode' => $mode,
            'reply_to_message_id' => $reply,
            'reply_markup' => $keyboard,
            'disable_notification' => $disable_notification,
        ]);
    }


//    function creator($method, $datas = [])
//    {
//        $url = "https://api.telegram.org/bot" . API_KEY . "/" . $method;
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
//        $res = curl_exec($ch);
//
//        if (curl_error($ch)) {
//            var_dump(curl_error($ch));
//        } else {
//            return json_decode($res);
//        }
//    }

    private
    function inviteToChat($chat_id)
    {

        return Helper::creator('exportChatInviteLink', ['chat_id' => $chat_id,]);

    }

    private
    function getChatMembersCount($chat_id)
    {
        $res = Helper::creator('getChatMembersCount', ['chat_id' => $chat_id,])->result;
        if ($res)
            return (int)$res; else return 0;
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

    private
    function Admin($chat_id, $from_id, $chat_type, $chat_username)
    {
        if ($chat_type == 'supergroup' || $chat_type == 'group') {
            $get = Helper::creator('getChatMember', ['chat_id' => $chat_id, 'user_id' => $from_id]);
            $rank = $get->result->status;

            if ($rank == 'creator' || $rank == 'administrator') {
                return true;
            } else {
//                $this->sendMessage($chat_id, "■  کاربر غیر مجاز \n $this->bot  ", 'MarkDown', null);
                return false;
            }
        } else if ($chat_type == 'channel') {
            return true;
//            $admins = Helper::creator('getChatAdministrators', ['chat_id' => $chat_id])->result;
//            $is_admin = false;
//
//            foreach ($admins as $admin) {
//                if ($from_id == $admin->user->id) {
//                    $is_admin = true;
//                }
//            }
//            return $from_id;

//            $this->user = User::whereIn('telegram_id', $admin_ids)->orWhere('channels', 'like', "%[$chat_username,%")
//                ->orWhere('channels', 'like', "%,$chat_username,%")
//                ->orWhere('channels', 'like', "%,$chat_username]%")->first();
//            if (!User::orWhere('channels', 'like', "%[$chat_username,%")
//                ->orWhere('channels', 'like', "%,$chat_username,%")
//                ->orWhere('channels', 'like', "%,$chat_username]%")->exists())
//                $this->sendMessage($chat_id, "■ ابتدا کانال را در ربات ثبت نمایید  \n📣$this->bot  ", 'MarkDown', null);


//            return $this->user ? true : false;
        }
    }

    private
    function get_chat_type($chat_id)
    {
        $res = Helper::creator('getChat', [
            'chat_id' => $chat_id,

        ]);
        if ($res->ok == false)
            return $res->description;
        return $res->result->type;
    }

    private
    function user_in_chat($chat_id, $user_id, $chat_type = null)
    {


        $res = Helper::creator('getChatMember', [
            'chat_id' => $chat_id,
            'user_id' => $user_id
        ]);
        if ($res->ok == false)
            return $res->description;
        return $res->result->status;
    }

    private
    function copyMessage($chat_id, $from_chat_id, $message_id, $text, $mode = null, $keyboard = null)
    {
        Helper::creator('copyMessage', [
            'chat_id' => $chat_id,
            'from_chat_id' => $from_chat_id,
            'message_id' => $message_id,
            'caption' => $text,
            'parse_mode' => $mode,
            'reply_markup' => $keyboard
        ]);
    }

    private
    function EditMessageCaption($chat_id, $message_id, $text, $mode = null, $keyboard = null)
    {
        return Helper::creator('editMessageCaption', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'caption' => $text,
            'parse_mode' => $mode,
            'reply_markup' => $keyboard
        ]);
    }

    private
    function EditMessageText($chat_id, $message_id, $text, $mode = null, $keyboard = null)
    {
        return Helper::creator('EditMessageText', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'text' => $text,
            'parse_mode' => $mode,
            'reply_markup' => $keyboard
        ]);
    }

    private
    function EditKeyboard($chat_id, $message_id, $keyboard)
    {
        Helper::creator('EditMessageReplyMarkup', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'reply_markup' => $keyboard
        ]);
    }

    private
    function DeleteMessage($chatid, $massege_id)
    {
        Helper::creator('DeleteMessage', [
            'chat_id' => $chatid,
            'message_id' => $massege_id
        ]);
    }

    private
    function Kick($chatid, $fromid)
    {
        Helper::creator('KickChatMember', [
            'chat_id' => $chatid,
            'user_id' => $fromid
        ]);
    }

    private
    function Forward($chatid, $from_id, $massege_id)
    {
        Helper::creator('ForwardMessage', [
            'chat_id' => $chatid,
            'from_chat_id' => $from_id,
            'message_id' => $massege_id
        ]);
    }

    function MarkDown($string)
    {
        return str_replace(["`", "_", "*", "[", "]"], "\t", $string);
    }


    private
    function check($what, $text, $chat_id, $message_id, $cancel_button)
    {
        $message = null;
        if ($what == 'name') {
            if (strlen($text) < 5)
                $message = "نام  حداقل 5 حرف باشد";
            elseif (strlen($text) > 50)
                $message = "نام  حداکثر 50 حرف باشد";
            elseif (User::where("name", $text)->exists())
                $message = "نام  تکراری است";
        } else if ($what == 'password') {
            if (strlen($text) < 5)
                $message = "طول گذرواژه حداقل 5";
            elseif (strlen($text) > 50)
                $message = "طول گذرواژه حداکثر 50";

        } else if ($what == 'channel') {

            if (Chat::where('chat_username', $text)->exists())
                $message = "این کانال از قبل ثبت شده است!";

            elseif ($this->get_chat_type($text) != 'channel')
                $message = "ورودی شما از نوع کانال نیست و یا ربات را بلاک کرده اید";

            //temporary disable admin check
//            else {
//                $result = $this->user_in_chat($text, $this->user->telegram_id);
//                if ($result == "Bad Request: user not found")
//                    $message = "شما عضو این کانال نیستید!";
//                elseif ($result == "Bad Request: chat not found")
//                    $message = "کانال وجود ندارد!";
//                elseif ($result != "creator" && $result != "administrator")
//                    $message = "شما مدیر کانال نیستید !";
//            }
        } else if ($what == 'group') {
            $type = $this->get_chat_type($text);
            $bot_role = $this->user_in_chat($text, $this->bot_id);

            if (Chat::where('chat_username', $text)->exists())
                $message = "این گروه از قبل ثبت شده است!";
            else if ($type != 'group' && $type != 'supergroup')
                $message = "این گروه وجود ندارد و یا ربات در گروه  نیست";
            else if ($bot_role != 'administrator' && $bot_role != 'creator')
                $message = 'ربات در گروه ادمین نیست. ربات را ادمین گروه کرده و مجدد تلاش نمایید';
            else {
                $result = $this->user_in_chat($text, $this->user->telegram_id);
                if ($result == "Bad Request: user not found")
                    $message = "شما عضو این گروه نیستید!";
                else if ($result == "Bad Request: chat not found")
                    $message = "گروه وجود ندارد!";
                else if ($result != "creator" && $result != "administrator")
                    $message = "فقط مدیران گروه میتواند آن را ثبت کنند!";
            }
        }

        if ($message) {
            $this->sendMessage($chat_id, $message, 'MarkDown', $message_id, $cancel_button);
            return false;
        } else {
            return true;
        }

    }

    public
    function request($request)
    {


        $http = new \GuzzleHttp\Client(['base_uri' => $request['url'],
        ]);

        try {
            $response = $http->post(/*route('passport.token'*/
                ''
                , [

                'headers' => ['cache-control' => 'no-cache',
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ],
                'form_params' => $request['params']
            ]);

            return json_decode($response->getBody()->getContents(), true)["result"]["status"];
        } catch (\Guzzlehttp\Exception\BadResponseException $e) {
            if ($e->getCode() == 400) {
                return json_decode($e->getResponse()->getBody()->getContents(), true)["description"];
            } else if ($e->getCode() == 401) {
                return response()->json($e->getMessage(), $e->getCode());
            }
            return response()->json($e->getMessage(), $e->getCode());

        }
    }

    private
    function sendFile($chat_id, $storage, $reply = null, $tag = true)
    {


        $message = json_decode($storage);
        $poll = $message->poll;
        $text = $message->text;
        $sticker = $message->sticker;  #width,height,emoji,set_name,is_animated,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,
        $animation = $message->animation;  #file_name,mime_type,width,height,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,

        $photo = $message->photo; #file_id,file_unique_id,file_size,width,height] array of different photo sizes
        $document = $message->document; #file_name,mime_type,thumb[file_id,file_unique_id,file_size,width,height]
        $video = $message->video; #duration,width,height,mime_type,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,height]
        $audio = $message->audio; #duration,mime_type,title,performer,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,height]
        $voice = $message->voice; #duration,mime_type,file_id,file_unique_id,file_size
        $video_note = $message->video_note; #duration,length,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,height]
        $caption = $message->caption;
        $media_group_id = $message->media_group_id;

        $media = $photo ? $photo : ($document ? $document : ($video ? $video : ($audio ? $audio : ($voice ? $voice : null))));

//        $media = [];
//        if ($photo) {
//            foreach ($photo as $item) {
//                $media[] = ['type' => 'photo', 'media' => $item->file_id, 'caption' => $caption];
//
//            }
//        }

        if ($text) {
            $adv_section = explode('banner=', $text); //banner=name=@id
            $text = $adv_section[0];
        } else if ($caption) {
            $adv_section = explode('banner=', $caption);
            $caption = $adv_section[0];
        }
        if (is_countable($adv_section) && count($adv_section) == 2) {

            $link = explode('=', $adv_section[1]);
            $trueLink = $link[1];
            foreach ($link as $idx => $li) {
                if ($idx > 1)
                    $trueLink .= ('=' . $li);
            }
            $buttons = [[['text' => "👈 $link[0] 👉", 'url' => $trueLink]], [['text' => '👈 محل تبلیغ کانال و گروه شما 👉', 'url' => "https://t.me/" . str_replace("@", "", $this->bot)]]];
        } else {
            if ($text && $tag) $text = $text . "\n\n" . $this->bot;
            else if ($caption && $tag) $caption = $caption . "\n\n" . $this->bot;
            $buttons = [[['text' => '👈 محل تبلیغ کانال و گروه شما 👉', 'url' => "https://t.me/" . str_replace("@", "", $this->bot)]]];
        }

        $caption = Helper::MarkDown($caption);

        if ($tag)
            $keyboard = json_encode(['inline_keyboard' => $buttons, 'resize_keyboard' => true]);


        if ($text)
            return Helper::creator('SendMessage', [
                'chat_id' => $chat_id,
                'text' => Helper::MarkDown($text),
                'parse_mode' => 'Markdown',
                'reply_to_message_id' => $reply,
                'reply_markup' => $keyboard
            ]);
//        elseif ($media_group_id && $media)
//            Helper::creator('sendMediaGroup', [
//                'chat_id' => $chat_id,
//                'media' => $media[count($media) - 1]->file_id /*[count($photo) - 1]->file_id*/,
//
//
//                'reply_to_message_id' => $reply,
//                'reply_markup' => $keyboard
//            ]);
        elseif ($photo)
            return Helper::creator('sendPhoto', [
                'chat_id' => $chat_id,
                'photo' => $photo[count($photo) - 1]->file_id,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
                'reply_to_message_id' => $reply,
                'reply_markup' => $keyboard
            ]);
        elseif ($audio)
            return Helper::creator('sendAudio', [
                'chat_id' => $chat_id,
                'audio' => $audio->file_id,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
                'duration' => $audio->duration,
                'performer' => $audio->performer,
                'title' => $audio->title,
                'thumb' => $audio->thumb->file_id,
                'reply_to_message_id' => $reply,
                'reply_markup' => $keyboard
            ]);
        elseif ($document)
            return Helper::creator('sendDocument', [
                'chat_id' => $chat_id,
                'document' => $document->file_id,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
                'thumb' => $document->thumb->file_id,
                'reply_to_message_id' => $reply,
                'reply_markup' => $keyboard
            ]);
        elseif ($video)
            return Helper::creator('sendVideo', [
                'chat_id' => $chat_id,
                'video' => $video->file_id,
                'duration' => $video->duration,
                'width' => $video->width,
                'height' => $video->height,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
                'thumb' => $video->thumb->file_id,
                'reply_to_message_id' => $reply,
                'reply_markup' => $keyboard
            ]);
        elseif ($animation)
            return Helper::creator('sendAnimation', [
                'chat_id' => $chat_id,
                'animation' => $animation->file_id,
                'duration' => $animation->duration,
                'width' => $animation->width,
                'height' => $animation->height,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
                'thumb' => $animation->thumb->file_id,
                'reply_to_message_id' => $reply,
                'reply_markup' => $keyboard
            ]);
        elseif ($voice)
            return Helper::creator('sendVoice', [
                'chat_id' => $chat_id,
                'voice' => $voice->file_id,
                'duration' => $voice->duration,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
                'reply_to_message_id' => $reply,
                'reply_markup' => $keyboard
            ]);
        elseif ($video_note)
            return Helper::creator('sendVideoNote', [
                'chat_id' => $chat_id,
                'video_note' => $video_note->file_id,
                'duration' => $video_note->duration,
                'length' => $video_note->length,
                'thumb' => $video_note->thumb->file_id,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
                'reply_to_message_id' => $reply,
                'reply_markup' => $keyboard
            ]);
        elseif ($sticker)
            return Helper::creator('sendSticker', [
                'chat_id' => $chat_id,
                'sticker' => $sticker->file_id,
                "set_name" => "DaisyRomashka",
                'reply_to_message_id' => $reply,
                'reply_markup' => $keyboard
            ]);
        elseif ($poll)
            return Helper::creator('sendPoll', [
                'chat_id' => $chat_id,
                'question' => $poll->question,
                'options' => json_encode(array_column($poll->options, 'text')),//  ,
                'type' => $poll->type,//quiz
                'allows_multiple_answers' => $poll->allows_multiple_answers,
                'is_anonymous' => $poll->is_anonymous,
                'correct_option_id' => $poll->correct_option_id, // index of correct answer for quiz
// //            'open_period' => 5-600,   this or close_date
// //            'close_date' => 5, 5 - 600,
                'reply_to_message_id' => $reply,
                'reply_markup' => $keyboard
            ]);
        return null;
    }

    private
    function sendBanner($chat_id, $storage)
    {


        $storage = json_decode($storage);
        $message = json_decode($storage->message);
        $link = $storage->link;
        $name = $storage->name;
        $poll = $message->poll;
        $text = $message->text;
        $sticker = $message->sticker;  #width,height,emoji,set_name,is_animated,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,
        $animation = $message->animation;  #file_name,mime_type,width,height,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,

        $photo = $message->photo; #file_id,file_unique_id,file_size,width,height] array of different photo sizes
        $document = $message->document; #file_name,mime_type,thumb[file_id,file_unique_id,file_size,width,height]
        $video = $message->video; #duration,width,height,mime_type,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,height]
        $audio = $message->audio; #duration,mime_type,title,performer,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,height]
        $voice = $message->voice; #duration,mime_type,file_id,file_unique_id,file_size
        $video_note = $message->video_note; #duration,length,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,height]
        $caption = $message->caption;


        $buttons = [[['text' => "👈 $name 👉", 'url' => $link]]];

        $keyboard = json_encode(['inline_keyboard' => $buttons, 'resize_keyboard' => true]);
        Storage::put("log.txt", $text);

        if ($text)
            Helper::creator('SendMessage', [
                'chat_id' => $chat_id,
                'text' => $text /*. "\n $this->bot"*/,
//                'parse_mode' => 'Markdown',
                'reply_to_message_id' => null,
                'reply_markup' => $keyboard
            ]);
        else if ($photo)
            Helper::creator('sendPhoto', [
                'chat_id' => $chat_id,
                'photo' => $photo[count($photo) - 1]->file_id,
                'caption' => $caption,
//                'parse_mode' => 'Markdown',
                'reply_to_message_id' => null,
                'reply_markup' => $keyboard
            ]);
        else if ($audio)
            Helper::creator('sendAudio', [
                'chat_id' => $chat_id,
                'audio' => $audio->file_id,
                'caption' => $caption,
//                'parse_mode' => 'Markdown',
                'duration' => $audio->duration,
                'performer' => $audio->performer,
                'title' => $audio->title,
                'thumb' => $audio->thumb,
                'reply_to_message_id' => null,
                'reply_markup' => $keyboard
            ]);
        else if ($document)
            Helper::creator('sendDocument', [
                'chat_id' => $chat_id,
                'document' => $document->file_id,
                'caption' => $caption,
//                'parse_mode' => 'Markdown',
                'thumb' => $document->thumb,
                'reply_to_message_id' => null,
                'reply_markup' => $keyboard
            ]);
        else if ($video)
            Helper::creator('sendVideo', [
                'chat_id' => $chat_id,
                'video' => $video->file_id,
                'duration' => $video->duration,
                'width' => $video->width,
                'height' => $video->height,
                'caption' => $caption,
//                'parse_mode' => 'Markdown',
                'thumb' => $video->thumb,
                'reply_to_message_id' => null,
                'reply_markup' => $keyboard
            ]);
        else if ($animation)
            Helper::creator('sendAnimation', [
                'chat_id' => $chat_id,
                'animation' => $animation->file_id,
                'duration' => $animation->duration,
                'width' => $animation->width,
                'height' => $animation->height,
                'caption' => $caption,
//                'parse_mode' => 'Markdown',
                'thumb' => $animation->thumb,
                'reply_to_message_id' => null,
                'reply_markup' => $keyboard
            ]);
        else if ($voice)
            Helper::creator('sendVoice', [
                'chat_id' => $chat_id,
                'voice' => $voice->file_id,
                'duration' => $voice->duration,
                'caption' => $caption,
//                'parse_mode' => 'Markdown',
                'reply_to_message_id' => null,
                'reply_markup' => $keyboard
            ]);
        else if ($video_note)
            Helper::creator('sendVideoNote', [
                'chat_id' => $chat_id,
                'video_note' => $video_note->file_id,
                'duration' => $video_note->duration,
                'length' => $video_note->length,
                'thumb' => $video_note->thumb,
                'caption' => $caption,
//                'parse_mode' => 'Markdown',
                'reply_to_message_id' => null,
                'reply_markup' => $keyboard
            ]);
        else if ($sticker)
            Helper::creator('sendSticker', [
                'chat_id' => $chat_id,
                'sticker' => $sticker->file_id,
                "set_name" => "DaisyRomashka",
                'reply_to_message_id' => null,
                'reply_markup' => $keyboard
            ]);
        else if ($poll)
            Helper::creator('sendPoll', [
                'chat_id' => $chat_id,
                'question' => $poll->question,
                'options' => json_encode(array_column($poll->options, 'text')),//  ,
                'type' => $poll->type,//quiz
                'allows_multiple_answers' => $poll->allows_multiple_answers,
                'is_anonymous' => $poll->is_anonymous,
                'correct_option_id' => $poll->correct_option_id, // index of correct answer for quiz
// //            'open_period' => 5-600,   this or close_date
// //            'close_date' => 5, 5 - 600,
//                'reply_to_message_id' => $reply,
                'reply_markup' => $keyboard
            ]);

//        Storage::delete("$chat_id.txt");
    }


    private
    function createUserImage($user_id)
    {

        $client = new \GuzzleHttp\Client();
        $res = creator('getUserProfilePhotos', [
            'user_id' => $user_id,

        ])->result->photos;
        // return json_encode($res);
        if (!isset($res) || count($res) == 0) return;
        $res = creator('getFile', [
            'file_id' => $res[0][count($res[0]) - 1]->file_id,

        ])->result->file_path;

        $image = "https://api.telegram.org/file/bot" . env('TELEGRAM_BOT_TOKEN', 'YOUR-BOT-TOKEN') . "/" . $res;
        Storage::put("public/users/$user_id.jpg", $client->get($image)->getBody());

    }

    private
    function getUserOrRegister($first_name, $last_name, $username, $from_id)
    {
        $this->user = null;
        if ($from_id == null) {
            return;
        }
        $this->user = User::where('telegram_id', "$from_id")->first();
        if (!$this->user) {
            $name = "";
            if ($first_name != "") {
                if (mb_strlen($first_name) > 50)
                    $name = mb_substr($first_name, 0, 49);
                else $name = $first_name;
            } elseif ($last_name != "") {
                if (mb_strlen($last_name) > 50)
                    $name = mb_substr($last_name, 0, 49);

            } elseif ($username != "" && $username != "@") {
                if (mb_strlen($username) > 50)
                    $name = mb_substr($username, 1, 49);
            } else
                $name = "$from_id";

//            if (!User::where('telegram_id', $from_id)->exists()) {
            $this->user = User::create(['telegram_id' => "$from_id", 'telegram_username' => $username, 'score' => $this->init_score, 'step' => null, 'name' => $name]);
//            }
        } else {
            if ($this->user->telegram_username != $username) {
                $this->user->telegram_username = $username;
                $this->user->save();
            }
            return;
        }
    }

    private
    function sendChannelBanner($send_to_id, $advertiser_id, $chat_id, $is_advertise)
    {

        $divar = Divar::where('chat_id', "$chat_id")->first();
        $user = User::where('id', $divar->user_id)->first();

        $rLink = "https://t.me/" . str_replace("@", "", $this->bot) . "?start=" . base64_encode("$advertiser_id$$chat_id");


        $address = "🔗 " . ($is_advertise ? (" ورود و دریافت سکه " . PHP_EOL . $rLink) : "$divar->chat_username");


        $line = array(

            "➖➖➖➖➖➖➖➖➖➖➖",
//            "➿➿➿➿➿➿➿➿➿➿➿",
            "🕳️🕳️🕳️🕳️🕳️🕳️🕳️🕳️🕳️🕳️🕳️",
            "〰️〰️〰️〰️〰️〰️〰️〰️〰️〰️〰️",
            "🔸🔸🔸🔸🔸🔸🔸🔸🔸🔸",
            "🕶🕶🕶🕶🕶🕶🕶🕶🕶🕶",
            "🚥🚥🚥🚥🚥🚥🚥🚥🚥🚥",
            "▪️▪️▪️▪️▪️▪️▪️▪️▪️▪️",
        );
        $sLine = array(

            "➖➖➖",
//            "➿➿➿",
            "🕳️🕳️🕳️",
            "〰️〰️〰️",
            "🔸🔸🔸",
            "🕶🕶🕶",
            "🚥🚥🚥",
            "▪️▪️▪️",
        );
        $idx = array_rand($line);
        $line = $line[$idx];
        $sLine = $sLine[$idx];

        $g = Group::where('id', $divar->group_id)->first();
        $caption = (" $g->emoji " . "#$g->name") . PHP_EOL;
        $caption .= /*PHP_EOL .*/
            "\xD8\x9C" . "$line" . PHP_EOL /*. PHP_EOL*/
        ;
        $caption .= "🌍 " . $divar->chat_title . PHP_EOL;
        $caption .= $address . PHP_EOL;
        $caption .= '👤Admin: ' . ($user->telegram_username != "" && $user->telegram_username != "@" ? "$user->telegram_username" :
                "[$user->name](tg://user?id=$user->telegram_id)") . PHP_EOL;
        $caption .= /*PHP_EOL .*/
            "$line" . PHP_EOL /*. PHP_EOL*/
        ;
        $caption .= "💬 " . (mb_strlen($divar->chat_description) < 150 ? $divar->chat_description : mb_substr($divar->chat_description, 0, 150)) . " ... " . PHP_EOL;
        $caption .= /*PHP_EOL .*/
            "\xD8\x9C" . "$line" . PHP_EOL /*. PHP_EOL*/
        ;
        if ($divar->follow_score > 0)
            $caption .= "✅جایزه عضویت: " . $divar->follow_score . PHP_EOL;
        if ($divar->ref_score > 0)
            $caption .= "🔗جایزه عضویابی: " . $divar->ref_score . PHP_EOL;
        $r = Helper::$remain_member_day_limit;
        if ($divar->follow_score > 0)
            $caption .= "⛔جریمه لفت دادن ($r روز): " . $divar->follow_score * 2 . PHP_EOL;
        $caption .= "$line" . PHP_EOL;
        $caption .= ($is_advertise ? $rLink : "$divar->chat_username") . PHP_EOL;
        $caption .= "$line" . PHP_EOL;


        $caption .= "💫ربات دیوار و تبادل شبانه💫" . PHP_EOL;
        $caption .= Helper::$bot . PHP_EOL;
        $caption .= PHP_EOL . "🅼🅰🅶🅽🅴🆃 🅶🆁🅰🅼" . PHP_EOL;

        $follow_score = Helper::$follow_score;

        if ($is_advertise)
            $cell_button = json_encode(['inline_keyboard' => [
                [['text' => "👈 ورود و عضویت (  $follow_score سکه ) 👉", 'url' => $rLink]],

            ], 'resize_keyboard' => true]);
        else
            $cell_button = json_encode(['inline_keyboard' => [
                [['text' => "👈 ورود و عضویت (  $follow_score سکه ) 👉", 'url' => "https://t.me/" . str_replace('@', '', $divar->chat_username)]],
                [['text' => "✅ عضو شدم (دریافت سکه) ✅", 'callback_data' => "divar_i_followed$$chat_id$$advertiser_id"]],

            ], 'resize_keyboard' => true]);


        $message = Helper::sendPhoto($send_to_id, asset("storage/chats/$chat_id.jpg"), self::MarkDown($caption), null, $cell_button);


        return true;
    }


}
