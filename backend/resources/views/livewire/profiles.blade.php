<div>

    <div class="pt-5 pb-5"
         style="
         background: rgb(30,67,72);
         background: linear-gradient(90deg, rgba(30,67,72,1) 0%, rgba(45,103,111,1) 35%, rgba(55,126,136,1) 100%);">
        <div>
            <livewire:profile-slider
                :profiles="$this->profiles"/>
        </div>
        <div>
            <livewire:public-figure
                :profiles="$this->profiles"/>
        </div>
    </div>
</div>
