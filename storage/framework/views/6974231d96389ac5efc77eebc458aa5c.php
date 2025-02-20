<div>

    <div class="pt-5 pb-5"
         style="
         background: rgb(30,67,72);
         background: linear-gradient(90deg, rgba(30,67,72,1) 0%, rgba(45,103,111,1) 35%, rgba(55,126,136,1) 100%);">
        <div>
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('profile-slider', ['profiles' => $this->profiles]);

$__html = app('livewire')->mount($__name, $__params, 'lw-305343553-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
        </div>
        <div>
            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('public-figure', ['profiles' => $this->profiles]);

$__html = app('livewire')->mount($__name, $__params, 'lw-305343553-1', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
        </div>
    </div>
</div>
<?php /**PATH E:\Projects\enjaz\resources\views\livewire\profiles.blade.php ENDPATH**/ ?>