<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Http\Helpers\Helper;
use App\Http\Helpers\Pay;
use App\Http\Helpers\Telegram;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Payment;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Mockery\Exception;
use PHPUnit\TextUI\Help;

class PaymentController extends Controller
{
    private $bazaar_client_id;
    private $bazaar_client_secret;
    private $myket_access_token;

    public function __construct()
    {
        $this->bazaar_client_id = env('BAZAAR_CLIENT_ID');
        $this->bazaar_client_secret = env('BAZAAR_CLIENT_SECRET');

        $this->myket_access_token = env('MYKET_ACCESS_TOKEN');
    }

    protected function create(Request $request)
    {
        $user = auth()->user();
        $type = $request->type;
        $days = $request->days;
        $price = $request->amount;
        $package = $request->package;
        $sku = $request->product_id;
        $title = $request->title;
        $order_id = Carbon::now()->getTimestampMs();
        $market = $request->market;
        $appVersion = $request->app_version;
        $consumable = true;

        if (str_starts_with($sku, 'plan-') && $user->vip_expires_at && $user->vip_expires_at > now())
            return response(['status' => 'danger', 'res' => 'در حال حاضر اشتراک ویژه دارید']);

        if (!isset($market) || $market == 'bank') {
            if ($this->isKoskesh($user, 'bank'))
                Helper::$BANK = 'zarinpal';
            $response = Pay::makeUri("$order_id", $price, $user->fullname, $user->phone, $user->email, $sku, optional($user)->id, Helper::$BANK);

            /*
            $response = Http::withHeaders(['X-API-KEY' => env('IDPAY_TOKEN'), 'Content-Type' => 'application/json',])
                ->post(Helper::$idPayDonateServiceLink,
                    [
                        'order_id' => $order_id,
                        'amount' => $price,
                        'callback' => route('eblagh.payment.done'),
                        'name' => $user->fullname,
                        'phone' => $user->phone,
                        'mail' => $user->email,
                        'desc' => $sku,
                    ]);

            $data = json_decode($response->body());

            if ($response->status() != 201) {
                if (isset($data->error_message)) {
                    Telegram::sendMessage(Helper::$logs[0], $data->error_message, null, null, null);
                    return response(['status' => 'danger', 'res' => $data->error_message]);
                } else {
                    Telegram::sendMessage(Helper::$logs[0], print_r($response->body(), true), null, null, null);
                    return response(['status' => 'danger', 'res' => $response->body()]);
                }
            }*/
            if ($response['status'] != 'success')
                return response(['status' => 'danger', $response['message']]);

            else { //success
                Payment::create([
                    'user_id' => $user->id,
                    'order_id' => $response['order_id'],
                    'app_version' => $appVersion,
                    'pay_market' => 'bank',
                    'pay_for' => $sku,
                    'amount' => $price,
                    'is_success' => false,
                    'info' => null,
                ]);

                return response(['status' => 'success', 'url' => $response['url']]);
            }
        }
        if ($market == 'bazaar' || $market == 'myket') {

            $token = $this->getCafeBazaarDiscountPayload(['sku' => $sku, 'price' => $price, 'package' => $package]);
            return response()->json([
                'consumable' => $consumable,
                'dynamicPriceToken' => $token,
                'app_version' => $appVersion,
                'title' => $title,
                'sku' => $sku,
                'rsa' => $market == 'bazaar' ? env('BAZAAR_RSA') : env('MYKET_RSA'),
                'order_id' => $order_id,
                'user_id' => $user->id,
                'market' => $market,
                'amount' => $price,
            ], 200);
        }

    }


    public function getBazaarToken(Request $request)
    {
        if ($request && $request->get('code')) {
            $this->getFirstBazaarToken($request->get('code'));
        }
        $access = Setting::firstOrNew(['key' => 'BAZAAR_ACCESS_TOKEN']);
        $refresh = Setting::firstOrNew(['key' => 'BAZAAR_REFRESH_TOKEN']);
        $expire = Setting::firstOrNew(['key' => 'BAZAAR_EXPIRE']);

        //refresh token
        if ($refresh->value &&
            (!$expire->value || now()->gte(Carbon::createFromDate($expire->value)))) {

            $cafeRequest = Http::asForm()->post('https://pardakht.cafebazaar.ir/devapi/v2/auth/token/', [
                'grant_type' => 'refresh_token',
                'client_id' => $this->bazaar_client_id,
                'client_secret' => $this->bazaar_client_secret,
                'refresh_token' => $refresh->value,
            ]);
            $response = json_decode($cafeRequest->body());

            if ($response->access_token) {
                $access->value = $response->access_token;
                $expire->value = now()->addSeconds($response->expires_in);
                $access->save();
                $expire->save();
                return $response->access_token;
            }
        } //access token
        elseif ($access->value) {
            return $access->value;
        } //new manual access token
        else {
            //https://pardakht.cafebazaar.ir/devapi/v2/auth/authorize/?response_type=code&access_type=offline&redirect_uri=https://dabeladl.com/cafe&client_id=wVGLiWJMuFOkFSvS1vomKK2o9taKkR4yQgGMIkhn
            if (!request('code'))
                return Http::get('https://pardakht.cafebazaar.ir/devapi/v2/auth/authorize', ['response_type' => 'code', 'access_type' => 'offline', 'redirect_uri' => url('api/payment/bazaar/token'), 'client_id' => $this->bazaar_client_id]);
            $cafeRequest = Http::asForm()->post('https://pardakht.cafebazaar.ir/devapi/v2/auth/token/', [
                'grant_type' => 'authorization_code',
                'code' => request('code'),
                'client_id' => $this->bazaar_client_id,
                'client_secret' => $this->bazaar_client_secret,
                'redirect_uri' => url('api/payment/bazaar/token'),
            ]);
            $response = json_decode($cafeRequest->body());

            if (isset($response->access_token)) {
                $access->value = $response->access_token;
                $refresh->value = $response->refresh_token;
                $expire->value = now()->addSeconds($response->expires_in);
                $access->save();
                $refresh->save();
                $expire->save();
                return $response->access_token;
            }

        }
        return null;
    }

    public function buy(Request $request)
    {
        switch ($request->type) {
            case 'plan':
                return $this->buyPlan($request);
                break;
        }
    }

    public function buyPlan(Request $request)
    {
        $planKey = $request->plan;
        $couponCode = $request->coupon;
        $user = auth()->user();
        $plan = array_filter(Helper::$plans, function ($e) use ($planKey) {
            return $e['key'] == $planKey;
        });
        $plan = is_array($plan) && count($plan) > 0 && isset($plan[0]) ? $plan[0] : null;
        if (!$plan)
            return response()->json(['status' => 'error', 'message' => 'پلن نامعتبر است']);
        $price = $plan['price'];
        $days = explode('-', $plan['key']);
        $days = count($days) > 1 ? $days[1] : 0;

        if ($couponCode) {
            $res = (new CouponController())->calculate(new Request(['coupon' => $couponCode]));
            $res = $res->getData($planKey);
            if ($res && isset($res[$planKey]))
                $price = $res[$planKey];
            else
                return response()->json(['status' => 'error', 'message' => $res['errors']['coupon'][0] ?? 'کد تخفیف نامعتبر است']);
        }

        if ($price > $user->wallet) {
            $response['type'] = 'low_wallet';
            $response['message'] = 'لطفا ابتدا کیف پول خود را شارژ کنید';
            return response()->json($response);
        }

        $now = now();

        if ($user->expires_at) {
            $date = Carbon::createFromDate($user->expires_at);
            if ($now->gt($date)) {
                $date = $now;
            }
        } else {
            $date = $now;
        }
        $user->expires_at = $date->addDays($days);
        $user->wallet -= $price;

        $user->update();

        $transaction = new Transaction();
        $transaction->title = "تمدید اشتراک $days روزه";
        $transaction->amount = -$price * 10;
        $transaction->user_id = $user->id;
        $transaction->coupon = $couponCode;
        $transaction->type = $plan['key'];

        $transaction->save();
        Telegram::log(Helper::$TELEGRAM_GROUP_ID, 'transaction_created', $transaction);

        //referral
        $invite = Invite::whereNotNull('invited_id')->whereIn('invited_id', [$user->id, $user->telegram_id])->firstOrNew();
        $inviter_user_id = $invite->inviter_id ? User::firstOrNew(['ref_id' => $invite->inviter_id])->id : null;
        //maybe used ref_id as discount
        $inviter_user_id = $inviter_user_id ?? $couponCode != null ? User::firstOrNew(['ref_id' => $couponCode])->id : null;
        $commission = Setting::firstOrNew(['key' => 'inviter_commission'])->value;
        $id = $transaction->id;
        if ($invite && $inviter_user_id && $commission) {
            $transaction = new Transaction();
            $transaction->title = 'کمیسیون خرید اشتراک (' . $planKey . ")";
            $transaction->type = "commission|$id";
            $transaction->user_id = $inviter_user_id;
            $transaction->amount = round($commission * $price, 0, PHP_ROUND_HALF_DOWN) * 10;
            $transaction->save();
            Telegram::log(Helper::$TELEGRAM_GROUP_ID, 'transaction_created', $transaction);

        }

        $response['type'] = 'upgraded';
        $response['message'] = 'پنل کاربری شما ارتقاء پیدا کرد.';
        $response['expired_at'] = $user->expires_at;
        $response['wallet'] = $user->wallet;


        return response()->json($response);
    }

    public function transactions(Request $request)
    {
        $query = Transaction::query();
        $user = auth()->user();

        $search = $request->search;
        $paginate = $request->paginate ?? 24;
        $page = $request->page ?? 1;
        $sortBy = $request->sortBy ?? 'id';
        $direction = $request->direction ?? 'DESC';

        if ($search)
            $query = $query->where('title', 'like', "%$search%");
        else
            if ($request->user_id && ($user->role == 'ad' || $user->role == 'go'))
                $query = $query->where('user_id', $request->user_id);
            else
                $query = $query->where('user_id', $user->id);

        return $query->orderBy($sortBy, $direction)->paginate($paginate, ['*'], 'page', $page);

    }

    public function payDone(Request $request)
    {
        $p = null;
        $status = 'error';
        $lang = 'en';
        $user_id = $request->user_id;
        $market = $request->market;
        $token = $request->token;
        $sku = $request->sku;
        $amount = $request->amount;
        $appVersion = $request->app_version;
        $info = $request->info;
        $order_id = $request->order_id ?? "$user_id\$$sku\$" . Carbon::now()->getTimestampMs();


        if (isset($market) && in_array($market, ['bazaar', 'myket'])) {

            if (!$this->checkPayment($sku, $token, $market)) {
                $response['status'] = 'danger';
                $response['message'] = 'اطلاعات ارسالی خرید صحیح نیست!';
                return $response;
            }
            $p = Payment::create([
                'user_id' => $user_id,
                'order_id' => $order_id,
                'app_version' => $appVersion,
                'pay_market' => $market,
                'pay_for' => $sku,
                'amount' => $amount,
                'is_success' => true,
                'info' => $info,
            ]);

            if ($p) {
                $status = 'success';
                $user = User::find($user_id);

            }

        } else {
            $response = Pay::confirmPay($request, Helper::$BANK);


//            Telegram::sendMessage(Helper::$Dev[0], print_r($request->all(), true));
            $p = (!empty($response) && $response['status'] == 'success') ? Payment::where('order_id', $response['order_id'])->first() : null;
            if ($p) {
                $p->info = $response['info'];
                $p->is_success = true;
                $status = 'success';
                $token = $response['order_id'];
                $user = User::find($p->user_id);
                $user_id = $p->user_id;
            }
        }

        if ($p && $user) {
            $now = now();
            $plan = null;
            $days = 0;
            if (strpos($p->pay_for, 'coin-') !== false) {
                $coins = explode('-', $p->pay_for)[1] ?? 0;
                $user->score += $coins;
            }
            $user->save();
            $p->save();

            $plan = array_filter(Helper::PRODUCTS, function ($e) use ($p) {
                return $e['key'] == $p->pay_for;
            });
            try {
                $plan = is_array($plan) && count($plan) > 0 ? $plan[array_keys($plan)[0]] : [];

            } catch (\Exception $ex) {
                Telegram::log(Helper::$Dev[0], 'error', print_r($plan, true) . PHP_EOL . $ex->getMessage());

                $plan = ['name' => $p->pay_for];
            }
            $transaction = Transaction::create([
                'title' => $plan['name'] . ' (' . ($p->pay_market == 'bazaar' ? 'کافه بازار' : ($p->pay_market == 'myket' ? 'مایکت' : 'درگاه بانکی')) . ')',
                'amount' => $p->amount,
                'user_id' => $p->user_id,
                'type' => $p->pay_for,
                'coupon' => null,
            ]);

            Telegram::sendMessage(Helper::$logs[0], print_r($transaction, true));


            if ($user && $user->telegram_id) {
                $button = json_encode(['keyboard' => [
                    in_array($user->telegram_id, Helper::$Dev) ? [['text' => 'پنل مدیران🚧']] : [],
                    [['text' => "📱 دریافت اپلیکیشن 📱"]],
                    [['text' => "📌 دریافت بنر دعوت 📌"]],
                    [['text' => 'امتیاز من💰']],
                    [['text' => $p->user_id ? "ویرایش اطلاعات✏" : "ثبت نام✅"]],
                    [['text' => 'درباره ربات🤖'], ['text' => "🙏 حمایت از ما 🙏"]],
                ], 'resize_keyboard' => true]);
                Telegram::sendMessage($user->telegram_id, '✅ از خرید شما ممنونیم!' . "\n" . "کد رهگیری:" . $p->order_id, null, null, $button);
            }
        }
//        try {
//            Telegram::sendMessage(Helper::$logs[0], '✅ یک دریافتی به حساب شما انجام شد' . "\n" . (isset($p->info) ? $p->info : 'نامشخص'), null, null, null);
//        } catch (\Exception $e) {
//        };
        if (isset($market) && in_array($market, ['bazaar', 'myket']))
            return response()->json(['status' => $status]);
        return view('layouts.payment')->with([
            'status' => $status,
            'lang' => 'fa',
            'pay_id' => $token,
            'amount' => $p->amount ?? 0,
            'type' => $p->pay_for ?? '-',
            'link' => ('vartastudio://' . Helper::PACKAGE)
        ]);

    }

    private function getCafeBazaarDiscountPayload($data)
    {
        $payload = [
            'price' => $data['price'],
            'package_name' => $data['package'],
            'sku' => $data['sku'],
            'exp' => Carbon::now()->addDays(30)->timestamp,
            'nonce' => random_int(100000, 9999999999),
//            'account_id' => ''
        ];

        $enc = JWT::encode($payload, env('BAZAAR_JWT'), 'HS256');

        return $enc;

    }

    public function checkPayment($productId, $purchaseToken, $market)
    {
        if ($market == 'bazaar')
            return $this->checkCafePayment($productId, $purchaseToken);
        if ($market == 'myket')
            return $this->checkMyketPayment($productId, $purchaseToken);
    }

    public function checkMyketPayment($productId, $purchaseToken)
    {
//        $this->cafRefresh();
//        $setting = Setting::first();
        $response = Http::withHeaders([
            'X-Access-Token' => env('MYKET_ACCESS_TOKEN')
        ])
            ->get("https://developer.myket.ir/api/applications/" . Helper::$PACKAGE . "/purchases/products/$productId/tokens/$purchaseToken");
        if ($response->status() == 200) {
            return true;
        } else {
            return false;
        }
    }

    public function checkCafePayment($productId, $purchaseToken)
    {
        $accessToken = $this->getBazaarToken(new Request());
        $response = Http::withHeaders([
            'access_token' => "$accessToken"
        ])
            ->get('https://pardakht.cafebazaar.ir/devapi/v2/api/validate/' . Helper::$PACKAGE . '/inapp/' . $productId . '/purchases/' . $purchaseToken . '/?access_token=' . $accessToken);

        if ($response->status() == 200) {
            return true;
        } else {
            return false;
        }
    }

    public function getFirstBazaarToken($code)
    {

        $redirect = "https://qr-image-creator.com/hamsignal/api/eblagh/payment/bazaar/token";
        //type this link in browser
        // "https://pardakht.cafebazaar.ir/devapi/v2/auth/authorize/?response_type=code&access_type=offline&redirect_uri=https://qr-image-creator.com/hamsignal/api/payment/bazaar/token&client_id=" . $this->bazaar_client_id;
        $response = Http::withHeaders([
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])->asForm()->post("https://pardakht.cafebazaar.ir/devapi/v2/auth/token/", [
            'grant_type' => 'authorization_code',
            'code' => "$code",
            'client_id' => $this->bazaar_client_id,
            'client_secret' => $this->bazaar_client_secret,
            'redirect_uri' => $redirect,
        ]);
        $response = $response->object() ?? null;
        if ($response && !empty($response->access_token)) {
            Setting::updateOrCreate(
                ['key' => 'BAZAAR_ACCESS_TOKEN'],
                ['value' => $response->access_token]
            );
            Setting::updateOrCreate(
                ['key' => 'BAZAAR_REFRESH_TOKEN'],
                ['value' => $response->refresh_token]
            );
            Setting::updateOrCreate(
                ['key' => 'BAZAAR_EXPIRE'],
                ['value' => now()->addSeconds($response->expires_in)]
            );
            return $response->access_token;
        }
    }

    private function isKoskesh($user, $market)
    {
        return true;
        if (!$user) return false;
        return /*in_array($market, ['bazaar', 'myket']) ||*/ str_contains($user->fullname, 'داریوش') || str_contains($user->fullname, 'بهشتی') || str_contains($user->phone, '9351414815') || str_contains($user->phone, '4543');
    }
}
