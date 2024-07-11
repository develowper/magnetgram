<?php

namespace App\Console\Commands;

use App\Http\Helpers\Helper;
use App\Models\Tab;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:messages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send 150 messages every 5 minutes (telegeram not allow >1 min connection)';

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
        $c = DB::table('queue')
            ->count();
        if ($c > 0) {
            Helper::sendMessage(Helper::$logs[0], "sendMessages $c", null, null, null);
            foreach (DB::table('queue')/*->take(150)*/
            ->get() as $item) {
                if ($item->message_id != null && $item->from_id != null) {
                    $res = Helper::Forward($item->id, $item->from_id, $item->message_id);
                    if ($res->ok == false)
                        User::where('telegram_id', $item->id)->update(['active' => 0]);
                    if ($item->for && $item->for == 't' && $res->ok && isset($res->result)) {
                        Tab::where('chat_id', $item->id)->update(['message_id' => $res->result->message_id]);
                    }
                } elseif ($item->file != null)
                    $this->sendFile($item->id, $item->file, null, $item->app_id);
                DB::table('queue')->where('i', $item->i)->delete();
            }
        }
    }

    private
    function sendFile($chat_id, $storage, $reply = null, $app_id = null)
    {


        $message = json_decode($storage);
//        $message_id = $message->message_id;
//        $from_chat_id = $message->chat->id;
        $poll = isset($message->poll) ? $message->poll : null;
        $text = isset($message->text) ? $message->text : null;
        $sticker = isset($message->sticker) ? $message->sticker : null;  #width,height,emoji,set_name,is_animated,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,
        $animation = isset($message->animation) ? $message->animation : null;   #file_name,mime_type,width,height,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,

        $photo = isset($message->photo) ? $message->photo : null;  #file_id,file_unique_id,file_size,width,height] array of different photo sizes
        $document = isset($message->document) ? $message->document : null;  #file_name,mime_type,thumb[file_id,file_unique_id,file_size,width,height]
        $video = isset($message->video) ? $message->video : null; #duration,width,height,mime_type,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,height]
        $audio = isset($message->audio) ? $message->audio : null; #duration,mime_type,title,performer,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,height]
        $voice = isset($message->voice) ? $message->voice : null; #duration,mime_type,file_id,file_unique_id,file_size
        $video_note = isset($message->video_note) ? $message->video_note : null; #duration,length,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,height]
        $caption = (isset($message->caption) ? $message->caption : "") . "\n" . "📣" . Helper::$channel . "\n" . "👦" . Helper::$admin_username;
        $media = isset($message->media) ? $message->media : null;

        if ($text) {
            $adv_section = explode('banner=', $text); //banner=name=@id
            $text = $adv_section[0] . "\n" . "📣" . Helper::$channel . "\n" . "👦" . Helper::$admin_username;
        } else if ($caption) {
            $adv_section = explode('banner=', $caption);
            $caption = $adv_section[0] . "\n" . "📣" . Helper::$channel . "\n" . "👦" . Helper::$admin_username;
        }
        if (count($adv_section) == 2) {

            $link = explode('=', $adv_section[1]);
            $trueLink = $link[1];
            foreach ($link as $idx => $li) {
                if ($idx > 1)
                    $trueLink .= ('=' . $li);
            }
            $buttons = [[['text' => "👈 $link[0] 👉", 'url' => $trueLink]],
                $app_id == 1 ? [['text' => '🔵 کانال ارتش استقلال 🔵', 'url' => "https://t.me/esteghlalwallpapers"]] : [],
                $app_id == 2 ? [['text' => '🔴 کانال ارتش پرسپولیس 🔴', 'url' => "https://t.me/perspoliswallpapers"]] : [],
                [['text' => '👦 پشتیبانی 👦', 'url' => "https://t.me/develowper"]],
            ];
        } else {
//            if ($text) $text = $text ;  //. "\n\n" . $this->bot;
//            else if ($caption) $caption = $caption . "\n\n" . $this->bot;
            $buttons = [
                $app_id == 1 ? [['text' => '🔵 کانال ارتش استقلال 🔵', 'url' => "https://t.me/esteghlalwallpapers"]] : [],
                $app_id == 2 ? [['text' => '🔴 کانال ارتش پرسپولیس 🔴', 'url' => "https://t.me/perspoliswallpapers"]] : [],
                [['text' => '👦 پشتیبانی 👦', 'url' => "https://t.me/develowper"]],
            ];
        }
        $keyboard = json_encode(['inline_keyboard' => $buttons, 'resize_keyboard' => true]);

        if ($text)
            Helper::creator('SendMessage', [
                'chat_id' => $chat_id,
                'text' => $text, //. "\n" . "📣" . Helper::$channel . "\n" . "👦" . Helper::$admin_username, //. "\n $this->bot",
                'parse_mode' => null,
                'reply_to_message_id' => $reply,
                'reply_markup' => $keyboard
            ], $app_id);
        else if ($photo)
            Helper::creator('sendPhoto', [
                'chat_id' => $chat_id,
                'photo' => $photo[count($photo) - 1]->file_id,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
                'reply_to_message_id' => $reply,
                'reply_markup' => $keyboard
            ], $app_id);
        else if ($audio)
            Helper::creator('sendAudio', [
                'chat_id' => $chat_id,
                'audio' => $audio->file_id,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
                'duration' => $audio->duration,
                'performer' => $audio->performer,
                'title' => $audio->title,
                'thumb' => $audio->thumb,
                'reply_to_message_id' => $reply,
                'reply_markup' => $keyboard
            ], $app_id);
        else if ($document)
            Helper::creator('sendDocument', [
                'chat_id' => $chat_id,
                'document' => $document->file_id,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
                'thumb' => $document->thumb,
                'reply_to_message_id' => $reply,
                'reply_markup' => $keyboard
            ], $app_id);
        else if ($video)
            Helper::creator('sendVideo', [
                'chat_id' => $chat_id,
                'video' => $video->file_id,
                'duration' => $video->duration,
                'width' => $video->width,
                'height' => $video->height,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
                'thumb' => $video->thumb,
                'reply_to_message_id' => $reply,
                'reply_markup' => $keyboard
            ], $app_id);
        else if ($animation)
            Helper::creator('sendAnimation', [
                'chat_id' => $chat_id,
                'animation' => $animation->file_id,
                'duration' => $animation->duration,
                'width' => $animation->width,
                'height' => $animation->height,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
                'thumb' => $animation->thumb,
                'reply_to_message_id' => $reply,
                'reply_markup' => $keyboard
            ], $app_id);
        else if ($voice)
            Helper::creator('sendVoice', [
                'chat_id' => $chat_id,
                'voice' => $voice->file_id,
                'duration' => $voice->duration,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
                'reply_to_message_id' => $reply,
                'reply_markup' => $keyboard
            ], $app_id);
        else if ($video_note)
            Helper::creator('sendVideoNote', [
                'chat_id' => $chat_id,
                'video_note' => $video_note->file_id,
                'duration' => $video_note->duration,
                'length' => $video_note->length,
                'thumb' => $video_note->thumb,
                'caption' => $caption,
                'parse_mode' => 'Markdown',
                'reply_to_message_id' => $reply,
                'reply_markup' => $keyboard
            ], $app_id);
        else if ($sticker)
            Helper::creator('sendSticker', [
                'chat_id' => $chat_id,
                'sticker' => $sticker->file_id,
                "set_name" => "DaisyRomashka",
                'reply_to_message_id' => $reply,
                'reply_markup' => $keyboard
            ], $app_id);
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
                'reply_to_message_id' => $reply,
                'reply_markup' => $keyboard
            ], $app_id);

        else if ($media)
            Helper::creator('sendMediaGroup', [
                'chat_id' => $chat_id,
                'media' => $media/*[count($photo) - 1]->file_id*/,


                'reply_to_message_id' => $reply,
                'reply_markup' => $keyboard
            ], $app_id);
    }
}
