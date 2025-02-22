<div>
    <div id="content-us-section">
        <div>
            {{ view("components.layouts.nav") }}
        </div>

        <div class="container" style="min-height: 70vh; margin-top: 120px">
            <div class="row justify-content-center">
                <div class="col-md-11">
                    <div class="text-white">
                        <h1>تواصل معنا</h1>
                    </div>
                </div>
                <div class="col-md-5">
                    <div>
                        <div class="contact-us">
                            <form wire:submit.prevent="submit">
                                <div class="mt-3">
                                    <label class="text-white" for="name">الاسم</label>
                                    <input class="form-control" type="text" id="name" wire:model="name" required>
                                </div>
                                <div class="mt-3">
                                    <label class="text-white" for="email">البريد الإلكتروني</label>
                                    <input class="form-control" type="email" id="email" wire:model="email" required>
                                </div>
                                <div class="mt-3">
                                    <label class="text-white" for="message">الرسالة</label>
                                    <textarea class="form-control" id="message" wire:model="message" required></textarea>
                                </div>
                                <div class="mt-3">
                                    @if (session()->has('success'))
                                        <div class="alert alert-success">{{ session('success') }}</div>
                                    @endif
                                    <button type="submit" class="btn btn-md btn-primary" style="border: none; border-radius: 20px; width: 25%; background-color: #a88344;">ارسال</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-1"></div>
                <div class="col-md-5 pt-5">
                    <div class="map-container mt-3"
                         style="border-radius: 20px; overflow: hidden; margin-bottom: 20px; margin-top: 35px;">
                        <img src="{{ Storage::url($settings->map_image) }}" alt="Map" style="width: 100%; height: auto;">
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between ">

                            <p class="text-white d-flex align-items-center mb-3">
                                <img src="{{ asset('images/app/email.svg') }}" alt="Email Icon" height="16" width="16" class="me-2" style="margin-left: 10px;">
                                <a href="mailto:{{$settings->email}}" style="color: white; text-decoration: none;">
                                    {{$settings->email}}
                                </a>
                            </p>

                            <p class="text-white d-flex align-items-center mb-3">
                                <img src="{{ asset('images/app/phone.svg') }}" alt="Phone Icon" height="16" width="16" class="me-2" style="margin-left: 10px;">
                                <a href="tel:99555564" style="color: white; text-decoration: none;">99555564</a>
                            </p>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        @foreach($settings->socialMediaAccounts as $account)
                            <div class="col-auto text-center">
                                <div class="p-1">
                                    <a href="{{$account['url']}}" target="_blank">
                                        <img src="{{$account['icon']}}"
                                             alt="social media icon"
                                             height="32"
                                             width="32">
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        body {
            background-image: url("{{asset('images/app/ABG.svg')}}");
            background-size: cover;
            background-repeat: no-repeat;
            height: fit-content;
        }
    </style>
@endpush
