<?php

namespace App\Console\Commands;

use App\Http\Helpers\Helper;
use App\Models\Chat;
use App\Models\Divar;
use App\Models\Group;
use App\Models\Tab;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;
use PhpParser\Node\Expr\AssignOp\Div;

class TabGuard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tab:guard';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check bot is kicked or post was deleted in tab time';

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

        //removed bot from channel

        $tabs = Tab::whereNotIn('chat_username', [/*'@vartastudio', '@magnetgramwall', '@esteghlalwallpapers', '@perspoliswallpapers', '@boorsaman'*/])->inRandomOrder()->get();

        $blockMessage = "";
        foreach ($tabs as $tab) {
            $d = Divar::where('chat_id', "$tab->chat_id")->first();
            if (!$d || $d->validated == false) {

                Divar::where('chat_id', "$tab->chat_id")->update(['blocked' => true]);

                $u = User::where('id', $tab->user_id)->first();
//                if ($u)
//                    Helper::sendMessage($u->telegram_id, " ⛔️ " . "متاسفانه کانال شما به علت حذف ربات در بازه تبادل، بلاک شد" . PHP_EOL . $tab->chat_username, null);

                Helper::sendMessage(Helper::$logs[0], "Channel Blocked For Remove Bot " . $tab->chat_username, null);

                $blockMessage .= PHP_EOL . $tab->chat_username;

                $tab->delete();
            }

        }
        if ($blockMessage != "") {
            Helper::sendMessage(Helper::$divarChannel, "⛔️" . "کانال(های) زیر به علت حذف ربات در بازه تبادل بلاک شدند:" . $blockMessage, null);

        }

        //removed tab post from channel

        $tabs = Tab::whereNotIn('chat_username', [/*'@vartastudio', '@magnetgramwall', '@esteghlalwallpapers', '@perspoliswallpapers', '@boorsaman'*/])->inRandomOrder()->get();

        $blockMessage = "";
        foreach ($tabs as $tab) {

            $res = Helper::Forward(Helper::$logs[0], $tab->chat_id, $tab->message_id, $tab->chat_username, true);
            if (isset($res) && $res->ok == false) {
                Divar::where('chat_id', "$tab->chat_id")->update(['blocked' => true]);
                Chat::where('chat_id', "$tab->chat_id")->update(['active' => false]);

//                $u = User::where('id', $tab->user_id)->first();
//                if ($u)
//                    Helper::sendMessage($u->telegram_id, " 📛 " . "متاسفانه کانال شما به علت حذف  پست تبادل، بلاک شد" . PHP_EOL . $tab->chat_username, null);

                Helper::sendMessage(Helper::$logs[0], "Channel Blocked For Delete Tab Post " . $tab->chat_username, null);

                $blockMessage .= PHP_EOL . $tab->chat_username;

                $tab->delete();
            } elseif (isset($res) && $res->ok == true) {
                Helper::DeleteMessage(Helper::$logs[0], $res->result->message_id);
//                Helper::DeleteMessage($tab->chat_id, $tab->message_id);
//                $tab->message_id = $res->result->message_id;
//                $tab->save();

            }
//            else {
//                $res = Helper::DeleteMessage($tab->chat_id, $tab->message_id);
//            }

        }
        if ($blockMessage != "") {
            Helper::sendMessage(Helper::$divarChannel, " 📛 " . "کانال(های) زیر به علت حذف پست تبادل بلاک شدند:" . $blockMessage, null);

        }


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


}
