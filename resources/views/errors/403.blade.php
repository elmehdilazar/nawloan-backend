@extends('errors::minimal')

@section('title', __('site.Forbidden'))
@section('code', '403')
@section('message', __('site.'.$exception->getMessage() ?: 'Forbidden'.''))
