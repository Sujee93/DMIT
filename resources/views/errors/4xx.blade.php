@extends('errors.layout')

@section('code', (string) $exception->getStatusCode())
@section('title', 'Something is not right')
@section('message', 'The request could not be completed. Please go back and try again.')
