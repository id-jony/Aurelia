<fieldset class="mb-3" data-async="">
    <div class="bg-white rounded shadow-sm p-4 py-4 d-flex flex-column">
        <dl class="d-block m-0">
            <img src="{{ $product->primaryImage }}" alt="" width="230px">

            <div class="d2-grid py-3 ">
                <dt class="text-muted fw-normal">Sku</dt>
                <dd class="text-black">{{ $product->sku }}</dd>
            </div>
            <div class="d2-grid py-3 border-top">
                <dt class="text-muted fw-normal">Наименование</dt>
                <dd class="text-black">{{ $product->name }}</dd>
            </div>
            <div class="d2-grid py-3 border-top">
                <dt class="text-muted fw-normal">Категория</dt>
                <dd class="text-black">{{ $product->categories->name }}</dd>
            </div>
            <div class="d2-grid py-3 border-top">
                <dt class="text-muted fw-normal">Бренд</dt>
                <dd class="text-black">{{ $product->brand }}</dd>
            </div>
        </dl>

    </div>
</fieldset>
