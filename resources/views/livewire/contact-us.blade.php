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
                            <div class="mt-3 d-flex justify-content-end">
                                <button type="submit" class="btn btn-md btn-primary">ارسال</button>
                            </div>
                        </form>

                        <div class="social-links">
                            <h2 class="text-white">تابعنا على</h2>
                            <a class="text-white" href="https://instagram.com/injazat_oman"
                               target="_blank">Instagram</a>
                            <a class="text-white" href="https://twitter.com/injazat_oman" target="_blank">Twitter</a>
                            <a class="text-white" href="https://linkedin.com/in/injazat_oman"
                               target="_blank">LinkedIn</a>
                        </div>

                        <div class="contact-info">
                            <h2 class="text-white">معلومات الاتصال</h2>
                            <p class="text-white">الهاتف: 99555564</p>
                            <p class="text-white">البريد الإلكتروني: injazat2040@gmail.com</p>
                        </div>
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


                        <div class="col-12">map</div>
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
