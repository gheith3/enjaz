<x-layouts.app>
@section('title', 'ContactUs')

    <div>
    <div id="hero-section">

<div class="background-container">
    <img src="{{ asset($decorationImage) }}" alt="" class="bg-main">
    <div class="content-wrapper">
        <img src="{{ asset($backgroundImage) }}" alt="" class="bg-overlay">
        {{ view("components.layouts.nav") }}

        <div class="hero-content">
            <h1 class="hero-title">تواصل معنا</h1>
            <div>

            <div class="contact-us-section">
    

    <div class="container">
        <div class="row">
            <div class="col-md-6">
            </div>
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-4">
            <p> 1</p>
                </div>
                <div class="col-md-4">
            <p> 2</p>
                </div>
                <div class="col-md-4">
            <p> 3</p>
                </div>
                <div class="col-12">
            <p> Map</p>
                </div>
                </div>
            </div>
        </div>
    </div>
    </div>

            </div>
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





    </div>
    
    
 
    @push('styles')
<style>
#hero-section {
    position: relative;
    width: 100%;
    height: 421px;
    overflow: hidden;
}

.background-container {
    position: relative;
    width: 100%;
    height: 100%;
}

.bg-main {
    position: absolute;
    top: 0;
    left: 0;
    width: 2409.1px;
    height: 421px;
    object-fit: cover;
    z-index: 1;
}

.content-wrapper {
    position: relative;
    width: 100%;
    height: 100%;
    z-index: 2;
}

.bg-overlay {
    position: absolute;
    top: 45px;
    right: -303px;
    width: auto;
    height: 100%;
    z-index: 2;
}

.hero-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100%;
    max-width: 1200px;
    z-index: 3;
    text-align: center;
}

.hero-title {
    font-size: clamp(2.5rem, 8vw, 4rem);
    color: white;
    margin-bottom: 3rem;
    line-height: 1.2;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.contact-us {
    max-width: 600px;
    margin: 20px auto;
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
  
</x-layouts.app> 