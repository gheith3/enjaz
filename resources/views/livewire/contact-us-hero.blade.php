<div id="hero-section">

    <div class="background-container">
        <img src="{{ asset($decorationImage) }}" alt="" class="bg-main">
        <div class="content-wrapper">
            <img src="{{ asset($backgroundImage) }}" alt="" class="bg-overlay">
            {{ view("components.layouts.nav") }}

            <div class="hero-content">
                <h1 class="hero-title">تواصل معنا</h1>
            </div>
        </div>
        <div class="contact-us">
    @if (session()->has('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form wire:submit.prevent="submit">
        <div>
            <label for="name">الاسم</label>
            <input type="text" id="name" wire:model="name" required>
        </div>
        <div>
            <label for="email">البريد الإلكتروني</label>
            <input type="email" id="email" wire:model="email" required>
        </div>
        <div>
            <label for="message">الرسالة</label>
            <textarea id="message" wire:model="message" required></textarea>
        </div>
        <button type="submit">ارسال</button>
    </form>

    <div class="social-links">
        <h2>تابعنا على</h2>
        <a href="https://instagram.com/injazat_oman" target="_blank">Instagram</a>
        <a href="https://twitter.com/injazat_oman" target="_blank">Twitter</a>
        <a href="https://linkedin.com/in/injazat_oman" target="_blank">LinkedIn</a>
    </div>

    <div class="contact-info">
        <h2>معلومات الاتصال</h2>
        <p>الهاتف: 99555564</p>
        <p>البريد الإلكتروني: injazat2040@gmail.com</p>
    </div>
</div>

    </div>
</div>



