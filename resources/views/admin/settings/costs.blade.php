@extends('layouts.admin.app')
@section('title',' | ' . __('site.costs_settings'))
@section('content')
<h2 class="section-title mb-5">@lang('site.costs_settings')</h2>
<form action="{{route('admin.setting.costs.store')}}" method="post">
    @csrf
    @method('post')
    <div class="row">
        <div class="col-xl-7 col-lg-9 co-12">
            <div class="input-group">
                <label for="customer_fee">@lang('site.customer_fee')</label>
                <div class="input-group">
                    <input type="number" min="0" step="1" name="customer_fee" id="customer_fee"
                           placeholder="@lang('site.customer_fee')"
                           value="{{setting('customer_fee')!=''?setting('customer_fee'):''}}">
                    <div class="input-group-prepend">
                        <span class="input-group-text">{{setting('currency_atr') !='' ? setting('currency_atr') :''}}</span>
                    </div>
                </div>
                @error('customer_fee')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
            <div class="input-group">
                <label for="company_fee">@lang('site.company_fee')</label>
                <div class="input-group">
                    <input type="number" min="0" step="1" name="company_fee" id="company_fee"
                           placeholder="@lang('site.company_fee')"
                           value="{{setting('company_fee')!=''?setting('company_fee'):''}}">
                    <div class="input-group-prepend">
                        <span class="input-group-text">{{setting('currency_atr') !='' ? setting('currency_atr') :''}}</span>
                    </div>
                </div>
                @error('company_fee')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
            <div class="input-group">
                <label for="" class="">@lang('site.driver_commission')</label>
                <div class="input-group">
                    <input type="number" min="0" max="99" step="1" name="driver_commission" id="driver_commission"
                           placeholder="@lang('site.driver_commission')"
                           value="{{setting('driver_commission')!=''?setting('driver_commission'):''}}">
                    <div class="input-group-prepend">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
                @error('driver_commission')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
            <div class="input-group">
                <label for="company_commission">@lang('site.company_commission')</label>
                <div class="input-group">
                    <input type="number" min="0"max="99" step="1" name="company_commission" id="company_commission"
                           placeholder="@lang('site.company_commission')"
                           value="{{setting('company_commission')!=''?setting('company_commission'):''}}">
                    <div class="input-group-prepend">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
                @error('company_commission')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
            <div class="input-group">
                <label for="vat">@lang('site.vat')</label>
                <div class="input-group">
                    <input type="number" min="0"max="99" step="1" name="vat" id="vat"
                           placeholder="@lang('site.vat')"
                           value="{{setting('vat')!=''?setting('vat'):''}}">
                    <div class="input-group-prepend">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
                @error('vat')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
            <div class="input-group">
                <label for="fine">@lang('site.fine')</label>
                <div class="input-group">
                    <input type="number" min="0" step="1" name="fine" id="fine"
                           placeholder="@lang('site.fine')"
                           value="{{setting('fine')!=''?setting('fine'):''}}">
                    <div class="input-group-prepend">
                        <span class="input-group-text">{{setting('currency_atr') !='' ? setting('currency_atr') : ''}}</span>
                    </div>
                </div>
                @error('fine')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
            <div class="input-group">
                <label for="liter_price">@lang('site.liter_price')</label>
                <div class="input-group">
                    <input type="number" min="0" step="1" name="liter_price" id="liter_price"
                           placeholder="@lang('site.liter_price')"
                           value="{{setting('liter_price')!=''?setting('liter_price'):''}}">
                    <div class="input-group-prepend">
                        <span class="input-group-text">{{setting('currency_atr') !='' ? setting('currency_atr') : ''}}</span>
                    </div>
                </div>
                @error('liter_price')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
            <div class="input-group">
                <label for="avg_fuel_consumption_per_10_km">@lang('site.avg_fuel_consumption_per_10_km')</label>
                <div class="input-group">
                    <input type="number" min="0" step="1" name="avg_fuel_consumption_per_10_km" id="avg_fuel_consumption_per_10_km"
                           placeholder="@lang('site.avg_fuel_consumption_per_10_km')"
                           value="{{setting('avg_fuel_consumption_per_10_km')!=''?setting('avg_fuel_consumption_per_10_km'):''}}">
                    <div class="input-group-prepend">
                        <span class="input-group-text">@lang('site.liter')</span>
                    </div>
                </div>
                @error('avg_fuel_consumption_per_10_km')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
            <div class="input-group">
                <label for="diesel_cost_per_km">@lang('site.diesel_cost_per_km')</label>
                <div class="input-group">
                    <input type="number" min="0" step="0.01" name="diesel_cost_per_km" id="diesel_cost_per_km"
                           placeholder="@lang('site.diesel_cost_per_km')"
                           value="{{setting('diesel_cost_per_km')!=''?setting('diesel_cost_per_km'):''}}">
                    <div class="input-group-prepend">
                        <span class="input-group-text">{{setting('currency_atr') !='' ? setting('currency_atr') :''}}</span>
                    </div>
                </div>
                @error('diesel_cost_per_km')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
            <div class="input-group">
                <label for="operating_costs">@lang('site.operating_costs')</label>
                <div class="input-group">
                    <input type="number" min="0" step="1" name="operating_costs" id="operating_costs"
                           placeholder="@lang('site.operating_costs')"
                           value="{{setting('operating_costs')!=''?setting('operating_costs'):''}}">
                    <div class="input-group-prepend">
                        <span class="input-group-text">{{setting('currency_atr') !='' ? setting('currency_atr') : ''}}</span>
                    </div>
                </div>
                @error('operating_costs')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
            <div class="input-group">
                <label for="expenses">@lang('site.expenses')</label>
                <div class="input-group">
                    <input type="number" min="0" step="1" name="expenses" id="expenses"
                           placeholder="@lang('site.expenses')"
                           value="{{setting('expenses')!=''?setting('expenses'):''}}">
                    <div class="input-group-prepend">
                        <span class="input-group-text">{{setting('currency_atr') !='' ? setting('currency_atr') : ''}}</span>
                    </div>
                </div>
                @error('expenses')
                <span class="text-danger"> {{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-navy min-width-170 mt-4" title="@lang('site.save')">@lang('site.save')</button>
</form>
@endsection
