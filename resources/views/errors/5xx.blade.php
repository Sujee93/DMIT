@extends('errors.layout')

@section('code', (string) ($exception->getStatusCode() ?? 500))
@section('title', 'Something went wrong')
@section('message', 'An unexpected error occurred. Nothing you did caused this - please try again. If it keeps happening, send the error code below to your administrator.')
@section('reference', \App\Exceptions\Handler::reference())
