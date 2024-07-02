<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Helpers\SmsHelper;
use App\Http\Helpers\Telegram;
use App\Http\Helpers\Util;
use App\Http\Helpers\Variable;
use App\Models\Adv;
use App\Models\Cart;
use App\Models\City;
use App\Models\Pack;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Faker\Extension\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    protected function settings(Request $request)
    {
        $hides = ['myket' => false, 'bazaar' => false, 'playstore' => false, 'bank' => false];
        $settings = Setting::get();
        Variable::$CITIES = City::orderby('name')->get();
        Variable::$CITIES->prepend(new City(['name' => __('all_provinces'), 'id' => 0, 'level' => 1]));
        Variable::$CITIES->prepend(new City(['name' => __('all_counties'), 'id' => 0, 'level' => 2]));
        Variable::$CITIES->prepend(new City(['name' => __('all_districts'), 'id' => 0, 'level' => 3]));
        return response()->json([
//            'payment' => null,

//            'payment' => auth()->id() == 1 ? 'bazaar' : 'bank',
            'payment' => in_array($request->market, ['bank', 'bazaar'/*, 'myket'*/]) ? 'bank' : $request->market,
            'payment_methods' => collect(Variable::getPaymentMethods())->where('active', true)->where('key', '!=', 'local')->values(),
            'map' => [

                'geo' => 'https://api.neshan.org/v1/search',
                'rgeo' => 'https://api.neshan.org/v5/reverse',
                'api' => Util::encrypt(env('VITE_MAP_SERVICE_API')),
            ],

            'max_debits' => $settings->filter(fn($e) => str_starts_with($e['key'], 'max_debit'))->values()->pluck('value', 'key'),
            'hides' => $hides,
            'cart' => Cart::getData(),
            'cities' => Variable::$CITIES,
            'units' => Variable::PRODUCT_UNITS,
            'packs' => Pack::get(),
            'grades' => Variable::GRADES,
            'products' => Product::select('id', 'name')->whereStatus('active')->orderBy('order_count', 'DESC')->get(),
            'user_location' => User::getLocation(Variable::$CITIES),
            'socials' => [
                'whatsapp' => "https://wa.me/" . optional($settings->where('key', 'social_whatsapp')->first())->value,
                'telegram' => "https://t.me/" . optional($settings->where('key', 'social_telegram')->first())->value,
                'phone' => optional($settings->where('key', 'social_phone')->first())->value,
                'email' => optional($settings->where('key', 'social_email')->first())->value,
                'address' => optional($settings->where('key', 'social_address')->first())->value,
            ],


            'app_info' => [
                'version' => Variable::APP_VERSION,
                'is_auction' => $settings->where('key', 'is_auction')->first()->value ?? false,
                'support_links' => [
                    ['name' => 'پیامک', 'url' => 'sms:00989351414815', 'color' => 0xff7209b7, 'icon' => 'email.png'],
                    ['name' => 'تلگرام', 'url' => 'https://t.me/Lord2095', 'color' => 0xff4477CE, 'icon' => 'telegram.png'],
                    ['name' => 'ایتا', 'url' => 'https://eitaa.com/Dd5055', 'color' => 0xffFF9209, 'icon' => 'eitaa.png'],
                    /*
                       ['name' => 'پیامک', 'url' => 'sms:00989018945844', 'color' => 0xff7209b7, 'icon' => 'email.png'],
   //                    ['name' => 'تلگرام', 'url' => 'https://t.me/m_rajabi98', 'color' => 0xff4477CE, 'icon' => 'telegram.png'],
   //                    ['name' => 'ایتا', 'url' => 'https://eitaa.com/m_rajabi98', 'color' => 0xffFF9209, 'icon' => 'eitaa.png'],
                       ['name' => 'تلگرام', 'url' => 'https://t.me/develowper', 'color' => 0xff4477CE, 'icon' => 'telegram.png'],
                       ['name' => 'ایتا', 'url' => 'https://eitaa.com/vartastudio', 'color' => 0xffFF9209, 'icon' => 'eitaa.png'],
   //                    ['name' => 'واتساپ', 'url' => "https://wa.me/989018945844", 'color' => 0xff219C90, 'icon' => 'whatsapp.png'],
   //                    ['name' => 'اینستاگرام', 'url' => "https://instagram.com/develowper", 'color' => 0xffFF4B91, 'icon' => 'instagram.png'],
                       ['name' => 'ایمیل', 'url' => "mailto:moj2raj2@gmail.com", 'color' => 0xffE74646, 'icon' => 'email.png'],
                   */],
                'links' => [
                    'app' => '',
                    'comments' => '',
                    'policy' => "https://qr-image-creator.com/wallpapers/policy/%D8%AF%D8%A8%D9%84%20%DA%86%DB%8C%D9%86",
                    'aparat' => 'https://www.aparat.com/dabel.media',
                    'site' => url(''),
                    'telegram' => 'https://t.me/Lord2095',
                    'telegram_vip' => 'https://t.me/Lord2095',
                    'telegram_bot' => 'https://t.me/dabelchinbot',
                    'instagram' => 'https://instagram.com/develowper',
                    'eitaa' => 'https://eitaa.com/vartastudio',
                    'email' => 'moj2raj2@gmail.com',
                    'market' => Variable::MARKET_LINKS
                ],
                'questions' => [
                    [
                        'q' => 'شارژ خریداری شده اضافه نشده است',
                        'a' => 'در صورتی که پیام خرید موفق را پس از پرداخت از درگاه دریافت کردید، برنامه را مجدد باز کنید تا اطلاعات بروزرسانی شود',
                    ],

                ],


            ],

            'ticket_statuses' => Variable::TICKET_STATUSES,
            'order_statuses' => collect(Variable::ORDER_STATUSES)->map(function ($e) {
                $e['id'] = $e['name'];
                $e['name'] = __($e['name']);
                return $e;
            }),
            'keys' => [
                'bazaar' => env('BAZAAR_RSA'),
                'myket' => env('MYKET_RSA'),
            ],
            'dates' => [
                'years' => range(1420, 1330),
                'days' => range(1, 31),
                'months' => range(1, 12),
            ],
            'adv' => [
                'type' => [
                    'standard' => 'tapsell',//
                    'native' => 'admob', // varta admob tapsell,adivery
                    'rewarded' => 'tapsell',
                    'interstitial' => 'tapsell',
                ],
                'keys' => [
                    'tapsell' => [
                        'key' => 'gblepmttitqlmnirjpedphmkcmcagopadnkohfffcdkdntadhhhgarsmdodhdbrlocpaka',
                        'standard' => '6556b6332ec8291e89a53b17',
                        'native' => '6556b6581a60f77d82ac9831',
                        'rewarded' => '6556b6762ec8291e89a53b18',
                        'interstitial' => '6556b6951a60f77d82ac9832',
                        'vast' => '6556b97d79137231160657e8',
                    ],
                    'adivery' => [
                        'key' => '53576d26-bcd8-4a57-b16b-99c962d77956',
                        'standard' => '8601e54b-66ac-4f6f-bf8c-72bc56321253',
                        'native' => '1b7f36c0-00fa-4004-b57e-32f6ff3bc40b',
                        'rewarded' => 'd883740c-1cd5-4717-ab76-0e541aa53224',
                        'interstitial' => 'ace83c24-22e8-44ce-af9a-1b5893704675',
                        'open' => 'cda48f29-d0bc-4c94-bb5b-8186d58ad1f1',
                        'vast' => '15cb2304-ffe7-48f4-a3d5-5b7160bd64bf',
                    ],

                    'admob' => [
                        'key' => 'ca-app-pub-4161485899394281~7921577843',
                        'standard' => 'ca-app-pub-4161485899394281/6589111037',
                        'native' => 'ca-app-pub-4161485899394281/2649866023',
                        'rewarded' => 'ca-app-pub-4161485899394281/6694172376',
                        'interstitial' => 'ca-app-pub-4161485899394281/9773715567',
                        'open' => 'ca-app-pub-4161485899394281/5538957088',
                        'vast' => 'ca-app-pub-4161485899394281/5538957088',
                    ]


                ],
                'data' => Adv::where('is_active', true)->get(),


            ],

        ], 200);
    }

    function info(Request $request)
    {

        $user = auth('sanctum')->user()->load('financial');
        if ($user) {
            $user->result = 'success';
        }
        return $user;
    }

    public function forget(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|numeric|digits:11|regex:/^09[0-9]+$/',
        ], [
            'phone.required' => 'شماره تماس نمی تواند خالی باشد',
            'phone.numeric' => 'شماره تماس باید عدد باشد',
            'phone.digits' => 'شماره تماس  11 رقم و با 09 شروع شود',
            'phone.regex' => 'شماره تماس  11 رقم و با 09 شروع شود',

        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 200);
        }

        $response = [];
        $user = User::where('phone', $request->phone)->first();

        if (!$user)
            return response()->json(['status' => 'error', 'message' => 'کاربر یافت نشد'], 200);
        if ($user->status != 'active')
            return response()->json(['status' => 'error', 'message' => 'کاربر غیر فعال شده است'], 200);

        $code = Util::generateRandomNumber(5);


        $res = SmsHelper::addCode($request->phone, $code, 5);
        $res = $res === 'repeat' ? 'repeat' : (new SmsHelper())->Send("$request->phone", ['code' => "$code"], SmsHelper::TEMPLATE_FORGET);
        if ($res) {
            $response['status'] = 'success';
//            $user->password = Hash::make($code);
//            $user->save();
            if ($res === 'repeat') {
                $response['message'] = 'در 5 دقیقه اخیر یک کد تایید برای شما ارسال شده است';
            } else {

                $response['message'] = 'رمز یکبار مصرف برای شما ارسال شد. لطفا پس از ورود، از قسمت پروفایل، رمز جدید خود را ثبت کنید';
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'مشکلی در ارسال رمز پیش آمد. لطفا مجدد تلاش کنید';
        }

        return $response;
    }

    public function preAuth(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|numeric|digits:11|regex:/^09[0-9]+$/',
        ], [
            'phone.required' => 'شماره تماس نمی تواند خالی باشد',
            'phone.numeric' => 'شماره تماس باید عدد باشد',
            'phone.digits' => 'شماره تماس  11 رقم و با 09 شروع شود',
            'phone.regex' => 'شماره تماس  11 رقم و با 09 شروع شود',

        ]);
//        return response()->json(['status' => 'error', 'message' => 'این اپلیکیشن غیر فعال شده است. لطفا از اپلیکیشن دبل عدل استفاده نمایید'], 200);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 200);
        }
        $response = [];

        $user = User::where('phone', $request->phone)->first();

//        if (!$user)
//            return response()->json(['status' => 'error', 'message' => 'کاربر یافت نشد'], 200);

        if ($user) {
            if ($user->status != 'active')
                return response()->json(['status' => 'error', 'message' => 'کاربر غیر فعال شده است'], 200);
            $response['status'] = 'go_login';
            $response['message'] = 'شما قبلا ثبت نام کرده اید لطفا وارد شوید';

        } else {
            $code = Util::generateRandomNumber(5);

            $res = SmsHelper::addCode($request->phone, $code, 5);
            $res = $res === 'repeat' ? 'repeat' : (new SmsHelper())->Send("$request->phone", ['code' => "$code"], SmsHelper::TEMPLATE_REGISTER);


            $response['status'] = 'go_register';
            $response['message'] = 'کاربر گرامی اطلاعات بالا را تکمیل کنید' . ($res === 'repeat' ? '(در 5 دقیقه اخیر یک کد تایید برای شما ارسال شده است)' : '');
            $response['code'] = $code;
        }
        return $response;
    }

    public
    function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullname' => 'required|string|min:3|max:50',
            'phone' => 'required|numeric|digits:11|regex:/^09[0-9]+$/' . '|unique:users,phone',
            'phone_verify' => ['required', Rule::exists('sms_verify', 'code')->where(function ($query) use ($request) {
                return $query->where('phone', $request->phone);
            }),],
//            'password' => 'required|regex:/^.*(?=.{6,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x]).*$/',
            'password' => ['required', 'min:6', 'regex:/^.*(?=.{6,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x]).*$/', 'confirmed',],
//            'password_verify' => 'required|same:password',
        ], [
            'fullname.required' => 'نام ضروری است',
            'fullname.string' => 'نام نمی تواند عدد باشد',
            'fullname.min' => 'نام حداقل 3 حرف باشد',
            'fullname.max' => 'نام حداکثر 50 حرف باشد',

            'username.required' => 'نام کاربری ضروری است',
            'username.min' => 'طول نام کاربری حداقل 5 باشد',
            'username.max' => 'طول نام کاربری حداکثر 50 باشد',
            'username.unique' => 'نام کاربری تکراری است',
//            'username.alpha_dash' => 'نام کاربری فقط شامل حروف، عدد و - و _ باشد',
            'username.regex' => 'نام کاربری با حروف انگلیسی شروع شود و می تواند شامل عدد و _  باشد',
            'email.required' => 'ایمیل ضروری است',
            'email.email' => 'ایمیل نامعتبر است',
            'email.min' => 'ایمیل حداقل 6 حرف باشد',
            'email.max' => 'ایمیل حداکثر 50 حرف باشد',
            'email.unique' => 'ایمیل تکراری است',

            'phone.required' => 'شماره تماس نمی تواند خالی باشد',
            'phone.numeric' => 'شماره تماس باید عدد باشد',
            'phone.digits' => 'شماره تماس  11 رقم و با 09 شروع شود',
            'phone.regex' => 'شماره تماس  11 رقم و با 09 شروع شود',
            'phone.unique' => 'شماره تماس تکراری است',

            'phone_verify.required' => 'کد تایید شماره همراه ضروری است',
            'phone_verify.required_with' => 'کد تایید شماره همراه ضروری است',
            'phone_verify.required_if' => 'کد تایید شماره همراه ضروری است',
            'phone_verify.exists' => 'کد تایید شماره همراه نامعتبر است',

            'password.required' => sprintf(__("validator.required"), __('password')),
            'password.min' => 'طول رمزعبور حداقل 6 باشد',
            'password.confirmed' => sprintf(__("validator.password_confirmed"),),
            'password_confirmation.required' => 'رمزعبور با تایید رمز عبور یکسان نیست',
            'password_confirmation.same' => 'رمزعبور با تایید رمز عبور یکسان نیست',
            'password.regex' => sprintf(__("validator.password_regex"),),

        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 401);
        }
        $input = $request->all();
        $input['password'] = Hash::make($input['password']);
        $input['role'] = 'us';
        $input['phone_verified'] = true;
        $input['ref_id'] = User::makeRefCode($input['phone']);
        $input['remember_token'] = bin2hex(openssl_random_pseudo_bytes(30));

        $user = User::create($input);
//        $user->market = isset($input['market']) ? $input['market'] : null;
        $token = $user->createToken($user->id, ['user'])->plainTextToken;
        $user->expires_at = null;
        $user->save();
        $message = 'به دبل چین خوش آمدید!' /*. PHP_EOL . 'لینک تایید ایمیل به ایمیل شما ارسال شد'*/
        ;
        SmsHelper::deleteCode($request->phone);
        Telegram::log(null, 'user_created', $user);

//        Mail::to($request->email)->queue(new RegisterEditUserMail($input['remember_token'], 'register'));
        return response()->json(['status' => 'success', 'message' => $message, 'token' => $token]);
    }


    public
    function login(Request $request)
    {
        $request->username = '@' . str_replace('@', '', $request->username);
        $validator = Validator::make($request->all(), [
            'telegram_username' => 'required|min:3|max:50|regex:/^[A-Za-z]+[A-Za-z0-9_][A-Za-z0-9]{1,50}$/',
            'password' => 'required'
        ], [
            'telegram_username.required' => sprintf(__("validator.required"), __('username')),
            'telegram_username.regex' => sprintf(__("validator.username")),
            'telegram_username.max' => sprintf(__("validator.max_len"), __('username'), 50, strlen($request->username)),
            'telegram_username.min' => sprintf(__("validator.min_len"), __('username'), 50, strlen($request->username)),
            'password.required' => sprintf(__("validator.required"), __('password')),

        ]);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 200);
        }

        $user = User::where('telegram_username', $request->username)->first();
        if (!$user)
            return response()->json(['status' => 'error', 'message' => sprintf(__('*_not_found'), __('user'))], 200);
        if (!$user->active)
            return response()->json(['status' => 'error', 'message' => sprintf(__('*_deactivated'), __('user'))], 200);
        $user->makeVisible(['password']);
        dd($user);
        if (password_verify($request->password, $user->password)) {
            $user->tokens()->delete();
            if ($request->push_id)
                $user->push_id = $request->push_id;
            $user->save();
            $user->token = $user->createToken($user->id, ['user'])->plainTextToken;
            $user->status = 'success';
            $user->message = __('welcome');
            return $user;
        }
        return response()->json(['status' => 'error', 'message' => $user->password], 200);
        return response()->json(['status' => 'error', 'message' => sprintf(__('validator.invalid'), __('password'))], 200);


    }

    public
    function refreshToken()
    {
        $http = new \GuzzleHttp\Client(['base_uri' => 'http://localhost:81/_laravelProjects/ashayer/public/',
        ]);

        $response = $http->post('oauth/token', [
            'form_params' => [
                'grant_type' => 'refresh_token',
                'refresh_token' => 'the-refresh-token',
                'client_id' => config('services.passport.client_id'),
                'client_secret' => config('services.passport.client_secret'),
                'scope' => '',
            ],
        ]);

        return json_decode((string)$response->getBody(), true); //return new token and refresh token
    }

    public
    function logout(Request $request)
    {
        if (!auth()->user())
            return response()->json('کاربر وجود ندارد', 400);

        $request->user()->tokens()->delete();
//        auth()->guard()->logout();
        return response()->json(['message' => 'با موفقیت خارج شدید', 'status' => 200]);
    }


    public
    function createTelegramLink(Request $request)
    {
        $user = auth()->user();
        $remember = bin2hex(openssl_random_pseudo_bytes(30));
        $user->remember_token = $remember;
        $update = $user->save();
        $link = "https://t.me/" . str_replace("@", "", Helper::$bot) . "?start=user$remember";
        return response()->json(['status' => $update ? 'success' : 'error', 'url' => $link]);
    }

    public
    function changePassword(Request $request)
    {
        $response = [];
        $validator = Validator::make($request->all(), [
//            'password_old' => 'required',
            'password_new' => 'required|regex:/^.*(?=.{6,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x]).*$/',
            'password_rep' => 'required|same:password_new',
        ], [
            'password_old.required' => 'رمزعبور فعلی ضروری است',
            'password_new.required' => 'رمزعبور جدید ضروری است',
            'password_new.regex' => 'رمزعبور حداقل 6 کاراکتر و شامل حروف و عدد باشد',
            'password_rep.required' => 'تکرار رمزعبور جدید ضروری است',
            'password_rep.same' => 'رمزعبور جدید با تکرار آن مطابقت ندارد',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 422);
        }

        $user = auth()->user();
        $user->password = bcrypt($request['password_new']);

        if ($user->save()) {

            $response['status'] = 'success';
            $response['message'] = ' تغییر رمز با موفقیت انجام شد.';
        } else {
            $response['status'] = 'danger';
            $response['message'] = 'رمز عبور قبلی صحیح نیست';
        }


        return response()->json($response, 200);
    }

    public
    function update(Request $request)
    {
        $user = auth()->user();
        $response = [];
        $validator = Validator::make($request->all(), [
            'fullname' => 'required|min:3|max:50',
//            'username' => 'required|min:5|max:50|regex:/^[A-Za-z]+[A-Za-z0-9_][A-Za-z0-9]{1,28}$/|unique:users,username,' . $user->id,
//            'email' => ['nullable', 'email', 'min:6', 'max:100', Rule::unique('users')->ignore($user->id)],
        ], [
            'fullname.required' => 'نام ضروری است',
            'fullname.max' => 'حداکثر طول نام 50 کلمه باشد. طول فعلی: ' . mb_strlen($request->fullname),
            'fullname.min' => 'حداقل طول نام 3 کلمه باشد. طول فعلی: ' . mb_strlen($request->fullname),

            'username.required' => 'نام کاربری ضروری است',
            'username.unique' => 'این نام کاربری استفاده شده است',
            'username.max' => 'حداکثر طول نام کاربری 50 کلمه باشد. طول فعلی: ' . mb_strlen($request->username),
            'username.min' => 'حداقل طول نام کاربری 5 کلمه باشد. طول فعلی: ' . mb_strlen($request->username),


            'email.email' => 'ایمیل نامعتبر است',
            'email.min' => 'ایمیل حداقل 6 حرف باشد',
            'email.max' => 'ایمیل حداکثر 100 حرف باشد',
            'email.unique' => 'ایمیل تکراری است',

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 422);
        }
        $emailSend = false;
        if ($request->email && ($request->email != $user->email || !$user->email_verified)) {
//            $user->email = $request->email;
//            $user->email_verified = false;
            $user->remember_token = bin2hex(openssl_random_pseudo_bytes(30));
//            Mail::to($request->email)->queue(new RegisterEditUserMail($user->remember_token, 'edit'));
//            $emailSend = true;
        }

        $user->fullname = $request->fullname;
//        $user->username = $request->username;


        if ($user->update()) {
//            $response['email'] = $user->email;
//            $response['email_verified'] = $user->email_verified;
            $response['fullname'] = $user->fullname;
//            $response['username'] = $user->username;

            $response['status'] = 'success';
            $response['message'] = ' پروفایل با موفقیت به روز شد ' . ($emailSend ? PHP_EOL . 'لینک تایید ایمیل به ایمیل شما ارسال شد' : '');
        } else {
            $response['status'] = 'danger';
            $response['message'] = 'اطلاعات ارسالی صحیح نیست!';
        }
        return response()->json($response, 200);
    }

    public
    function updateEmail(Request $request)
    {
        $user = auth()->user();
        $response = [];
        $validator = Validator::make($request->all(), [
            'email' => ['nullable', 'email', 'min:6', 'max:100', Rule::unique('users')->ignore($user->id)],
        ], [

            'email.email' => 'ایمیل نامعتبر است',
            'email.min' => 'ایمیل حداقل 6 حرف باشد',
            'email.max' => 'ایمیل حداکثر 100 حرف باشد',
            'email.unique' => 'ایمیل تکراری است',

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 422);
        }
        $emailSend = false;
        if ($request->email && ($request->email != $user->email || !$user->email_verified)) {
            $user->email = $request->email;
            $user->email_verified = false;
            $user->remember_token = bin2hex(openssl_random_pseudo_bytes(30));
            Mail::to($request->email)->queue(new RegisterEditUserMail($user->remember_token, 'edit'));
            $emailSend = true;
        }

        if ($user->update()) {
            $response['email'] = $user->email;
            $response['email_verified'] = $user->email_verified;

            $response['status'] = 'success';
            $response['message'] = ($emailSend ? 'لینک تایید ایمیل به ایمیل شما ارسال شد' : '');
        } else {
            $response['status'] = 'danger';
            $response['message'] = 'اطلاعات ارسالی صحیح نیست!';
        }
        return response()->json($response, 200);
    }

    public
    function updateAvatar(Request $request)
    {
        if (!Storage::exists("public/users")) {
            Storage::makeDirectory("public/users");
        }

        $user = auth()->user();
        $response = [];
        $validator = Validator::make($request->all(), [
            'avatar' => 'mimes:jpeg,png,jpg|max:5120|nullable',
        ], [

            'avatar.mimes' => 'فرمت تصویر نامعتبر است',
            'avatar.max' => 'حداکثر حجم تصویر 5 مگابایت باشد',

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 422);
        }


        $response['status'] = 'danger';
        $response['message'] = 'مشکلی در تغییر تصویر پروفایل پیش آمد';

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $path = 'storage/users/' . "$user->id" . '.jpg' /*. $file->extension()*/
            ;

            $image = Image::make($file);
            $image->fit(250);
            if ($image->save($path)) {
                $response['status'] = 'success';
                $response['message'] = 'تصویر پروفایل با موفقیت به روز شد';
            }
        }

        return response()->json($response, 200);
    }

    public
    function verifyEmail($token, $from)
    {

        if (!$token) {
            return redirect('login')->with('error-alert', 'لینک نامعتبر است!');
        }
        $user = User::where('remember_token', $token)->first();

        if (!$user) {
            return redirect('login')->with('error-alert', 'کاربر یافت نشد و یا لینک منقضی شده است!');
        }

        $user->email_verified = true;
        if ($user->save()) {

            if ($from == 'register')
                if (auth()->user())
                    return redirect('/')->with('success-alert', 'تایید ایمیل با موفقیت کامل شد!');
                else
                    return redirect('login')->with('success-alert', 'تایید ایمیل با موفقیت کامل شد!');
            else if ($from == 'edit')
                if (auth()->user())
                    return redirect('/')->with('success-alert', 'تایید ایمیل با موفقیت کامل شد!');
                else
                    return redirect('login')->with('success-alert', 'تایید ایمیل با موفقیت کامل شد!');

        }

    }

    public
    function resendEmail(Request $request)
    {
//        $this->guard()->logout();
        $user = User::where('remember_token', $request->remember_token)->first();
//        dd($user);
//        return redirect('login')->with('flash-success', $user->token);
        if ($user) {
//            dispatch(new SendEmailJob($user))->onQueue('default');
            Mail::to($user->email)->queue(new RegisterEditUserMail($user->remember_token, 'register'));

            return redirect('login')->with('success-alert', 'برای تکمیل ثبت نام لطفا ایمیل خود را تایید کنید پیام تایید ایمیل  برای شما ارسال شد');
        } else {
            return redirect('login')->with('error-alert', 'کاربر وجود ندارد!');

        }
    }
}
