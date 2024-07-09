<?php

namespace App\Console\Commands;

use App\Http\Helpers\Helper;
use App\Models\Divar;
use App\Models\Tab;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;
use PhpParser\Node\Expr\AssignOp\Div;

class EndTabTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tab:end';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'End Tab Time (clear lists from channels)';

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
     * @return mixed
     */
    public function handle()
    {
// delete tabs from channels and table - set divar process to false (8 AM)
        $tabs = Tab::inRandomOrder()->get();
        $winner = $tabs->first();
        Divar::query()->update(['processed' => false]);
        if (count($tabs) == 0) return;

        foreach ($tabs as $tab) {

            $res = $this->DeleteMessage($tab->chat_id, $tab->message_id);

//            if (isset($res) && $res->ok == false) {
//                Divar::where('chat_id', "$tab->chat_id")->update(['blocked' => true]);
//                $u = User::where('id', $tab->user_id)->first();
//                if ($u)
//                    Helper::sendMessage($u->telegram_id, " ⛔️ " . "متاسفانه کانال شما به علت حذف پست تبادل، بلاک شد" . PHP_EOL . $tab->chat_username, null);
//                Helper::sendMessage(Helper::$logs[0], "Channel Blocked " . $tab->chat_username, null);
//            }

            $tab->delete();
        }
        Helper::sendMessage(Helper::$logs[0], "Tab Delete Success ! " . count($tabs), null);

        $user = User::where('id', $winner->user_id)->first();
        $user->score += Helper::$lottery_score;
        $user->save();

        $txt = "🔥💰🎭🥇🏆🎁🎈🎉🎀🎊" . PHP_EOL . PHP_EOL;
        $admin = ($user->telegram_username != "" && $user->telegram_username != "@" ? "$user->telegram_username" :
                "[$user->name](tg://user?id=$user->telegram_id)") . PHP_EOL;
        $txt .= "برنده قرعه کشی لیست تبادل مگنت گرامی ها: " . PHP_EOL . "🥇 کانال: " . $winner->chat_username . PHP_EOL . "🎭 کاربر: " . $admin . PHP_EOL . "💰 " . Helper::$lottery_score . " سکه به شما اضافه شد! می توانید برای جایزه عضویت در کانال خود از آن استفاده کنید! " . PHP_EOL;
        $txt .= PHP_EOL . "🔥💰🎭🥇🏆🎁🎈🎉🎀🎊" . PHP_EOL . Helper::$bot;
        Helper::sendMessage(Helper::$divarChannel, Helper::MarkDown($txt), 'markDown');
    }

    private
    function DeleteMessage($chatid, $massege_id)
    {
        return Helper::creator('DeleteMessage', [
            'chat_id' => $chatid,
            'message_id' => $massege_id
        ]);
    }

    private
    function botIsAdminAndHasPrivileges($chat_id)
    {


        $res = Helper::creator('getChatMember', [
            'chat_id' => $chat_id,
            'user_id' => Helper::$bot_id
        ]);
        if ($res->ok == false)
            return false;// $res->description;
        elseif ($res->result->status != "administrator" ||
            !$res->result->can_post_messages ||
            !$res->result->can_edit_messages ||
            !$res->result->can_delete_messages)
            return false;


    }
}
