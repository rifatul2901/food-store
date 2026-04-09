<div>
    <button 
        wire:click="storeCheckout" 
        wire:loading.attr="disabled"
        class="btn btn-primary w-100 rounded">

        <span wire:loading.remove wire:target="storeCheckout">
            Process to Payment
        </span>

        <span wire:loading wire:target="storeCheckout">
            <span class="spinner-border spinner-border-sm" role="status"></span>
            Processing...
        </span>

    </button>
</div>