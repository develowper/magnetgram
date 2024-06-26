<?php

namespace App\Console\Commands;


use App\Http\Helper;
use App\Models\Chat;
use App\Models\Image;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Morilog\Jalali\Jalalian;

class DailyProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:productsdaily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'daily products  to channels';

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
        $now = Jalalian::forge('now');
        $time = $now->format('%A, %d %B %Y ⏰ H:i');

        if ($now->getHour() == 8 || $now->getHour() == 20) { //a random gallery

            foreach (Shop::get() as $shop) {
                $channel = Chat::where('chat_id', "$shop->channel_address")->where('active', true)->first();
                if (!$channel)
                    continue;

                $sh = array('❤️', '🧡', '💛', '💚', '💙', '💜', '🖤', '🤎');
                $tags = '';

                foreach (Product::where('shop_id', $shop->id)->inRandomOrder()->get() as $p) {

                    $p = explode("\n", $p->tags);
                    $p = $p[array_rand($p, 1)];
                    if ($p)
                        $tags .= $sh[array_rand($sh, 1)] . $p . PHP_EOL;
                    if (strlen($tags) >= 150) break;
                }


                $tag = ($channel->tag) ?? "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL . $channel->chat_username;

                $txt = "";
                $sen = ['🖐دنبال چی میگردی جوون دل 🙅؟ روی گزینه دلخواهت کلیک کن تا برات نمایش داده بشه',
                    'سلام جوون دل❤️. اگه  دنبال یه هدیه شیک میگردی پیشنهادای من رو هم یه نگاه بکن🤷! ',
                    'سر کیفی عزیز😇. این محصولات خاص برای آدمای خاص مثله توعه 😍!',
                ];
                $txt .= $sen[array_rand($sen, 1)] . PHP_EOL . PHP_EOL;
                $txt .= "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
                $txt .= $tags . PHP_EOL;
                $txt .= "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
                $txt .= $time . PHP_EOL;
                $txt .= $tag . PHP_EOL;

                $images = [];
                foreach (Image::whereIn('for_id', Product::where('shop_id', $shop->id)->pluck('id'))->inRandomOrder()->take(10)->get() as $item) {
                    $images[] = ['type' => 'photo', 'media' => "https://qr-image-creator.com/magnetgram/storage/products/$item->id.jpg"];
                }
                $images[0]['caption'] = $txt;


//                Helper::sendMediaGroup(Helper::$logs[0], $images);
                Helper::sendMediaGroup('@lamassaba', $images);
                Helper::sendMediaGroup($shop->channel_address, $images);
//                Helper::sendMediaGroup('@magnetgramwall', $images);
            }
        } else { //a random banner

            foreach (Shop::get() as $shop) {
                $channel = Chat::where('chat_id', "$shop->channel_address")->where('active', true)->first();
                if (!$channel)
                    continue;
                $tag = ($channel->tag) ?? "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL . $channel->chat_username;
                $product = Product::where('shop_id', $shop->id)->inRandomOrder()->first();
                if (!$product) continue;
                $caption = ($product->discount_price && $product->discount_price > 0 ? "🔥 #حراج" : "") . PHP_EOL;
                $caption .= ' 🆔 ' . "کد محصول: #" . $product->id . PHP_EOL;
                $caption .= ' 🔻 ' . "نام: " . $product->name . PHP_EOL;
//                    $caption .= ' ▪️ ' . "تعداد موجود: " . $product->count . PHP_EOL;
                $caption .= ' 🔸 ' . "قیمت: " . ($product->price == 0 ? 'پیام دهید' : strrev(str_replace('000', '000,', strrev($product->price))) . ' ت ') . PHP_EOL;
                if ($product->discount_price > 0)
                    $caption .= ' 🔹 ' . "قیمت حراج: " . strrev(str_replace('000', '000,', strrev($product->discount_price))) . ' ت ' . PHP_EOL;
                $caption .= ' 🔻 ' . "توضیحات: " . PHP_EOL . "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL . $product->description . PHP_EOL . "\xD8\x9C" . "➖➖➖➖➖➖➖➖➖➖➖" . PHP_EOL;
                $caption .= $product->tags . PHP_EOL;
                $caption .= $tag . PHP_EOL;
                $caption = Helper::MarkDown($caption);

                $images = Image::where('type', 'p')->where('for_id', $product->id)->get();
                if (count($images) == 0) {
                    Helper::sendPhoto($channel->chat_username, asset("https://qr-image-creator.com/magnetgram/storage/chats/$channel->image.jpg"), $caption, null, null);
                } elseif (count($images) == 1) {
                    Helper::sendPhoto($channel->chat_username, "https://qr-image-creator.com/magnetgram/storage/products/" . $images[0]['id'] . ".jpg", $caption, null, null);
                } else {
                    foreach ($images as $idx => $item) {
                        if ($idx == 0)
                            $images[$idx] = ['type' => 'photo', 'media' => "https://qr-image-creator.com/magnetgram/storage/products/$item->id.jpg", 'parse_mode' => 'Markdown', 'caption' => $caption];
                        else
                            $images[$idx] = ['type' => 'photo', 'media' => "https://qr-image-creator.com/magnetgram/storage/products/$item->id.jpg", 'parse_mode' => 'Markdown',];
                    }
                    Helper::sendMediaGroup('@lamassaba', $images);
                    Helper::sendMediaGroup($channel->chat_username, $images);
                }
            }


        }
    }


}
