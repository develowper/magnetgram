<?php

namespace App\Console\Commands;

use App\Http\Helpers\Helper;
use App\Models\Chat;
use App\Models\Divar;
use App\Models\Follower;
use App\Models\Need;
use App\Models\Queue;
use App\Models\User;
use Illuminate\Console\Command;
use Carbon\Carbon;

class UpdateDivar extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'divar:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'حذف گروه/کانال منقضی شده';

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
        //delete expired from divar
        //add from divar
//        return;
        foreach (Divar::whereNotNull('message_id')->get() as $d) {

            if (Carbon::parse($d->expire_time) >= Carbon::now()) {
//                Helper::sendMessage(Helper::$logs[0], $d->chat_username, null);
            } else {
                $u = User::where('id', $d->user_id)->first();

//                Helper::DeleteMessage(Helper::$divarChannel, $d->message_id);
                $d->message_id = null;
                $d->save();
//                $d->delete();
                $txt = "⏰" . PHP_EOL;
                $txt .= "زمان نمایش کانال $d->chat_username در دیوار به پایان رسید." . PHP_EOL;
                $txt .= "جهت ثبت مجدد، دکمه دیوار -> ثبت در دیوار را بزنید" . PHP_EOL;
                $txt .= "جهت ثبت در تبادل اتوماتیک، دکمه مدیریت کانال ها->انتخاب کانال->تب اتوماتیک را فعال کنید و ربات را ادمین کانال خود کنید" . PHP_EOL;

                $txt .= "💬 ادمین:" . PHP_EOL . Helper::$admin . PHP_EOL;
                $txt .= "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
                $txt .= "🅼🅰🅶🅽🅴🆃 🅶🆁🅰🅼" . PHP_EOL;
                $txt .= "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;

                Helper::sendMessage($u->telegram_id, $txt, null);
            }


        }

//        punish left members

        foreach (Follower::get() as $f) {
//            'telegram_id', 'chat_id', 'added_by', 'ref_score', 'follow_score', 'created_at'


            if (Carbon::now() > Carbon::parse($f->created_at)->addDays(Helper::$remain_member_day_limit)) {
//                Helper::sendMessage(Helper::$logs[0], Carbon::now() . PHP_EOL . Carbon::parse($f->created_at)->addDays(Helper::$remain_member_day_limit), null);

                $f->delete();
                continue;
            }

            if (!$this->isMember($f->telegram_id, $f->chat_id)) {
                $c = Chat::where('chat_id', "$f->chat_id")->first();
                if ($c) {
                    $chatUsername = $c->chat_username;
                    $ou = User::where('id', $c->user_id)->first();
                }

                $punish = $f->follow_score * 2;

                $u = User::where('telegram_id', "$f->telegram_id")->first();
                if ($u) {
                    $u->score = $u->score - $punish;
                    $username = $u->telegram_username;
                    $u->save();
                }

                if ($ou) {
                    $ou->score = $ou->score + $f->follow_score;

                    $ou->save();
                }


                Helper::sendMessage(Helper::$logs[0], "⛔  کاربر $username لفت دادن از کانال $chatUsername تعداد $punish  سکه جریمه شد .", null);
                Helper::sendMessage($f->telegram_id, "⛔ متاسفانه به علت لفت دادن از کانال $chatUsername تعداد $punish سکه جریمه شدید.", null);
                $f->delete();
            }

        }

//delete need from divar
        foreach (Need::get() as $need) {
            if (Carbon::parse($need->expire_time) < Carbon::now()) {
                Helper::DeleteMessage(Helper::$divarChannel, $need->message_id);
                $need->delete();
            }
        }


//        $current = Carbon::now();
//        $nums = Divar::where('expire_time', '<', $current)->delete();
//        $queue = Queue::take($nums)->get();
//        foreach ($queue as $item) {
//            Divar::create(['user_id' => $item->user_id,
//                'chat_id' => $item->chat_id,
//                'chat_type' => $item->chat_type,
//                'chat_username' => $item->chat_username,
//                'chat_title' => $item->chat_title,
//                'chat_description' => $item->chat_description,
//                'expire_time' => Carbon::now()->addMinutes($item->show_time),
//                'start_time' => $current]);
//
//            Helper::sendMessage(User::find($item->user_id)->telegram_id, "گروه/کانال $item->chat_username هم اکنون در دیوار قرار گرفت!", null);
//        }
//
    }


    private
    function isMember($user_id, $chat_id)
    {


        $res = Helper::creator('getChatMember', [
            'chat_id' => "$chat_id",
            'user_id' => "$user_id"
        ]);
//        Helper::sendMessage(Helper::$logs[0], json_encode($res), null);


        if (isset($res) && ($res->ok == false || $res->result->status != 'member'))
            return false;// $res->description;

        else return true;


    }
}
