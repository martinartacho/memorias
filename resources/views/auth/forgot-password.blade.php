<x-guest-layout>
    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-4 font-sans text-sm text-stone-600 bg-stone-50 border border-stone-200 rounded-lg p-3">
            {{ session('status') }}
        </div>
    @endif

    <form id="forgot-form" method="POST" action="{{ route('password.email') }}" class="p-8 space-y-6">
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
                   placeholder="{{ __('tu@email.com') }}"
                   required
                   autofocus>
            @error('email')
              <p class="mt-2 text-sm text-red-600 font-sans">{{ $message }}</p>
            @enderror
        </div>
    </form>

    <!-- Actions -->
    <div class="px-8 py-6 bg-stone-100 border-t border-stone-300">
        <div class="flex justify-between items-center">
            <a href="{{ route('login') }}" 
               class="font-sans text-xs tracking-wider uppercase text-stone-600 hover:text-stone-900">
                {{ __('Back to login') }}
            </a>
            
            <button type="submit" form="forgot-form"
                    class="px-6 py-3 font-sans text-xs tracking-wider uppercase text-white bg-stone-900 hover:bg-stone-800 transition-colors">
                {{ __('Email Password Reset Link') }}
            </button>
        </div>
    </div>
</x-guest-layout>
