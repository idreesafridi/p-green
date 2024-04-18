@extends('errors::minimal')

@section('title', __('Server Error'))
@section('code', '500')


@section('message')
    @if(isset($exception) && !empty($exception->getMessage()))
        {{ $exception->getMessage() }}
    @else
    Server Error
    @endif
@endsection
{{-- @section('message', __('Server Error')) --}}
