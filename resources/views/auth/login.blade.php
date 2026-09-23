@extends('layouts.app')
@section('title', 'Login — Aquavend')

@section('content')
<div class="min-h-[calc(100vh-12rem)] flex items-center justify-center px-4 py-8">
    <div class="w-full max-w-md bg-white rounded-2xl p-8 shadow-xl border border-slate-200/80">
        
        {{-- Header / Brand Logo --}}
        <div class="text-center mb-8">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-sky-500 via-sky-400 to-cyan-300 flex items-center justify-center text-slate-950 text-xl font-black shadow-md shadow-sky-500/20 mx-auto mb-3">
                <i class="bi bi-droplet-fill"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight m-0">Log in to Aquavend</h1>
            <p class="text-xs text-slate-500 mt-1 m-0">Enter your email and password to access your dashboard</p>
        </div>

        {{-- Error Alert Banner --}}
        @if ($errors->any())
            <div class="mb-6 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold flex items-center gap-2">
                <i class="bi bi-exclamation-circle-fill text-rose-500 text-sm shrink-0"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        {{-- Form --}}
        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf
            
            {{-- Email --}}
            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Email Address</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="bi bi-envelope text-sm"></i>
                    </div>
                    <input 
                        id="email"
                        type="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        autofocus
                        placeholder="you@example.com"
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border @error('email') border-rose-300 bg-rose-50/30 @else border-slate-200 bg-slate-50/50 @enderror text-sm text-slate-900 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all outline-none"
                    >
                </div>
                @error('email')
                    <p class="text-rose-600 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="bi bi-lock text-sm"></i>
                    </div>
                    <input 
                        id="password"
                        type="password" 
                        name="password" 
                        required 
                        placeholder="••••••••"
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border @error('password') border-rose-300 bg-rose-50/30 @else border-slate-200 bg-slate-50/50 @enderror text-sm text-slate-900 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 transition-all outline-none"
                    >
                </div>
                @error('password')
                    <p class="text-rose-600 text-xs mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit Button --}}
            <button 
                type="submit" 
                class="w-full py-2.5 px-4 rounded-xl font-bold text-xs text-white bg-sky-600 hover:bg-sky-500 shadow-md shadow-sky-600/20 active:scale-[0.98] transition-all cursor-pointer flex items-center justify-center gap-2"
            >
                <span>Log In</span>
                <i class="bi bi-arrow-right text-sm"></i>
            </button>
        </form>

        {{-- Footer Link --}}
        <p class="mt-6 text-center text-xs text-slate-500 m-0">
            Don't have an account? 
            <a href="{{ route('register') }}" class="font-bold text-sky-600 hover:text-sky-800 transition-colors no-underline">Register</a>
        </p>

    </div>
</div>
@endsection