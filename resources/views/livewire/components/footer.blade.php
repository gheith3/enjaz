<footer id="app-footer" class="pt-5 pb-4">
    <div class="container text-center text-md-left">
        <div class="row text-center text-md-left">
            <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mt-3">
                <img src="{{ asset($settings->logo) }}" alt="Logo"
                     class="footer-logo">
            </div>

            <div class="col-md-2 col-lg-2 col-xl-2 mt-3">
                @foreach($this->getLinks()->take(4) as $text => $link)
                    <p class="text-end">
                        <a href="{{$link}}" class="text-white-50 link-text">
                            {{$text}}
                        </a>
                    </p>
                @endforeach
            </div>

            <div class="col-md-2 col-lg-2 col-xl-2 mt-3">
                @foreach($this->getLinks()->skip(4) as $text => $link)
                    <p class="text-end">
                        <a href="{{$link}}" class="text-white-50 link-text">
                            {{$text}}
                        </a>
                    </p>
                @endforeach
            </div>

            <div class="col-md-2 col-lg-2 col-xl-2 mt-3">
                @foreach($this->getAddressDetails() as $item)
                    <p class="text-end text-white-50">
                           {{$item['text']}}
                    </p>
                @endforeach
            </div>

            <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mt-3 d-flex align-items-center justify-content-center">
                <p class="mb-0 text-white-50">
        <span>
            جميع الحقوق محفوظة
        </span>
                    <span>
            {{ now()->year }}
        </span>
                </p>
            </div>
        </div>
    </div>
</footer>
