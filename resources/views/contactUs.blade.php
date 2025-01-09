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
    min-height: 300px;
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
    width: 100%;
    height: 100%;
    object-fit: cover;
    z-index: 1;
}

.content-wrapper {
    position: relative;
    width: 100%;
    height: 100%;
    z-index: 2;
    padding: 1rem;
}

.bg-overlay {
    display: none;
}

.hero-content {
    position: relative;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100%;
    max-width: 1200px;
    z-index: 3;
    text-align: center;
    padding: 0 1rem;
}

.hero-title {
    font-size: clamp(2rem, 6vw, 4rem);
    color: white;
    margin-bottom: 2rem;
    line-height: 1.2;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.contact-us {
    max-width: 100%;
    margin: 20px auto;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.contact-us form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.contact-us label {
    margin-bottom: 5px;
    font-size: 1rem;
    font-weight: 500;
}

.contact-us input,
.contact-us textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 1rem;
}

.contact-us textarea {
    min-height: 120px;
    resize: vertical;
}

.contact-us button {
    padding: 12px 20px;
    background-color: #4CAF50;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 1rem;
    font-weight: 500;
    transition: background-color 0.3s ease;
}

.contact-us button:hover {
    background-color: #45a049;
}

.social-links {
    margin-top: 30px;
    text-align: center;
}

.social-links h2 {
    margin-bottom: 15px;
    font-size: 1.5rem;
    color: #333;
}

.social-links a {
    display: inline-block;
    margin: 5px 10px;
    padding: 8px 15px;
    background-color: #f0f0f0;
    border-radius: 20px;
    color: #333;
    text-decoration: none;
    transition: all 0.3s ease;
}

.social-links a:hover {
    background-color: #e0e0e0;
    transform: translateY(-2px);
}

.contact-info {
    margin-top: 30px;
    text-align: center;
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.contact-info h2 {
    margin-bottom: 15px;
    font-size: 1.5rem;
    color: #333;
}

.contact-info p {
    margin: 10px 0;
    font-size: 1rem;
    color: #666;
}

.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 8px;
    text-align: center;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

/* Media Queries */
@media (max-width: 768px) {
    .contact-us {
        margin: 10px;
        padding: 15px;
    }

    .hero-title {
        font-size: 2rem;
        margin-bottom: 1.5rem;
    }

    .social-links a {
        display: block;
        margin: 10px auto;
        max-width: 200px;
    }

    .contact-info {
        margin: 20px 10px;
    }

    .contact-us button {
        width: 100%;
    }

    .contact-us-section .container {
        padding: 0 15px;
    }

    .contact-us form > div {
        margin-bottom: 15px;
    }
}

@media (max-width: 480px) {
    #hero-section {
        min-height: 250px;
    }

    .hero-title {
        font-size: 1.75rem;
    }

    .contact-info h2,
    .social-links h2 {
        font-size: 1.25rem;
    }

    .contact-us input,
    .contact-us textarea {
        padding: 10px;
    }

    .contact-us label {
        font-size: 0.9rem;
    }

    .contact-info p {
        font-size: 0.9rem;
    }
}

/* Add smooth transitions */
.contact-us button,
.social-links a {
    transition: all 0.3s ease-in-out;
}

/* Improve touch targets */
.contact-us input,
.contact-us textarea,
.contact-us button,
.social-links a {
    min-height: 44px;
}
</style>
@endpush
  
</x-layouts.app> 