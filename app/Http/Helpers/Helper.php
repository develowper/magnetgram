<?php

//--------[Your Config]--------//
namespace App\Http\Helpers;

use App\Models\Chat;
use App\Models\Group;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class Helper
{

    const APP_VERSION = 1;
    const PRODUCTS = [
        ['name' => "100 عدد سکه", 'key' => "coin-100", 'price' => 5000,],
        ['name' => "300 عدد سکه", 'key' => "coin-300", 'price' => 10000,],
        ['name' => "500 عدد سکه", 'key' => "coin-500", 'price' => 15000,],
    ];
    static $market_link = [
        "playstore" => "https://play.google.com/store/apps/details?id=com.varta.magnetgram",
        'bazaar' => 'https://cafebazaar.ir/app/com.varta.magnetgram',
        'myket' => 'https://myket.ir/app/com.varta.magnetgram',
    ];
    static $lottery_score = 5;
    static $product_image_limit = 5;
    static $create_shop_score = 50;
    static $create_product_score = 5;
    static $add_needing_score = 1;
    static $remain_member_day_limit = 15;
    static $vip_limit = 20;
    static $tag_score = 5;
    static $tabListLimit = 10;
    static $test = true;
    static $Dev = [72534783, 871016407, 225594412]; // آیدی عددی ادمین را از بات @userinfobot بگیرید
    static $logs = [72534783, 225594412];
    static $init_score = 10;
    static $ref_score = 2;
    static $divar_show_items = 1000;
    static $see_video_score = 10;
    static $left_score = 10;
    static $follow_score = 5;
    static $add_score = 1;
    static $vip_count = 4;
    static $vip_score = 300;// 80;
    static $install_chat_score = 0;// 100;
    static $divar_scores = ['6' => 50, '12' => 100, '24' => 200]; //min
    static $bot = "@magnetgrambot";
    static $admin = "@develowper";
    static $divarChannel = "@magnetgramwall";
    static $bot_id = "1180050721";
    static $app_link = "https://play.google.com/store/apps/details?id=com.varta.magnetgram_simple";
    static $youtube_link = "https://www.youtube.com/channel/UCzwQ6GnoNQG1PwpqZhkIogA";
    static $channel = "@vartastudio"; // ربات را ادمین کانال کنید
    static $info = "\n\n*@magnetgrambot*\n\n\n👦[Admin 1](instagram.com/develowper)\n\n👱[Admin 2](tg://user?id=72534783)\n\n\n🅼🅰🅶🅽🅴🆃 🅶🆁🅰🅼\n  \n🏠 *@vartastudio*  \n📸 *instagram.com/vartastudio*";


//-----------------------------//

    static function sendMessage($chat_id, $text, $mode = null, $reply = null, $keyboard = null, $disable_notification = false, $app_id = null)
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

    static function sendPhoto($chat_id, $photo, $caption, $reply = null, $keyboard = null)
    {


        return Helper::creator('sendPhoto', [
            'chat_id' => $chat_id,
            'photo' => $photo,
            'caption' => $caption,
            'parse_mode' => 'Markdown',
            'reply_to_message_id' => $reply,
            'reply_markup' => $keyboard
        ]);

    }

    static function sendMediaGroup($chat_id, $media, $keyboard = null, $reply = null)
    {
//2 to 10 media can be send

        return Helper::creator('sendMediaGroup', [
            'chat_id' => $chat_id,
            'media' => json_encode($media),
            'reply_to_message_id' => $reply,

        ]);

    }

    static function creator($method, $datas = [], $token = null)
    {
        $url = "https://api.telegram.org/bot" . ($token ?? env('TELEGRAM_BOT_TOKEN', 'YOUR-BOT-TOKEN')) . "/" . $method;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
        $res = curl_exec($ch);

        if ($token) {
            return $res;
        }

        $res = json_decode($res);

        if ($res && $res->ok == false)
            Helper::sendMessage(Helper::$logs[0], /*"[" . $datas['chat_id'] . "](tg://user?id=" . $datas['chat_id'] . ") \n" .*/
                json_encode($method) . "\n" . json_encode($datas) . "\n" . $res->description, null, null, null);

//        Helper::sendMessage(Helper::$logs[0], ..$res->description, null, null, null);
        if (curl_error($ch)) {
            Helper::sendMessage(Helper::$logs[0], 'curl error' . PHP_EOL . json_encode(curl_error($ch)), null, null, null);
            var_dump(curl_error($ch));
            return null;
        } else {
            return $res;
        }
    }

    public
    static function addNeedToDivar($groupId, $time, $user, $text)
    {


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

        $g = Group::where('id', $groupId)->first();
        $txt = "🅼🅰🅶🅽🅴🆃 🅶🆁🅰🅼" . PHP_EOL . PHP_EOL;
        $txt .= (" $g->emoji " . "#$g->name") . " $sLine " . "⏳ $time ساعت" . PHP_EOL . PHP_EOL;
        $txt .= '👤  ' . ($user->telegram_username != "" && $user->telegram_username != "@" ? "$user->telegram_username" :
                "[$user->name](tg://user?id=$user->telegram_id)") . PHP_EOL;
        $txt .= "\xD8\x9C" . "$line" . PHP_EOL;

        $txt .= PHP_EOL . $text . PHP_EOL;
        $txt .= "\xD8\x9C" . "$line" . PHP_EOL;
        $txt .= Helper::$bot . PHP_EOL;

        $res = Helper::sendMessage(Helper::$divarChannel, Helper::MarkDown($txt), 'MarkDown', null, null);
        if (!$res || $res->result == false) {
            Helper::sendMessage(Helper::$logs[0], "خطای ثبت !" . PHP_EOL . " لطفا به ادمین گزارش دهید. " . Helper::$admin, null);
            Helper::sendMessage(Helper::$logs[0], "خطای ثبت نیازمندی" . PHP_EOL . $res->description, null);
            return false;
        }
        Need::create(['user_id' => $user->id, 'group_id' => $groupId, 'message_id' => $res->result->message_id, 'description' => $text, 'expire_time' => Carbon::now()->addHours($time)]);

        $res = Helper::sendMessage('@lamassaba', Helper::MarkDown($txt), 'MarkDown', null, null);

        return true;

    }

    public
    static function addChatToDivar($info, $time, $follow_score = 0, $ref_score = 0)
    {


        $chat_type = $info->type == 'channel' ? 'c' : ($info->type == 'group' || $info->type == 'supergroup' ? 'g' : ($info->type == 'bot' ? 'b' : null));

        $chat = Chat::where('chat_id', "$info->id")->first();

        if ($chat) {
            $timestamp = Helper::createChatImage($info->photo, $chat->chat_id);

            $chat->image = $timestamp;
            $chat->chat_username = "@$info->username";
            $chat->chat_title = $info->title;
            $chat->chat_description = $info->description;
            $chat->save();
        }
        $user = User::where('id', $chat->user_id)->first();
        $divar = Divar::create(['user_id' => $chat->user_id, 'chat_id' => "$info->id", 'chat_type' => $chat_type, 'image' => $chat->image, $chat_type, 'chat_username' => "@$info->username",
            'chat_title' => $info->title, 'chat_description' => $info->description, 'chat_main_color' => Helper::simple_color_thief(storage_path("app/public/chats/$info->id.jpg")),
            'expire_time' => Carbon::now()->addHours($time), 'start_time' => Carbon::now(),
            'group_id' => $chat->group_id, 'follow_score' => $follow_score, 'ref_score' => $ref_score,]);

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
        $sLine = array(
            "➖➖➖",
            "🕳️🕳️🕳️",
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

        $g = Group::where('id', $chat->group_id)->first();
        $caption = (" $g->emoji " . "#$g->name") . " $sLine " . "⏳ $time ساعت" . PHP_EOL;
        $caption .= /*PHP_EOL .*/
            "\xD8\x9C" . "$line" . PHP_EOL /*. PHP_EOL*/
        ;
        $caption .= "🌍 " . $info->title . PHP_EOL;
        $caption .= ("🔗 " . "@$info->username") . PHP_EOL;
        $caption .= '👤Admin: ' . ($user->telegram_username != "" && $user->telegram_username != "@" ? "$user->telegram_username" :
                "[$user->name](tg://user?id=$user->telegram_id)") . PHP_EOL;
        $caption .= /*PHP_EOL .*/
            "$line" . PHP_EOL /*. PHP_EOL*/
        ;
        $caption .= "💬 " . (mb_strlen($info->description) < 150 ? $info->description : mb_substr($info->description, 0, 150)) . " ... " . PHP_EOL;
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
        $caption .= /*PHP_EOL . */
            "$line" . PHP_EOL /*. PHP_EOL*/
        ;
        $caption .= "👔گروه تخصصی ادمین های تلگرام👔" . PHP_EOL;
        $caption .= '@magnetgram_admins' . PHP_EOL;
        $caption .= "💫ربات دیوار، فروشگاه و تبادل 💫" . PHP_EOL . PHP_EOL;
        $caption .= Helper::$bot . PHP_EOL;
        $caption .= PHP_EOL . "🅼🅰🅶🅽🅴🆃 🅶🆁🅰🅼" . PHP_EOL;


        $cell_button = json_encode(['inline_keyboard' => [

            $follow_score > 0 ? [['text' => "👈 ورود 👉", 'url' => "https://t.me/$info->username"],
                ['text' => "✅ عضو شدم(" . $divar->follow_score . "امتیاز)", 'callback_data' => "divar_i_followed$$info->id"]] : [],
            $ref_score > 0 ? [['text' => "🔗 بنر عضوگیری 🔗", 'callback_data' => "divar_i_advertise$$info->id"]] : [],
        ], 'resize_keyboard' => true]);


        $message = Helper::sendPhoto(Helper::$divarChannel, asset("storage/chats/$timestamp.jpg"), self::MarkDown($caption), null, $cell_button);

        if (!$message || $message->result == null) {

            Helper::sendMessage($user->telegram_id, "مشکلی در ثبت پیش امد.مطمئن شوید که کانال شما عکس پروفایل داشته باشد و اگر مجدد به مشکل خوردید به ادمین گزارش دهید\n" . Helper::$admin, 'MarkDown', null, null);
            Helper::sendMessage(Helper::$logs[0], $info->id . PHP_EOL . "@$info->username" . PHP_EOL . json_encode($message), null, null, null);
            $divar->delete();
//            if ($chat)
//                $chat->delete();
            return false;
        }
        $divar->message_id = $message->result->message_id;
        $divar->save();
        Chat::where('chat_id', "$info->id")->update(['chat_title' => $info->title,
            'chat_description' => $info->description, 'chat_username' => "@$info->username",
            'chat_main_color' => Helper::simple_color_thief(storage_path("app/public/chats/$timestamp.jpg")), 'chat_type' => $chat_type]);

        $message = Helper::sendPhoto("@lamassaba", asset("storage/chats/$timestamp.jpg"), self::MarkDown($caption), null, $cell_button);

        return true;
    }

    public
    static function addChatToTab($info, $first_name, $last_name)
    {
    }

    static
    function getChatMembersCount($chat_id)
    {

        $res = Helper::creator('getChatMembersCount', ['chat_id' => $chat_id]);
        if (isset($res) && $res->ok == true)
            return (int)$res->result; else return 0;
    }

    static
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

    static
    function DeleteMessage($chatid, $massege_id)
    {
        return Helper::creator('DeleteMessage', [
            'chat_id' => $chatid,
            'message_id' => $massege_id
        ]);
    }

    static
    function Forward($chatid, $from_id, $massege_id, $disable_notification = false)
    {
        return Helper::creator('forwardMessage', [
            'chat_id' => $chatid,
            'from_chat_id' => $from_id,
            'message_id' => $massege_id,
            'disable_notification' => $disable_notification,
        ]);
    }

    public
    static function createChatImage($photo, $chat_id)
    {

        if (!isset($photo) || !isset($photo->big_file_id)) return null;
//        $timestamp = Carbon::now()->timestamp;

        $client = new \GuzzleHttp\Client();
        $res = Helper::creator('getFile', [
            'file_id' => $photo->big_file_id,

        ])->result->file_path;


        $image = "https://api.telegram.org/file/bot" . env('TELEGRAM_BOT_TOKEN', 'YOUR-BOT-TOKEN') . "/" . $res;
        if (Storage::exists("public/chats/$chat_id.jpg")) {
            Storage::delete("public/chats/$chat_id.jpg");
        }
        Storage::put("public/chats/$chat_id.jpg", $client->get($image)->getBody());


        $img = Image::make(storage_path("app/public/chats/$chat_id.jpg"));
        $img2 = Image::make(storage_path("app/public/magnetgramcover.png"));
        $img2->resize($img->width(), $img->height());
        $img->insert($img2, 'center');
        $img->save(storage_path("app/public/chats/$chat_id.jpg"));

        return $chat_id;

    }

    public
    static function MarkDown($string)
    {
        $string = str_replace(["_",], '\_', $string);
        $string = str_replace(["`",], '\`', $string);
        $string = str_replace(["*",], '\*', $string);
        $string = str_replace(["~",], '\~', $string);


        return $string;
    }

    public
    static function logAdmins($msg)
    {
        foreach (Helper::$logs as $log)
            Helper::sendMessage($log, $msg, /*'MarkDown'*/
                null);

    }

    public static function sendSticker($chat_id, $file_id, $keyboard, $reply = null, $set_name = null)
    {
        return Helper::creator('sendSticker', [
            'chat_id' => $chat_id,
            'sticker' => $file_id,
            "set_name" => $set_name,
            'reply_to_message_id' => $reply,
            'reply_markup' => $keyboard
        ]);
    }

    public static function simple_color_thief($img, $default = null)
    {
        if (@exif_imagetype($img)) { // CHECK IF IT IS AN IMAGE
            $type = getimagesize($img)[2]; // GET TYPE
            if ($type === 1) { // GIF
                $image = imagecreatefromgif($img);
                // IF IMAGE IS TRANSPARENT (alpha=127) RETURN fff FOR WHITE
                if (imagecolorsforindex($image, imagecolorstotal($image) - 1)['alpha'] == 127) return 'fff';
            } else if ($type === 2) { // JPG
                $image = imagecreatefromjpeg($img);
            } else if ($type === 3) { // PNG
                $image = imagecreatefrompng($img);
                // IF IMAGE IS TRANSPARENT (alpha=127) RETURN fff FOR WHITE
                if ((imagecolorat($image, 0, 0) >> 24) & 0x7F === 127) return 'fff';
            } else { // NO CORRECT IMAGE TYPE (GIF, JPG or PNG)
                return $default;
            }
        } else { // NOT AN IMAGE
            return null;
        }
        $newImg = imagecreatetruecolor(1, 1); // FIND DOMINANT COLOR
        imagecopyresampled($newImg, $image, 0, 0, 0, 0, 1, 1, imagesx($image), imagesy($image));
        return dechex(imagecolorat($newImg, 0, 0)); // RETURN HEX COLOR
    }
}


function flash($title = null, $message = null)
{
//    session()->flash('flash_message', $message);
//    session()->flash('flash_message_level', $level);

    $flash = app('App\Http\Flash');

    if (func_num_args() == 0) { //  flash() is empty means flash()->success('title','message') and ...
        return $flash;
    }

    return $flash->info($title, $message); //means flash('title','message')

}

function w2e($str)
{
    $eastern = array('٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩');
    $western = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    return str_replace($western, $eastern, $str);
}

function textFancy($str)
{
    $src = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
    $src2 = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
    $dst = array('🅰', '🅱', '🅲', '🅳', '🅴', '🅵', '🅶', '🅷', '🅸', '🅹', '🅺', '🅻', '🅼', '🅽', '🅾', '🅿', '🆀', '🆁', '🆂', '🆃', '🆄', '🆅', '🆆', '🆇', '🆈', '🆉');

    return str_replace($src, $dst, str_replace($src2, $dst, $str));
}

function sort_banners_by($column, $body)
{
    $direction = (Request::get('direction') == 'ASC') ? 'DESC' : 'ASC';

    return '<a href=' . route('banners.index', ['sortBy' => $column, 'direction' => $direction]) . '>' . $body . '</a>';
}

if (!function_exists('validate_base64')) {

    /**
     * Validate a base64 content.
     *
     * @param string $base64data
     * @param array $allowedMime example ['png', 'jpg', 'jpeg']
     * @return bool
     */
    function validate_base64($base64data, array $allowedMime)
    {
        // strip out data uri scheme information (see RFC 2397)
        if (strpos($base64data, ';base64') !== false) {
            list(, $base64data) = explode(';', $base64data);
            list(, $base64data) = explode(',', $base64data);
        }

        // strict mode filters for non-base64 alphabet characters
        if (base64_decode($base64data, true) === false) {
            return false;
        }

        // decoding and then reeconding should not change the data
        if (base64_encode(base64_decode($base64data)) !== $base64data) {
            return false;
        }

        $binaryData = base64_decode($base64data);

        // temporarily store the decoded data on the filesystem to be able to pass it to the fileAdder
        $tmpFile = tempnam(sys_get_temp_dir(), 'medialibrary');
        file_put_contents($tmpFile, $binaryData);

        // guard Against Invalid MimeType
        $allowedMime = array_flatten($allowedMime);

        // no allowedMimeTypes, then any type would be ok
        if (empty($allowedMime)) {
            return true;
        }

        // Check the MimeTypes
        $validation = Illuminate\Support\Facades\Validator::make(
            ['file' => new Illuminate\Http\File($tmpFile)],
            ['file' => 'mimes:' . implode(',', $allowedMime)]
        );

        return !$validation->fails();
    }

}
function encrypt($str)
{
    return openssl_encrypt(
        $str,
        'AES-256-CBC',
        substr(env('API_KEY'), -32),
        0,
        substr(env('API_KEY'), -16),
    );
}


