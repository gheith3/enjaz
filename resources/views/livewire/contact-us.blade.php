<div>
    <div id="content-us-section">
        <div>
            {{ view("components.layouts.nav") }}
        </div>
        <div class="container" style="margin-top: 120px">
            <div class="row justify-content-center">
                <div class="col-12 text-white">
                    <h1>تواصل معنا</h1>
                </div>
                <div class="col-md-5">
                    <div class="contact-us">


                        @if (session()->has('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

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
                            <div class="mt-3 d-flex justify-content-start">
                                <button type="submit" class="btn btn-md btn-primary" style="border: none; border-radius: 20px; width: 25%; background-color: #a88344;">ارسال</button>
                            </div>
                        </form>



                        
                    </div>
                </div>
                <div class="col-md-2"></div>
                <div class="col-md-5">
                    <div class="row" style="margin-top: 40px">
                        @foreach($socialMedia as $link)
                            <div class="col-md-4 text-center">
                                <div class="border rounded-4 border-2 p-3">
                                    <img src="{{$link['image']}}"
                                         alt="{{$link['title']}}"
                                         height="32"
                                         width="32">

                                    <p dir="ltr" class="text-white p-0 m-0 mt-3 ">
                                        {{$link['account']}}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                        <div class="map-container" style="border-radius: 20px; overflow: hidden; margin-bottom: 20px; margin-top: 35px;">
                            <img src="{{ asset('images/app/Map.png') }}" alt="Map" style="width: 100%; height: auto;">
                        </div>
                        <div class="contact-info" style="margin-top: 35px; font-size: 20px;">
                            <div class="d-flex justify-content-between ">
                                
                                <p class="text-white d-flex align-items-center mb-3">
                                    <img src="{{ asset('images/app/email.svg') }}" alt="Email Icon" height="16" width="16" class="me-2" style="margin-left: 10px;"> 
                                    <a href="mailto:injazat2040@gmail.com" style="color: white; text-decoration: none;">injazat2040@gmail.com</a>
                                </p>

                                <p class="text-white d-flex align-items-center mb-3">
                                    <img src="{{ asset('images/app/phone.svg') }}" alt="Phone Icon" height="16" width="16" class="me-2" style="margin-left: 10px;"> 
                                    <a href="tel:99555564" style="color: white; text-decoration: none;">99555564</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center">
            <img style="max-width: 100%" src="{{asset('images/app/AchieversBG.svg')}}" alt="achievers">
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