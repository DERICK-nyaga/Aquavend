@extends('layouts.app')

@section('title', 'New Restock Order')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
    
    {{-- Page Header --}}
    <header class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                Create Restock Order
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Log a new incoming bulk delivery for a target station.
            </p>
        </div>
        <a href="{{ route('restock-orders.index') }}" 
           class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm transition-colors hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            &larr; Back to Orders
        </a>
    </header>

    {{-- Validation Error Banner --}}
    @if ($errors->any())
        <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700 shadow-sm" role="alert">
            <div class="flex items-center gap-2 font-semibold text-red-800">
                <svg class="h-5 w-5 fill-current text-red-500" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <span>Please fix the following errors:</span>
            </div>
            <ul class="mt-2 list-inside list-disc space-y-1 text-red-600">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Restock Form Card --}}
    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="p-6 sm:p-8">
            <form action="{{ route('restock-orders.store') }}" method="POST">
                @csrf
                @include('restock-orders.form')
            </form>
        </div>
    </div>
</div>
@endsection