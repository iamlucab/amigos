@extends('adminlte::page')
@section('title', 'Add Product - Staff')

@section('content')
<div class="container-fluid p-2 p-md-3">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm rounded-4 border-0">
                {{-- Header --}}
                <div class="card-header bg-primary text-white rounded-top-4 py-3 d-flex align-items-center">
                    <i class="bi bi-plus me-2 fs-5"></i>
                    <h6 class="mb-0 fs-5" style="color:white !important ">Add New Product</h4>
                </div>

                {{-- Form --}}
                <div class="card-body">
                    <form action="{{ route('staff.products.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf

                        {{-- Product Name --}}
                        <div class="form-floating mb-3">
                            <input type="text" name="name" id="name" class="form-control rounded-3" placeholder="Product Name" value="{{ old('name') }}" required>
                          
                        </div>

                        {{-- Description --}}
                        <div class="mb-3">
                            <label class="form-label small">Description</label>
                            <textarea name="description" id="description" class="form-control rounded-3" rows="4" placeholder="Enter product description with paragraph formatting...">{{ old('description') }}</textarea>
                            <div class="form-text">Use line breaks to separate paragraphs. Formatting will be preserved when displaying.</div>
                        </div>

                        {{-- Price + Cashback --}}
                        <div class="row g-3">
                            <div class="col-sm-6 form-floating">
                                <input type="number" step="0.01" name="price" id="price" class="form-control rounded-3" placeholder="Price" value="{{ old('price') }}" required>
                               
                            </div>
                            <div class="col-sm-6 form-floating">
                                <input type="number" step="0.01" name="cashback_amount" id="cashback_amount" class="form-control rounded-3" placeholder="Cashback" value="{{ old('cashback_amount') }}" required>
                           
                            </div>
                        </div>

                        {{-- Cashback Level --}}
                        <div class="mt-3">
                            <label for="cashback_max_level" class="form-label fw-bold small text-muted">Maximum Cashback Level</label>
                            <select name="cashback_max_level" id="cashback_max_level" class="form-select rounded-3" required>
                                @for ($i = 1; $i <= 11; $i++)
                                    <option value="{{ $i }}" {{ old('cashback_max_level', 3) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                            <small class="text-muted">Levels to distribute cashback</small>
                        </div>

                        {{-- Cashback Distribution --}}
                        <div class="card mt-3 border-0 shadow-sm">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0">Cashback Level Distribution</h6>
                                <small class="text-muted">Leave empty for auto distribution</small>
                            </div>
                            <div class="card-body p-2" id="cashback-level-inputs"></div>
                        </div>

                        {{-- Cashback Preview --}}
                        <div class="card mt-3 border-0 shadow-sm">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0">Cashback Preview</h6>
                            </div>
                            <div class="card-body p-2">
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered align-middle text-center mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Level</th>
                                                <th>Amount (₱)</th>
                                                <th>Type</th>
                                            </tr>
                                        </thead>
                                        <tbody id="cashback-preview-table"></tbody>
                                        <tfoot>
                                            <tr class="fw-bold">
                                                <td>Total</td>
                                                <td id="cashback-preview-total">₱0.00</td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Member Benefits --}}
                        <div class="card mt-3 border-0 shadow-sm">
                            <div class="card-header bg-light py-2">
                                <h6 class="mb-0"><i class="bi bi-tag me-1"></i>Member Benefits</h6>
                                <small class="text-muted">Applied during checkout</small>
                            </div>
                            <div class="card-body p-2">
                                <div class="alert alert-info small">
                                    <i class="bi bi-info-circle me-1"></i> Discounts apply only for members
                                </div>

                                {{-- Discount --}}
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <label class="form-label small">Discount Value</label>
                                        <input type="number" step="0.01" name="discount_value" id="discount_value" class="form-control rounded-3" placeholder="Discount Value" value="{{ old('discount_value') }}">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label small">Discount Type</label>
                                        <select name="discount_type" id="discount_type" class="form-select rounded-3">
                                            <option value="">-- None --</option>
                                            <option value="flat" @selected(old('discount_type') == 'flat')>Flat</option>
                                            <option value="percent" @selected(old('discount_type') == 'percent')>Percentage</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- Promo --}}
                                <div class="mt-3">
                                    <label class="form-label small">Promo Code</label>
                                    <input type="text" name="promo_code" id="promo_code" class="form-control rounded-3" placeholder="Promo Code" value="{{ old('promo_code') }}">
                                </div>

                                {{-- Price Preview --}}
                                <div id="price-preview" class="mt-3 p-3 rounded bg-light small">
                                    <div class="d-flex justify-content-between">
                                        <span>Original Price:</span><span id="original-price">₱0.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between text-success">
                                        <span>Discount:</span><span id="discount-amount">-₱0.00</span>
                                    </div>
                                    <hr class="my-2">
                                    <div class="d-flex justify-content-between fw-bold">
                                        <span>Final Price:</span><span id="final-price">₱0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Category & Unit --}}
                        <div class="row g-3 mt-3">
                            <div class="col-sm-6">
                                <label class="form-label small">Category</label>
                                <select name="category_id" class="form-select rounded-3" required>
                                    <option value="">-- Select --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label small">Unit</label>
                                <select name="unit_id" class="form-select rounded-3" required>
                                    <option value="">-- Select --</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}" @selected(old('unit_id') == $unit->id)>{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Sizes & Colors --}}
                        {{-- <div class="mt-3">
                            <label class="form-label small">Available Sizes</label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($sizes as $size)
                                    <label class="form-check-label border p-2 rounded small">
                                        <input class="form-check-input me-1" type="checkbox" name="sizes[]" value="{{ $size }}" {{ in_array($size, old('sizes', [])) ? 'checked' : '' }}>
                                        {{ $size }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="form-label small">Available Colors</label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($colors as $color)
                                    <label class="form-check-label border p-2 rounded small">
                                        <input class="form-check-input me-1" type="checkbox" name="colors[]" value="{{ $color }}" {{ in_array($color, old('colors', [])) ? 'checked' : '' }}>
                                        {{ $color }}
                                    </label>
                                @endforeach
                            </div>
                        </div> --}}

                        {{-- Thumbnail --}}
                        <div class="mt-3">
                            <label class="form-label small">Thumbnail Image</label>
                            <input type="file" name="thumbnail" class="form-control rounded-3" accept="image/*">
                            <img id="thumbnail-preview" class="img-thumbnail mt-2" style="max-height: 100px; display: none;">
                        </div>

                        {{-- Gallery --}}
                        <div class="mt-3">
                            <label class="form-label small">Gallery Images</label>
                            <input type="file" name="gallery[]" multiple class="form-control rounded-3" accept="image/*">
                        </div>
                        <div id="gallery-preview" class="d-flex flex-wrap gap-2 mt-2"></div>
                        <input type="hidden" name="gallery_order" id="gallery_order">

                        {{-- Stock --}}
                        <div class="form-floating mt-3">
                            <input type="number" name="stock_quantity" id="stock_quantity" class="form-control rounded-3" placeholder="Stock Quantity" value="{{ old('stock_quantity') }}" required>
                            <label for="stock_quantity">Stock Quantity</label>
                        </div>

                        {{-- Sticky Action Bar --}}
                        <div class="position-sticky bottom-0 bg-white py-2 mt-3 border-top d-flex gap-2">
                            <a href="{{ route('staff.products.index') }}" class="btn btn-secondary flex-fill rounded-pill">
                                <i class="bi bi-arrow-left me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-success flex-fill rounded-pill">
                                <i class="bi bi-save me-1"></i> Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@include('partials.mobile-footer')

{{-- JS remains same as your original with cashback + preview + select2 --}}

@section('js')
<script>
// Generate cashback level inputs based on max_level
function generateCashbackLevelInputs() {
    const maxLevel = parseInt($('#cashback_max_level').val());
    const container = $('#cashback-level-inputs');
    container.empty();
    
    const row = $('<div class="row"></div>');
    
    for (let i = 1; i <= maxLevel; i++) {
        const col = $(`
            <div class="col-md-4 mb-3">
                <div class="form-group">
                    <label for="cashback_level_${i}">Level ${i} Bonus (₱)</label>
                    <input type="number" step="0.01" min="0" class="form-control cashback-level-bonus"
                        id="cashback_level_${i}" name="cashback_level_bonuses[${i}]"
                        placeholder="Auto">
                </div>
            </div>
        `);
        
        row.append(col);
    }
    
    container.append(row);
}

// Update cashback preview based on current values
function updateCashbackPreview() {
    const cashbackAmount = parseFloat($('#cashback_amount').val()) || 0;
    const maxLevel = parseInt($('#cashback_max_level').val()) || 1;
    
    // Collect custom values
    const levelBonuses = {};
    $('.cashback-level-bonus').each(function() {
        const level = $(this).attr('id').replace('cashback_level_', '');
        const value = $(this).val();
        if (value !== '' && !isNaN(parseFloat(value))) {
            levelBonuses[level] = parseFloat(value);
        }
    });
    
    // Send to server for calculation
    $.ajax({
        url: '{{ route("staff.products.preview-cashback") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            cashback_amount: cashbackAmount,
            cashback_max_level: maxLevel,
            cashback_level_bonuses: levelBonuses
        },
        success: function(response) {
            const previewTable = $('#cashback-preview-table');
            previewTable.empty();
            
            for (let i = 1; i <= maxLevel; i++) {
                const amount = response.cashbacks[i] || 0;
                const isCustom = levelBonuses[i] !== undefined;
                
                previewTable.append(`
                    <tr>
                        <td>Level ${i}</td>
                        <td>₱${amount.toFixed(2)}</td>
                        <td>
                            ${isCustom ?
                                '<span class="badge badge-info">Custom</span>' :
                                '<span class="badge badge-secondary">Auto</span>'}
                        </td>
                    </tr>
                `);
            }
            
            $('#cashback-preview-total').text(`₱${response.total.toFixed(2)}`);
        }
    });
}

// Initialize cashback level inputs and preview
$(function() {
    generateCashbackLevelInputs();
    updateCashbackPreview();
    
    // Event listeners for cashback configuration
    $('#cashback_max_level').on('change', function() {
        generateCashbackLevelInputs();
        updateCashbackPreview();
    });
    
    $('#cashback_amount').on('input', updateCashbackPreview);
    
    $(document).on('input', '.cashback-level-bonus', updateCashbackPreview);
    
    // Price preview calculation
    function updatePricePreview() {
        const originalPrice = parseFloat($('#price').val()) || 0;
        const discountValue = parseFloat($('#discount_value').val()) || 0;
        const discountType = $('#discount_type').val();
        
        let discountAmount = 0;
        let finalPrice = originalPrice;
        
        // Calculate discount based on type
        if (discountType === 'flat' && discountValue > 0) {
            discountAmount = discountValue;
            finalPrice = Math.max(0, originalPrice - discountAmount);
        } else if (discountType === 'percent' && discountValue > 0) {
            discountAmount = (originalPrice * discountValue) / 100;
            finalPrice = originalPrice - discountAmount;
        }
        
        // Update the preview
        $('#original-price').text(`₱${originalPrice.toFixed(2)}`);
        $('#discount-amount').text(`-₱${discountAmount.toFixed(2)}`);
        $('#final-price').text(`₱${finalPrice.toFixed(2)}`);
        
        // Show/hide the preview based on whether there's a price entered
        if (originalPrice > 0) {
            $('#price-preview').show();
        } else {
            $('#price-preview').hide();
        }
    }
    
    // Initial update
    updatePricePreview();
    
    // Update on change of relevant fields
    $('#price, #discount_value, #discount_type').on('change input', updatePricePreview);
    
    // Thumbnail preview functionality
    $('input[name="thumbnail"]').on('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#thumbnail-preview').attr('src', e.target.result).show();
            };
            reader.readAsDataURL(file);
        } else {
            $('#thumbnail-preview').hide();
        }
    });
    
    // Gallery preview functionality
    $('input[name="gallery[]"]').on('change', function(e) {
        const files = e.target.files;
        const previewContainer = $('#gallery-preview');
        previewContainer.empty();
        
        if (files.length > 0) {
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewContainer.append(
                            `<img src="${e.target.result}" class="img-thumbnail" style="height: 80px;">`
                        );
                    };
                    reader.readAsDataURL(file);
                }
            }
            previewContainer.show();
        } else {
            previewContainer.hide();
        }
    });
});
</script>
@endsection
