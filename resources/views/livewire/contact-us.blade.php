<div class="contact-us">
    <h1>تواصل معنا</h1>

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

@push('styles')
<style>
.contact-us {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.contact-us h1 {
    text-align: center;
}

.contact-us form {
    display: flex;
    flex-direction: column;
}

.contact-us label {
    margin-bottom: 5px;
}

.contact-us input,
.contact-us textarea {
    margin-bottom: 15px;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.contact-us button {
    padding: 10px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.contact-us .social-links {
    margin-top: 20px;
    text-align: center;
}

.contact-us .contact-info {
    margin-top: 20px;
    text-align: center;
}
</style>
@endpush
