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
    public function generalStore(Request $request)
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
        $requestData = []; // $requestData = $request->except(['_token', '_method']);
        if ($request->has('title')) {
            $requestData['title'] = $request->title;
        }
        if ($request->has('canonical')) {
            $requestData['canonical'] = $request->canonical;
        }
        if ($request->has('keywords_ar')) {
            $requestData['keywords_ar'] = $request->keywords_ar;
        }
        if ($request->has('keywords_en')) {
            $requestData['keywords_en'] = $request->keywords_en;
        }
        if ($request->has('description_ar')) {
            $requestData['description_ar'] = $request->description_ar;
        }
        if ($request->has('description_en')) {
            $requestData['description_en'] = $request->description_en;
        }
        if ($request->has('og_site_name')) {
            $requestData['og_site_name'] = $request->og_site_name;
        }
        if ($request->has('og_title')) {
            $requestData['og_title'] = $request->og_title;
        }
        if ($request->has('og_type')) {
            $requestData['og_type'] = $request->og_type;
        }
        if ($request->has('og_url')) {
            $requestData['og_url'] = $request->og_url;
        }
        if ($request->has('og_description_ar')) {
            $requestData['og_description_ar'] = $request->og_description_ar;
        }
        if ($request->has('og_description_en')) {
            $requestData['og_description_en'] = $request->og_description_en;
        }
        if ($request->has('twitter_title')) {
            $requestData['twitter_title'] = $request->twitter_title;
        }
        if ($request->has('twitter_domain')) {
            $requestData['twitter_domain'] = $request->twitter_domain;
        }
        if ($request->has('twitter_card_ar')) {
            $requestData['twitter_card_ar'] = $request->twitter_card_ar;
        }
        if ($request->has('twitter_card_en')) {
            $requestData['twitter_card_en'] = $request->twitter_card_en;
        }
        if ($request->has('twitter_description_ar')) {
            $requestData['twitter_description_ar'] = $request->twitter_description_ar;
        }
        if ($request->has('twitter_description_en')) {
            $requestData['twitter_description_en'] = $request->twitter_description_en;
        }
        if ($request->og_image) {
            if (Setting('og_image') != 'uploads/logo.jpg' && Setting('logo') != 'uploads/logo.png') {
                if (file_exists(public_path(Setting('og_image')))) {
                    unlink(public_path(Setting('og_image')));
                }
            }
            $request->og_image->store('', ['disk' => 'public_uploads']);
            $requestData['og_image'] = 'uploads/' . $request->og_image->hashName();
        }
        Setting($requestData)->save();
        session()->flash('success', __('site.saved_success'));
        return redirect()->back();
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
