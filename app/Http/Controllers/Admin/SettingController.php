<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','is_super_admin']);
    }
    public function general()
    {
        if (session('success')) {
            toast(session('success'), 'success');
        }
        return view('admin.settings.general');
    }
   /* public function generalStore(Request $request)
    {
        $request->validate(
            [
                'app_name_ar'  =>  'required',
                'app_name_en'  =>  'required',
                'site_link' => 'required',
                'email' => 'sometimes|nullable|email',
                'currency'  =>  'required',
                'currency_atr'  =>  'required',
                'customers_terms_conditions'=>'required|string',
                'factories_terms_conditions'=>'required|string',
                'drivers_terms_conditions'=>'required|string',
                'shipping_company_terms_conditions'=>'required|string',
                'policy'=>'required|string',

            ]
        );
        $requestData = [
            'app_name_ar' => $request->app_name_ar,
            'app_name_en'   =>  $request->app_name_en,
            'currency'  =>  $request->currency,
            'currency_atr'=>$request->currency_atr,
            'site_link' => $request->site_link,
            'customers_terms_conditions'=>$request->customers_terms_conditions,
            'factories_terms_conditions'=>$request->factories_terms_conditions,
            'drivers_terms_conditions'=>$request->drivers_terms_conditions,
            'shipping_company_terms_conditions'=>$request->shipping_company_terms_conditions,
            'policy'=>$request->policy,
        ];
        if ($request->has('email') && $request->email !='') {
            $requestData['email'] = $request->email;
        }
        if ($request->has('phone') && $request->phone !='') {
            $requestData['phone'] = $request->phone;
        }
        if ($request->has('address_ar') && $request->address_ar !='') {
            $requestData['address_ar'] = $request->address_ar;
        }
        if ($request->has('address_en') && $request->address_en !='') {
            $requestData['address_en'] = $request->address_en;
        }
        if ($request->has('terms_conditions') && $request->terms_conditions) {
            $requestData['terms_conditions'] = $request->terms_conditions;
        }
        $requestData['logo'] = Setting('favoico');
        if ($request->logo) {
            if (Setting('logo') != 'uploads/img/logo.jpg' && Setting('logo') != 'uploads/img/logo.png') {
                if (file_exists(public_path(Setting('logo')))) {
                    unlink(public_path(Setting('logo')));
                }
            }
            $request->logo->store('', ['disk' => 'public_uploads']);
            $requestData['logo'] = 'uploads/' . $request->logo->hashName();
        }
         $requestData['favoico']=Setting('favoico');
        if ($request->favoico) {
            if (Setting('favoico') != 'uploads/img/logo.jpg' && Setting('favoico') != 'uploads/img/logo.png') {
                if (file_exists(public_path(Setting('favoico')))) {
                    unlink(public_path(Setting('favoico')));
                }
            }
            $request->favoico->store('', ['disk' => 'public_uploads']);
            $requestData['favoico'] = 'uploads/' . $request->favoico->hashName();
        }
        Setting($requestData)->save();
        session()->flash('success', __('site.saved_success'));
        return redirect()->back();
    }*/

public function generalStore(Request $request)
{
    /* -----------------------------------------------------------------
     | 1. Validate input
     |----------------------------------------------------------------- */
    $request->validate([
        // basics
        'app_name_ar'  => 'required|string',
        'app_name_en'  => 'required|string',
        'site_link'    => 'required|url',
        'email'        => 'sometimes|nullable|email',
        'phone'        => 'sometimes|nullable|string',
        'currency'     => 'required|string',
        'currency_atr' => 'required|string',

        // terms / policy (EN & AR)
        'customers_terms_conditions'            => 'required|string',
        'customers_terms_conditions_ar'         => 'required|string',
        'factories_terms_conditions'            => 'required|string',
        'factories_terms_conditions_ar'         => 'required|string',
        'drivers_terms_conditions'              => 'required|string',
        'shipping_company_terms_conditions'     => 'required|string',
        'shipping_company_terms_conditions_ar'  => 'required|string',
        'policy'                                => 'required|string',

        // optional images
        'logo'     => 'sometimes|file|image|mimes:png,jpg,jpeg,svg|max:2048',
        'favoico'  => 'sometimes|file|image|mimes:png,jpg,jpeg,svg,ico|max:1024',
    ]);

    /* -----------------------------------------------------------------
     | 2. Collect scalar settings (strip empty values)
     |----------------------------------------------------------------- */
    $requestData = array_filter(
        $request->only([
            'app_name_ar', 'app_name_en', 'currency', 'currency_atr', 'site_link',
            'email', 'phone', 'address_ar', 'address_en',
            'customers_terms_conditions', 'customers_terms_conditions_ar',
            'factories_terms_conditions', 'factories_terms_conditions_ar',
            'drivers_terms_conditions',
            'shipping_company_terms_conditions', 'shipping_company_terms_conditions_ar',
            'policy',
        ]),
        fn ($value) => !is_null($value) && $value !== ''
    );

    /* -----------------------------------------------------------------
     | 3. Handle logo upload
     |----------------------------------------------------------------- */
    $requestData['logo'] = Setting('logo');          // keep existing path by default
    if ($request->hasFile('logo')) {
        // delete previous custom logo
        if (Setting('logo') &&
            !in_array(Setting('logo'), ['uploads/img/logo.jpg', 'uploads/img/logo.png']) &&
            file_exists(public_path(Setting('logo')))) {
            unlink(public_path(Setting('logo')));
        }

        $path = $request->file('logo')
                        ->store('', ['disk' => 'public_uploads']); // e.g. uploads/xxxx.png
        $requestData['logo'] = 'uploads/' . basename($path);
    }

    /* -----------------------------------------------------------------
     | 4. Handle favicon upload
     |----------------------------------------------------------------- */
    $requestData['favoico'] = Setting('favoico');
    if ($request->hasFile('favoico')) {
        if (Setting('favoico') &&
            !in_array(Setting('favoico'), ['uploads/img/logo.jpg', 'uploads/img/logo.png']) &&
            file_exists(public_path(Setting('favoico')))) {
            unlink(public_path(Setting('favoico')));
        }

        $path = $request->file('favoico')
                        ->store('', ['disk' => 'public_uploads']);
        $requestData['favoico'] = 'uploads/' . basename($path);
    }

    /* -----------------------------------------------------------------
     | 5. Persist & redirect
     |----------------------------------------------------------------- */
    Setting($requestData)->save();  // spatie/laravel-settings helper
    return redirect()->back()->with('success', __('site.saved_success'));
}
    public function seo()
    {
        if (session('success')) {
            toast(session('success'), 'success');
        }
        return view('admin.settings.seo');
    }
    public function seoStore(Request $request)
    {
        // 1) Validate inputs (nullable allowed, proper types)
        $request->validate([
            'title'                 => 'sometimes|nullable|string',
            'canonical'             => 'sometimes|nullable|url',
            'keywords_ar'           => 'sometimes|nullable|string',
            'keywords_en'           => 'sometimes|nullable|string',
            'description_ar'        => 'sometimes|nullable|string',
            'description_en'        => 'sometimes|nullable|string',
            'og_site_name'          => 'sometimes|nullable|string',
            'og_title'              => 'sometimes|nullable|string',
            'og_type'               => 'sometimes|nullable|string',
            'og_url'                => 'sometimes|nullable|url',
            'og_description_ar'     => 'sometimes|nullable|string',
            'og_description_en'     => 'sometimes|nullable|string',
            'twitter_title'         => 'sometimes|nullable|string',
            'twitter_domain'        => 'sometimes|nullable|string',
            'twitter_card_ar'       => 'sometimes|nullable|string',
            'twitter_card_en'       => 'sometimes|nullable|string',
            'twitter_description_ar'=> 'sometimes|nullable|string',
            'twitter_description_en'=> 'sometimes|nullable|string',
            'og_image'              => 'sometimes|file|image|mimes:png,jpg,jpeg,svg,webp|max:4096',
        ]);

        // 2) Collect, normalize (trim, convert 'null' to null) and drop empties
        $keys = [
            'title','canonical','keywords_ar','keywords_en','description_ar','description_en',
            'og_site_name','og_title','og_type','og_url','og_description_ar','og_description_en',
            'twitter_title','twitter_domain','twitter_card_ar','twitter_card_en',
            'twitter_description_ar','twitter_description_en',
        ];

        $raw = $request->only($keys);

        $normalized = array_map(function ($v) {
            if (is_string($v)) {
                $v = trim($v);
                if ($v === 'null') {
                    $v = null; // treat literal "null" as null
                }
            }
            return $v;
        }, $raw);

        // Remove nulls and empty strings so we don't insert null values
        $requestData = array_filter($normalized, fn ($v) => !is_null($v) && $v !== '');

        // 3) Handle OG image upload
        if ($request->hasFile('og_image')) {
            $current = Setting('og_image');
            if ($current && !in_array($current, ['uploads/logo.jpg', 'uploads/logo.png'])) {
                if (file_exists(public_path($current))) {
                    @unlink(public_path($current));
                }
            }
            $path = $request->file('og_image')->store('', ['disk' => 'public_uploads']);
            $requestData['og_image'] = 'uploads/' . basename($path);
        }

        // 4) Save filtered settings (no null values)
        if (!empty($requestData)) {
            Setting($requestData)->save();
        }

        return redirect()->back()->with('success', __('site.saved_success'));
    }
    public function social()
    {
        if (session('success')) {
            toast(session('success'), 'success');
        }
        return view('admin.settings.social');
    }
    public function socialStore(Request $request)
    {
        $request->validate(
            [
                'facebook_link' => 'nullable|url',
                'twitter_link' => 'nullable|url',
                'instagram_link' => 'nullable|url',
                'snapchat_link' => 'nullable|url',
                'linkedin_link' => 'nullable|url',
                'youtube_link'=>    'nullable|url',
            ],
            [
                'facebook_link.url' => 'الرابط غير صحيح',
                'twitter_link.url' => 'الرابط غير صحيح',
                'instagram_link.url' => 'الرابط غير صحيح',
                'snapchat_link.url' => 'الرابط غير صحيح',
                'linkedin_link.url' => 'الرابط غير صحيح',
                'youtube_link.url' => 'الرابط غير صحيح',
            ]
        );
        $requestData = [];
        if ($request->has('facebook_link')) {
            $requestData['facebook_link'] = $request->facebook_link;
        }
        if ($request->has('instagram_link')) {
            $requestData['instagram_link'] = $request->instagram_link;
        }
        if ($request->has('twitter_link')) {
            $requestData['twitter_link'] = $request->twitter_link;
        }
        if ($request->has('snapchat_link')) {
            $requestData['snapchat_link'] = $request->snapchat_link;
        }
        if ($request->has('linkedin_link')) {
            $requestData['linkedin_link'] = $request->linkedin_link;
        }
        if ($request->has('youtube_link')) {
            $requestData['youtube_link'] = $request->youtube_link;
        }

        Setting($requestData)->save();
        session()->flash('success', __('site.saved_success'));
        return redirect()->back();
    }
    public function costs()
    {
        if (session('success')) {
            toast(session('success'), 'success');
        }
        return view('admin.settings.costs');
    }
    public function costsStore(Request $request)
    {
        $request->validate(
            [
                'fine' => 'required|min:0',
                'vat'=>'required|min:0',
                'operating_costs' => 'required|min:0',
                'expenses' => 'required|min:0',
                'driver_commission' => 'required|min:0|max:99',
                'company_commission' => 'required|min:0|max:99',
                'customer_fee' => 'required|min:0',
                'company_fee' => 'required|min:0',
            ]
        );
        $requestData =
        [
            'vat'   =>  $request->vat,
            'fine'              => $request->fine ,
            'operating_costs' => $request->operating_costs ,
            'expenses' => $request->expenses ,
            'driver_commission' => $request->driver_commission,
            'company_commission' => $request->company_commission,
            'customer_fee' => $request->customer_fee ,
            'company_fee' => $request->company_fee ,
        ];
        Setting($requestData)->save();
        session()->flash('success', __('site.saved_success'));
        return redirect()->back();
    }
    public function api()
    {
        if (session('success')) {
            toast(session('success'), 'success');
        }
        return view('admin.settings.api');
    }
    public function apiStore(Request $request)
    {
        dd($request);
    }

    public function theme(){
       return view('admin.settings.theme');
    }
    public function storeTheme(Request $request){
        $request->validate(
            [
                'light_top_menu_bg' => 'required',
                'light_top_menu_tc' => 'required',
                'light_side_menu_bg' => 'required',
                'light_side_menu_tc' => 'required',
                'light_side_menu_ttc' => 'required'
            ]
        );
        $requestData=[
                'light_top_menu_bg' => $request->light_top_menu_bg,
                'light_top_menu_tc' => $request->light_top_menu_tc,
                'light_side_menu_bg' => $request->light_side_menu_bg,
                'light_side_menu_tc' => $request->light_side_menu_tc,
                'light_side_menu_ttc' => $request->light_side_menu_ttc
        ];
        Setting($requestData)->save();
        session()->flash('success', __('site.saved_success'));
        return redirect()->back();
    }
}
