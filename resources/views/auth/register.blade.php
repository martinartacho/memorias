<x-guest-layout>
    <form id="register-form" method="POST" action="{{ route('register') }}" class="p-8 space-y-6">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block font-sans text-xs tracking-wider uppercase text-stone-600 mb-3">
                {{ __('Name') }} <span class="text-red-600">*</span>
            </label>
            <input type="text" 
                   id="name" 
                   name="name" 
                   value="{{ old('name') }}"
                   class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-stone-500 focus:border-stone-500 font-sans"
                   placeholder="{{ __('Tu nombre') }}"
                   required
                   autofocus
                   autocomplete="name">
            @error('name')
              <p class="mt-2 text-sm text-red-600 font-sans">{{ $message }}</p>
            @enderror
        </div>

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
                   placeholder="{{ __('tu@email.com') }}"
                   required
                   autocomplete="username">
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
                   placeholder="{{ __('••••••••') }}"
                   required
                   autocomplete="new-password">
            @error('password')
              <p class="mt-2 text-sm text-red-600 font-sans">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block font-sans text-xs tracking-wider uppercase text-stone-600 mb-3">
                {{ __('Confirm Password') }} <span class="text-red-600">*</span>
            </label>
            <input type="password" 
                   id="password_confirmation" 
                   name="password_confirmation" 
                   class="w-full px-4 py-3 border border-stone-300 rounded-lg focus:ring-2 focus:ring-stone-500 focus:border-stone-500 font-sans"
                   placeholder="{{ __('••••••••') }}"
                   required
                   autocomplete="new-password">
            @error('password_confirmation')
              <p class="mt-2 text-sm text-red-600 font-sans">{{ $message }}</p>
            @enderror
        </div>
    </form>

    <!-- Actions -->
    <div class="px-8 py-6 bg-stone-100 border-t border-stone-300">
        <div class="flex justify-between items-center">
            <a href="{{ route('login') }}" 
               class="font-sans text-xs tracking-wider uppercase text-stone-600 hover:text-stone-900">
                {{ __('Already registered?') }}
            </a>
            
            <button type="submit" form="register-form"
                    class="px-6 py-3 font-sans text-xs tracking-wider uppercase text-white bg-stone-900 hover:bg-stone-800 transition-colors">
                {{ __('Register') }}
            </button>
        </div>
    </div>
</x-guest-layout>
