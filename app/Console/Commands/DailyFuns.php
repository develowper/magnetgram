<?php

namespace App\Console\Commands;


use App\Http\Helpers\Helper;
use App\Models\Chat;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;

class DailyFuns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:messagesfun';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'daily messages to channels';

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


        foreach (Chat::where('active', true)->where('auto_fun', true)->get() as $channel) {
            $fun = DB::table('funs')->inRandomOrder()->first();
            $res = $this->sendFile("$channel->chat_id", $fun->msg, null, false);
//            Helper::sendMessage(Helper::$logs[0], json_encode($fun));
            if ($res && $res->ok == false) {
                Helper::sendMessage(Helper::$logs[0], $res->description);
                $channel->auto_fun = false;
                $channel->save();
            }
        }


    }

    private
    function sendFile($chat_id, $storage, $reply = null, $tag = true)
    {


        $message = json_decode($storage);
        $poll = isset($message->poll) ? $message->poll : null;
        $text = isset($message->text) ? $message->text : null;
        $sticker = isset($message->sticker) ? $message->sticker : null;  #width,height,emoji,set_name,is_animated,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,
        $animation = isset($message->animation) ? $message->animation : null;   #file_name,mime_type,width,height,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,

        $photo = isset($message->photo) ? $message->photo : null;  # file_id,file_unique_id,file_size,width,height] array of different photo sizes
        $document = isset($message->document) ? $message->document : null;  #file_name,mime_type,thumb[file_id,file_unique_id,file_size,width,height]
        $video = isset($message->video) ? $message->video : null; #duration,width,height,mime_type,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,height]
        $audio = isset($message->audio) ? $message->audio : null; #duration,mime_type,title,performer,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,height]
        $voice = isset($message->voice) ? $message->voice : null; #duration,mime_type,file_id,file_unique_id,file_size
        $video_note = isset($message->video_note) ? $message->video_note : null; #duration,length,file_id,file_unique_id,file_size,thumb[file_id,file_unique_id,file_size,width,height]
        $caption = isset($message->caption) ? $message->caption : "";
        $media = isset($message->media) ? $message->media : null;

//        $media = [];
//        if ($photo) {
//            foreach ($photo as $item) {
//                $media[] = ['type' => 'photo', 'media' => $item->file_id, 'caption' => $caption];
//
//            }
//        }


        if ($text && $tag) $text = $text . "\n\n" . Helper::$bot;
        else if ($caption && $tag) $caption = $caption . "\n\n" . Helper::$bot;
        $buttons = [[['text' => '👈 محل تبلیغ کانال و گروه شما 👉', 'url' => "https://t.me/" . str_replace("@", "", Helper::$bot)]]];


        $caption = Helper::MarkDown($caption);
        $keyboard = null;
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

}
