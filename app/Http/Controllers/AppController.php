<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Helper;
use App\Models\Chat;
use App\Models\Divar;
use App\Models\Follower;
use App\Models\Group;
use App\Models\Queue;
use App\Models\Ref;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Expr\AssignOp\Div;
use function App\Http\Helpers\simple_color_thief;

class AppController extends Controller
{

    public function __construct()
    {
        error_reporting(1);
        set_time_limit(-1);
        header("HTTP/1.0 200 OK");
        date_default_timezone_set('Asia/Tehran');

    }


    protected function sendError(Request $request)
    {
        $message = $request->message;


//        $this->sendMessage(Helper::$logs[0], "■ Error!\n" . $request->header('User-Agent'), null, null, null);
        $this->sendMessage(Helper::$logs[0], "\n\n $message", null, null, null);

    }

    protected function testMode()
    {
        return ['test' => Helper::$test];

    }

    protected function getSettings(Request $request)
    {
        if ($request->test == true)
            return Helper::$test;
        return [
            'divar_scores' => Helper::$divar_scores,
            'vip_score' => Helper::$vip_score,
            'add_score' => Helper::$add_score,
            'follow_score' => Helper::$follow_score,
            'install_chat_score' => Helper::$install_chat_score,
            'see_video_score' => Helper::$see_video_score,
            'ref_score' => Helper::$ref_score,
            'groups' => Group::select('id', 'name')->get(),
            'keys' => [
                'bazaar' => env('BAZAAR_RSA'),
                'myket' => env('MYKET_RSA'),
            ],
            'adv' => [

                'type' => [
                    'standard' => 'admob',//
                    'native' => 'admob', // varta admob tapsell
                    'rewarded' => 'admob',
                    'interstitial' => 'admob',
                ],
                'keys' => [
                    'tapsell' => [
                        'key' => 'jbfiaenkccjigohfgoihbftqraklhnbemqngabmbbtrtlpkkpetbldbqjbckmaffmdsfij',
                        'standard' => '668d770f338f5f0c2e5a6d3c',
                        'native' => '668d7735338f5f0c2e5a6d3d',
                        'rewarded' => '668d776d42918a55dec5125a',
                        'interstitial' => '668d778f42918a55dec5125b',
                    ],

                    'adivery' => [
                        'key' => '29e11306-cc67-45d1-970b-89e871c81876',
                        'standard' => 'ce8a85e2-0da5-494f-befd-b832c26142bb',
                        'native' => 'f68470b5-2fd2-4650-ae2d-64223f7f2b50',
                        'rewarded' => 'f9ad5f0f-1738-4c3f-8d85-1dc6cf0b6607',
                        'interstitial' => 'b09e0503-289f-4725-a000-3b4b1b2999f9',
                        'open' => 'f7720eba-51f2-40a1-9a76-5afcaa6ef39b',
                    ],

                    'admob' => [
                        'key' => 'ca-app-pub-4161485899394281~1512777978',
                        'standard' => 'ca-app-pub-4161485899394281/5019135495',
                        'native' => 'ca-app-pub-4161485899394281/1515553492',
                        'rewarded' => 'ca-app-pub-4161485899394281/8421741184',
                        'interstitial' => 'ca-app-pub-4161485899394281/9888318799',
                        'open' => 'ca-app-pub-4161485899394281/3322910446',
                    ]


                ],
                'data' => [],


            ],
            'payment' => null,
            'hides' => [],
            'products' => Helper::PRODUCTS,

            'app_info' => [
                'version' => Helper::APP_VERSION,

                'links' => [
                    'app' => '',
                    'comments' => '',
                    'tutorial' => 'https://www.aparat.com/playlist/449893',
                    'site' => 'https://zil.ink/varta',
                    'telegram' => 'https://t.me/develowper',
                    'telegram_bot' => 'https://t.me/magnetgrambot',
                    'instagram' => 'https://instagram.com/develowper',
                    'eitaa' => 'https://eitaa.com/develowper',
                    'email' => 'moj2raj2@gmail.com',
                    'market' => [
                        'bazaar' => Helper::$market_link['bazaar'],
                        'myket' => Helper::$market_link['myket'],
                        'playstore' => Helper::$market_link['playstore'],
                        'bank' => Helper::$market_link['playstore'],
                    ]
                ],
                'questions' => [
                    [
                        'q' => 'کیف پول شارژ نمی شود',
                        'a' => 'به دلیل اختلال در سیستم بانکی ممکن است خرید شما با تاخیر انجام شود. لطفا پس از چند دقیقه بر روی دکمه "بروز رسانی" کیف پول که در قسمت "پروفایل" در کنار عدد کیف پول است بزنید تا بروز رسانی شود',
                    ],
                ],

            ],
            'marketing' => [
                'title' => '',
                'ref_message' => '',

            ],

        ];

    }

    protected function addToVip(Request $request)
    {
        $user = $request->user();
        $chatId = $request->chat_id;

        $chat = Chat::where("chat_id", $chatId)->where('user_id', $user->id)->first();
        if (!$chat)
            return response()->json(['status' => 'danger', 'message' => "CHAT_NOT_FOUND"]);
        $divar = Divar::where("chat_id", $chatId)->where('user_id', $user->id)->where('expire_time', '>=', Carbon::now())->first();

        if (!$divar)
            return response()->json(['status' => 'danger', 'message' => "ADD_TO_DIVAR_FIRST"]);
        if ($divar->is_vip)
            return response()->json(['status' => 'danger', 'message' => "IS_VIP_BEFORE"]);
        $divar->is_vip = true;
        $divar->save();
        $user->score -= (Helper::$vip_score);
        $user->save();
        $first_name = $user->name;
        $from_id = $user->telegram_id;
        $chat_username = '@' . $chat->chat_username;

        foreach (Helper::$logs as $log)
            $this->sendMessage($log, "■  کاربر [$first_name](tg://user?id=$from_id)  $chat_username را پین کرد  .", 'MarkDown', null, null);

        return response()->json(['status' => 'success', 'message' => "SUCCESS_PIN"]);


    }

    protected function getDivar(Request $request)
    {
        $name = $request->name;
        $group_id = $request->group_id;
        $type = $request->type;
        $paginate = $request->paginate ?? 24;
        $page = $request->page ?? 1;
        $sortBy = $request->sortBy ?? 'start_time';
        $direction = $request->direction ?? 'DESC';
        $query = Divar::query();
        $user = $request->user();
        if ($group_id)
            $query = $query->where('group_id', $group_id);
        if ($name)
            $query = $query->where('name', 'like', $name . '%');
        if ($request->exists('type') && $request->type !== null)
            $query = $query->where('is_vip', $type == 'vip');

        $query = $query->orderby('is_vip', 'DESC')->orderby($sortBy, $direction);
//            ->        select(['id', 'user_id', 'chat_id', 'chat_username', 'chat_type', 'chat_title', 'chat_description', 'chat_main_color', 'is_vip', 'expire_time']);

        return tap($query->paginate($paginate, ['*'], 'page', $page), function ($paginated) use ($user) {
            $following = Follower::where('telegram_id', $user->telegram_id)->where('left', false)->whereIn('chat_id', $paginated->getCollection()->pluck('chat_id'))->get();
            return $paginated->getCollection()->transform(

                function ($item) use ($following) {
                    if ($following->where('chat_id', $item->chat_id)->first())
                        $item->role = 'member';
                    return $item;
                }
            );
        });

        foreach ($divars as $d) {
            // $info = $this->getChatInfo(['chat_id' => "$d->chat_id"]);

//             $role = $this->getUserInChat(['chat_id' => $d->chat_id, 'user_id' => auth()->user()->telegram_id,]);
//             $role = $role ? isset($role->result) ? isset($role->result->status) ? $role->result->status : null : null : null;
            if (Follower::where('chat_id', $d->chat_id)->where('telegram_id', $user->telegram_id)->where('left', false)->exists())
                $d->role = 'member';
        }
        return $divars;
    }

    protected function newChat(Request $request)
    {
        $chat_username = "@" . str_replace("@", "", $request->chat_username);
        $group_id = $request->group_id;
        $user = $request->user();
        if ($user->score < Helper::$install_chat_score)
            return response()->json(['message' => 'LOW_SCORE', 'status' => 'danger']);
        if (Chat::where("chat_username", $chat_username)->exists())
            return response()->json(['message' => 'CHAT_EXISTS', 'status' => 'danger']);


        $role = $this->getUserInChat(['chat_id' => $chat_username, 'user_id' => Helper::$bot_id,]);
        if ($role != 'administrator' && $role != 'creator')
            return response()->json(['message' => 'BOT_NOT_ADMIN', 'status' => 'danger']);


        $role = $this->getUserInChat(['chat_id' => $chat_username, 'user_id' => $user->telegram_id,]);
        if ($role != 'creator' && $role != 'administrator')
            return response()->json(['message' => 'NOT_ADMIN_OR_CREATOR', 'status' => 'danger']);

        $info = $this->getChatInfo($chat_username);
        if (!$info)
            return response()->json(['message' => 'CHAT_NOT_FOUND', 'status' => 'danger']);

        if (Chat::where("chat_id", "$info->id")->exists())
            return response()->json(['message' => 'CHAT_EXISTS', 'status' => 'danger']);

//        if ($info->type == 'channel') {
//            $tmp = $user->channels;
//            array_push($tmp, $chat_username);
//            $user->channels = $tmp;
//        } else {
//            $tmp = $user->groups;
//            array_push($tmp, $chat_username);
//            $user->groups = $tmp;
//        }


        $user->score -= Helper::$install_chat_score;
        $user->save();

        Helper::createChatImage($info->photo, "$info->id");

        Chat::create([
            'user_id' => $user->id,
            'group_id' => $group_id ?? null,
            'user_telegram_id' => $user->telegram_id,
            'chat_id' => "$info->id",
            'chat_type' => $info->type == 'channel' ? 'c' : 'g',
            'chat_username' => '@' . $info->username,
            'chat_main_color' => Helper::simple_color_thief(storage_path("app/public/chats/$info->id.jpg")),
            'chat_title' => $info->title,
            'chat_description' => $info->description,
        ]);

        return response()->json(['message' => 'REGISTER_SUCCESS', 'status' => 'success']);


    }

    protected function deleteChat(Request $request)
    {
        $chat_id = $request->chat_id;
        $user = $request->user();

        $chat = Chat::where('chat_id', "$chat_id")->first();
        if ($chat && ($chat->user_id == $user->id || $user->role == 'ad')) {
            Storage::delete("public/chats/$chat_id.jpg");
            $chat->delete();
            Divar::where('chat_id', "$chat_id")->delete();
            QUEUE::where('chat_id', "$chat_id")->delete();

            return ['res' => 'DELETE_SUCCESS'];
        }
        return ['res' => 'DELETE_FAILED'];


    }

    protected function addToDivar(Request $request)
    {
        $chat_id = $request->chat_id;
        $time = $request->time;
        $vip = $request->is_vip ? Helper::$vip_score : 0;
        $agree_queue = $request->agree_queue;
        // return $agree_queue;
        $user = $request->user();
//check time is valid
        if (!in_array($time, array_keys(Helper::$divar_scores)))
            return null;

        if ($vip > 0 && !$agree_queue && Divar::where('is_vip', true)->where('expire_time', '>=', Carbon::now())->count() >= Helper::$vip_count) {
            return ['res' => "VIP_FULL"];
        }

        if ($user->score < Helper::$divar_scores[$time] + $vip)
            return ['res' => "LOW_SCORE"];


        $chat = Chat::where('chat_id', $chat_id)->first();
        $role = $this->getUserInChat(['chat_id' => $chat_id, 'user_id' => $user->telegram_id]);

        if (($role != 'administrator' && $role != 'creator') || !$chat)
            return ['res' => "NOT_ADMIN"];

        if (in_array($chat_id, Divar::where('expire_time', '>=', Carbon::now())->pluck('chat_id')->toArray())) {
            return ['res' => "EXISTS_IN_DIVAR"];
        }
        if (in_array($chat_id, Queue::pluck('chat_id')->toArray())) {
            return ['res' => "EXISTS_IN_QUEUE"];
        }
        $first_name = $user->name;
        $from_id = $user->telegram_id;
        $chat_username = '@' . str_replace('@', '', $chat->chat_username);


        if (Divar::count() < Helper::$divar_show_items) {
            $d = Divar::create(['user_id' => $user->id, 'chat_id' => "$chat_id", 'chat_type' => $chat->chat_type, 'chat_username' => $chat_username, 'image' => $chat->image,
                'chat_title' => $chat->chat_title, 'chat_description' => $chat->chat_description, 'chat_main_color' => $chat->chat_main_color, 'is_vip' => $vip > 0 ? true : false, 'expire_time' => Carbon::now()->addHours($time), 'start_time' => Carbon::now()]);


            foreach (Helper::$logs as $log)
                $this->sendMessage($log, "■  کاربر [$first_name](tg://user?id=$from_id)  $chat_username را وارد دیوار کرد  .", 'MarkDown', null, null);

            $ref = Ref::where('new_telegram_id', $from_id)->first();
            if ($ref) {
                $u = User::where('telegram_id', $ref->invited_by)->first();
                if ($u) {
                    $ref_score = Helper::$ref_score;
                    $u->score += $ref_score;
                    $u->save();
                    $this->sendMessage($ref->invited_by, "■  کاربر [$first_name](tg://user?id=$from_id)  را وارد دیوار کرد و $ref_score سکه به شما اضافه شد! $chat_username .", 'MarkDown', null, null);
                }
            }

            $user->score -= (Helper::$divar_scores[$time] + $vip);
            $user->save();

            return ['res' => 'SUCCESS_DIVAR', 'score' => $user->score, 'is_vip' => $d->is_vip, 'expire_time' => $d->expire_time];
        } else {
            if ($agree_queue) {

                $q = Queue::create(['user_id' => $user->id, 'chat_id' => "$chat_id", 'chat_type' => $chat->chat_type, 'chat_username' => $chat->chat_username, 'image' => $chat->image,
                    'chat_title' => $chat->chat_title, 'chat_description' => $chat->chat_description, 'chat_main_color' => $chat->chat_main_color, 'is_vip' => $vip > 0 ? true : false, 'show_time' => $time]);
                $user->score -= (Helper::$divar_scores[$time] + $vip);
                $user->save();
                return ['res' => 'SUCCESS_QUEUE', 'score' => $user->score, 'is_vip' => $q->is_vip];
            } else
                return ['res' => 'AGREE_QUEUE'];
        }


    }

    protected function getUserChats(Request $request)
    {
        $what = $request->what;
        $search = $request->search;
        $user = $request->user();

        if (!$what) {
            $chat = Chat::query()->where('user_id', $user->id);
            if ($search)
                $chat->where(function ($query) use ($search) {
                    $query->orWhere('chat_title', 'like', "%$search%")
                        ->orWhere('chat_username', 'like', "%$search%");
                });

            $chats = $chat->get();
            foreach ($chats as $chat) {
                $d = Divar::where('chat_id', $chat->chat_id)->where('expire_time', '>=', Carbon::now())->first();

                $chat->expire_time = -1;
                $chat->in = null;
                $chat->is_vip = false;
                if ($d) {
                    $chat->in = 'd';
                    $chat->expire_time = $d->expire_time;
                    $chat->is_vip = $d->is_vip;
                    if ($d->is_vip)
                        continue;
                }
                $q = Queue::where('chat_id', $chat->chat_id)->first();
                if ($q) {
                    $chat->in = 'q';
                    $chat->is_vip = $q->is_vip;
                }
            }
            return response()->json(['data' => $chats, 'total' => count($chats ?? [])]);
        }
    }

    protected function refreshChat(Request $request)
    {

        $group_id = $request->group_id;
        $chat_id = $request->chat_id;
        $chat = null;
        if ($chat_id) {
            $chat = Chat::where('chat_id', $chat_id)->first();

            if ($chat) {
                $info = $this->getChatInfo($chat_id);
                if ($info) {
                    Helper::createChatImage($info->photo, "$info->id");
                    $chat->chat_main_color = Helper::simple_color_thief(storage_path("app/public/chats/$chat_id.jpg"));
                    $chat->chat_username = "@$info->username";
                    $chat->chat_title = $info->title;
                    $chat->chat_description = $info->description;
                    $chat->group_id = $group_id;
                    $chat->save();
                    $d = Divar::where('chat_id', $chat->chat_id)->where('expire_time', '>=', Carbon::now())->first();
                    $chat->expire_time = -1;
                    if ($d) {
                        $d->chat_username = $chat->chat_username;
                        $d->chat_title = $chat->chat_title;
                        $d->chat_description = $chat->chat_description;
                        $d->group_id = $group_id;
                        $d->save();
                        $chat->in = 'd';
                        $chat->expire_time = $d->expire_time;
                        $chat->is_vip = $d->is_vip;
                        return $chat;
                    }

                    $q = Queue::where('chat_id', $chat->chat_id)->first();
                    if ($q) {
                        $chat->in = 'q';
                        $chat->is_vip = $q->is_vip;
                        $chat->group_id = $group_id;
                        return $chat;
                    }
                }

            }

        }
        return $chat;
    }

    protected
    function viewChat(Request $request)
    {
        $chat_id = $request->chat_id;
        $user = $request->user();
        $item = Divar::where('chat_id', $chat_id)->first();

        if ($item && $item->expire_time < Carbon::now()->timestamp) {
            // $item->delete();
            return response()->json(['status' => 'danger', 'message' => "TIMEOUT_CHAT"], 200);
        }

        $role = $this->getUserInChat(['chat_id' => $chat_id, 'user_id' => $user->telegram_id,]);

        //   return json_encode($role);
        if ($role == 'member' || $role == 'administrator' || $role == 'creator' || $role == 'left' || $role == 'kicked')
            return response()->json(['status' => 'success', 'message' => "VIEW"], 200);
        else if (strpos($role, "telegram") !== false)
            return response()->json(['status' => 'danger', 'message' => "TELEGRAM_ERROR"], 200);
        else if (strpos($role, "kicked") !== false || strpos($role, "chat not") !== false || strpos($role, "user not") !== false)
            return response()->json(['status' => 'danger', 'message' => "BOT_NOT_ADDED"], 200);
        else
            return response()->json(['status' => 'danger', 'message' => $role], 200);

    }

    protected
    function getUser(Request $request)
    {

        if ($request->for == 'me')
            return [auth()->user()->only(['id', 'name', 'telegram_username', 'telegram_id', 'role', 'channels', 'groups', 'score'])];
        if ($request->for == 'score')
            return ['score' => auth()->user()->score];
//        elseif (in_array(auth()->user()->telegram_id, Helper::$Dev))
//            return User::whereIn('id', $request->ids)->get();

        $user = $request->user();
        $user->status = 'success';
        return $user;

    }

    protected
    function getChatInfo($chat_id)
    {
        $res = $this->creator('getChat', [
            'chat_id' => $chat_id,

        ]);
        if (isset($res->result))
            return $res->result;
        else return null;
    }

    protected
    function getUserInChat($request)
    {
        $role = $this->creator('getChatMember', [
            'chat_id' => $request['chat_id'],
            'user_id' => $request['user_id']
        ]);
        $role = $role ? isset($role->result) ? isset($role->result->status) ? $role->result->status : $role->description : $role->description : null;
        return $role;
    }

    protected
    function checkUserJoined(Request $request)
    {
        $chat_id = $request->chat_id;
        $chat_username = $request->chat_username;
        $last_score = $request->last_score;
        $isChannel = $request->chat_type == 'c' ? true : false;
        $user = $request->user();
        if (!Divar::where('chat_id', $chat_id)->where('expire_time', '>=', Carbon::now())->exists()) {
            return response()->json(['status' => 'danger', 'message' => "TIMEOUT_CHAT"], 200);
        }
        $res = $this->getUserInChat(['chat_id' => $chat_id, 'user_id' => $user->telegram_id,]);

        $f = Follower::where('telegram_id', $user->telegram_id)->where('chat_id', $chat_id)->first();
        if ($res == 'member') {
            if ($isChannel) {
                if (!$f) {
                    Follower::create(['chat_id' => $chat_id, 'chat_username' => $chat_username,
                        'telegram_id' => $user->telegram_id, 'follow_score' => Helper::$follow_score, 'user_id' => $user->id]);
                    $user->score += Helper::$follow_score;
                    $user->save();
                    return response()->json(['status' => 'success', 'message' => "MEMBER"], 200);
                } else {
                    //left or before register
                    return response()->json(['status' => 'danger', 'message' => "REPEATED_ADD"], 200);

                }
            } else { // group or supergroup
                if ($f && $f->left)
                    return response()->json(['status' => 'danger', 'message' => "REPEATED_ADD"], 200);
                else {
                    if ($user->score > $last_score) { // app not updated
                        return response()->json(['status' => 'success', 'message' => "MEMBER"], 200);
                    } else {
                        if (!$f)
                            Follower::create(['chat_id' => $chat_id, 'chat_username' => $chat_username,
                                'telegram_id' => $user->telegram_id, 'user_id' => $user->id]);
                        return response()->json(['status' => 'danger', 'message' => null], 200);

                    }
                }
            }

        } elseif ($res == 'creator' || $res == 'administrator') {
            if (!$f)
                Follower::create(['chat_id' => $chat_id, 'chat_username' => $chat_username,
                    'telegram_id' => $user->telegram_id, 'user_id' => $user->id]);
            return response()->json(['status' => 'danger', 'message' => "ADMIN_OR_CREATOR"], 200);


        } else if (strpos($res, "telegram") !== false)
            return response()->json(['status' => 'danger', 'message' => "TELEGRAM_ERROR"], 200);

        else {
            return response()->json(['status' => 'danger', 'message' => "BOT_NOT_ADDED_OR_NOT_EXISTS"], 200);

        }

    }

    protected
    function updateScore(Request $request)
    {
//        $user = User::where("id", $request->id)->first();
        $user = auth()->user();
        if ($user)
            switch ($request->command) {
                case  'install_chat':
                    $user->score += Helper::$install_chat_score;
                    $user->save();
                    return $user->score;
                    break;
                case  'follow_chat':
                    $user->score += Helper::$follow_score;
                    $user->save();
                    return $user->score;

                    break;
                case  'see_video':
                    $user->score += Helper::$see_video_score;
                    $user->save();
                    return $user->score;
                    break;

            }
    }

    protected
    function leftUsersPenalty(Request $request)
    {

        $user = $request->user();
        if (in_array($user->telegram_id, Helper::$Dev)) {

            $user_chats = Chat::pluck('chat_id')->toArray();

        } else
            $user_chats = Chat::where('user_id', $user->id)->pluck('chat_id')->toArray();
        $left = 0;

        foreach (Follower::whereIntegerInRaw('chat_id', $user_chats)->where('left', false)->get() as $f) {
            $vip = $f->in_vip ? 2 : 1;

            if ($f->added_by) {
                $penalty_user = User::where('telegram_id', $f->added_by)->first();
                $left_score = Helper::$add_score * $vip;
            } else {
                $penalty_user = User::where('telegram_id', $f->telegram_id)->first();
                $left_score = Helper::$follow_score * $vip;
            }

            $role = $this->getUserInChat(['chat_id' => $f->chat_id, 'user_id' => $f->telegram_id]);
            usleep(rand(500, 1000));
            if ($role != 'member' && $role != 'creator' && $role != 'administrator') {

                if ($penalty_user) {
                    $left++;
                    $penalty_user->score -= $left_score;
                    $penalty_user->save();
                    if ($f->added_by)
                        $this->sendMessage($penalty_user->telegram_id, "🚨 متاسفانه به علت خروج ممبر اد شده توسط شما از  " . "$f->chat_username" . " تعداد " . " $left_score " . " سکه جریمه شدید ", 'MarkDown', null);

                    else
                        $this->sendMessage($penalty_user->telegram_id, "🚨 متاسفانه به علت خروج از  " . "$f->chat_username" . " تعداد " . " $left_score " . " سکه جریمه شدید ", 'MarkDown', null);
                }

                $f->left = true;
                $f->save();
            }

        }


        return response()->json(['left' => $left, 'status' => 'success']);
    }


    function sendMessage($chat_id, $text, $mode, $reply = null, $keyboard = null, $disable_notification = false)
    {
        return $this->creator('sendMessage', [
            'chat_id' => $chat_id,
            'text' => $text,
            'parse_mode' => $mode,
            'reply_to_message_id' => $reply,
            'reply_markup' => $keyboard,
            'disable_notification' => $disable_notification,
        ]);
    }

    private
    function creator($method, $datas = [])
    {
        $url = "https://api.telegram.org/bot" . env('TELEGRAM_BOT_TOKEN', 'YOUR-BOT-TOKEN') . "/" . $method;

        $res = Http::asForm()->post($url, $datas);
        if ($res->status() != 200)
            self::sendMessage(Helper::$logs[0], $res->body() . PHP_EOL . print_r($datas, true), null);
        return json_decode($res->body());


    }


    function simple_color_thief($img, $default = null)
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
