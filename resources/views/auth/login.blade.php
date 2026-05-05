<x-guest-layout>
    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-4 font-sans text-sm text-stone-600 bg-stone-50 border border-stone-200 rounded-lg p-3">
            {{ session('status') }}
        </div>
    @endif

    <form id="login-form" method="POST" action="{{ route('login') }}" class="p-8 space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block font-sans text-xs tracking-wider uppercase text-stone-600 mb-3">
                {{ __('Email') }} <span class="text-red-600">*</span>
            </label>
            <input type="email" 
                   id="email" 
                   name="email" 
                   value="{{ old('email') }}"
                   class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-stone-500 focus:border-stone-500 font-sans"
                   placeholder="admin@memorias.com"
                   required
                   autocomplete="email"
                   autofocus>
            @error('email')
              <p class="mt-2 text-sm text-red-600 font-sans">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block font-sans text-xs tracking-wider uppercase text-stone-600 mb-3">
                {{ __('Password') }} <span class="text-red-600">*</span>
            </label>
            <input type="password" 
                   id="password" 
                   name="password" 
                   class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-stone-500 focus:border-stone-500 font-sans"
                   placeholder="••••••••"
                   required
                   autocomplete="current-password">
            @error('password')
              <p class="mt-2 text-sm text-red-600 font-sans">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input type="checkbox" 
                   id="remember" 
                   name="remember" 
                   class="mr-2 text-stone-600 focus:ring-stone-500"
                   {{ old('remember') ? 'checked' : '' }}>
            <label for="remember" class="font-sans text-sm text-stone-600">
                {{ __('Remember me') }}
            </label>
        </div>
    </form>

    <!-- Actions -->
    <div class="px-8 py-6 bg-stone-100 border-t border-stone-300">
        <div class="flex justify-between items-center">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" 
                   class="font-sans text-xs tracking-wider uppercase text-stone-600 hover:text-stone-900">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
            
            <button type="submit" form="login-form"
                    class="px-6 py-3 font-sans text-xs tracking-wider uppercase text-white bg-stone-900 hover:bg-stone-800 transition-colors">
                {{ __('Log in') }}
            </button>
        </div>
    </div>
</x-guest-layout>
