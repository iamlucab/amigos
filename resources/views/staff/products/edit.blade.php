@extends('adminlte::page')
@section('title', 'Edit Product - Staff')

@section('content')
<div class="container-fluid p-2">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-sm-12">
            <div class="card shadow-sm rounded-4">
                <div class="card-header bg-primary text-white rounded-top-4">
                    <h4 class="mb-0"><i class="bi bi-pencil me-2"></i> Edit Product</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('staff.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" name="name" class="form-control rounded-3" value="{{ old('name', $product->name) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" rows="4" class="form-control rounded-3" placeholder="Enter product description with paragraph formatting...">{{ old('description', $product->description) }}</textarea>
                            <div class="form-text">Use line breaks to separate paragraphs. Formatting will be preserved when displaying.</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Price (₱)</label>
                                <input type="number" name="price" step="0.01" class="form-control rounded-3" value="{{ old('price', $product->price) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Total Cashback (₱)</label>
                                <input type="number" name="cashback_amount" id="cashback_amount" step="0.01" class="form-control rounded-3" value="{{ old('cashback_amount', $product->cashback_amount) }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cashback_max_level" class="form-label">Maximum Cashback Level</label>
                                <select name="cashback_max_level" id="cashback_max_level" class="form-select rounded-3" required>
                                    @for ($i = 1; $i <= 11; $i++)
                                        <option value="{{ $i }}" {{ old('cashback_max_level', $product->cashback_max_level) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                                <small class="text-muted">Maximum number of levels to distribute cashback</small>
                            </div>
                        </div>
                        
                        <div class="card mt-3 mb-4">
                            <div class="card-header bg-light">
                                <h5>Cashback Level Distribution</h5>
                                <p class="text-muted mb-0">Leave fields empty to auto-distribute the remaining amount</p>
                            </div>
                            <div class="card-body">
                                <div id="cashback-level-inputs">
                                    <!-- Level inputs will be generated here -->
                                </div>
                            </div>
                        </div>
                        
                        <div class="card mt-3 mb-4">
                            <div class="card-header bg-light">
                                <h5>Cashback Preview</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Level</th>
                                                <th>Amount (₱)</th>
                                                <th>Type</th>
                                            </tr>
                                        </thead>
                                        <tbody id="cashback-preview-table">
                                            <!-- Preview data will be generated here -->
                                        </tbody>
                                        <tfoot>
                                            <tr class="font-weight-bold">
                                                <td>Total</td>
                                                <td id="cashback-preview-total">₱0.00</td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Category</label>
                                <select name="category_id" class="form-select rounded-3" required>
                                    <option value="">-- Select Category --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" @selected($product->category_id == $category->id)>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Unit</label>
                                <select name="unit_id" class="form-select rounded-3" required>
                                    <option value="">-- Select Unit --</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}" @selected($product->unit_id == $unit->id)>{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Thumbnail Image</label>
                            @if ($product->thumbnail)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $product->thumbnail) }}" class="img-thumbnail" style="max-height: 100px;" alt="Current Thumbnail">
                                </div>
                            @endif
                            <input type="file" name="thumbnail" class="form-control rounded-3">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Gallery Images</label>
                            <input type="file" name="gallery[]" multiple class="form-control rounded-3">
                            @php
                                // Ensure gallery is properly handled
                                $gallery = $product->gallery ?? [];
                                // Make sure it's an array
                                if (!is_array($gallery)) {
                                    $gallery = [];
                                }
                            @endphp
                            @if (count($gallery) > 0)
                                <div class="mt-2">
                                    <label class="form-label">Current Gallery Images:</label>
                                    <div class="d-flex flex-wrap gap-2" id="gallery-container">
                                        @foreach ($gallery as $index => $image)
                                            @if($image)
                                                <div class="position-relative" id="gallery-item-{{ $index }}">
                                                    <img src="{{ asset('storage/' . $image) }}" alt="Gallery Image" class="img-thumbnail" style="height: 80px;">
                                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0" onclick="removeGalleryImage({{ $index }})">
                                                        <i class="bi bi-x"></i>
                                                    </button>
                                                    <input type="hidden" name="existing_gallery[]" value="{{ $image }}" id="existing_gallery_{{ $index }}">
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Stock Quantity</label>
                            <input type="number" name="stock_quantity" class="form-control rounded-3" value="{{ old('stock_quantity', $product->stock_quantity) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Attributes (optional)</label>
                            <select name="attributes" class="form-select rounded-3">
                                <option value="">-- Select --</option>
                                @foreach(['S', 'M', 'L', 'XL', 'XXL', 'Corner', 'Round', 'Red', 'Blue', 'Green', 'Yellow', 'Black', 'White', 'Others'] as $option)
                                    <option value="{{ $option }}" @selected(old('attributes', $product->attributes) == $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="card mt-3 mb-4">
                            <div class="card-header bg-light">
                                <h5><i class="bi bi-tag me-2"></i>Member Benefits</h5>
                                <p class="text-muted mb-0">These benefits are exclusively for members during checkout</p>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i> These discounts and promotions will only apply to members during checkout.
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Discount Value</label>
                                        <input type="number" step="0.01" id="discount_value" name="discount_value" class="form-control rounded-3" value="{{ old('discount_value', $product->discount_value) }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Discount Type</label>
                                        <select name="discount_type" id="discount_type" class="form-select rounded-3">
                                            <option value="">-- None --</option>
                                            <option value="flat" @selected(old('discount_type', $product->discount_type) == 'flat')>Flat</option>
                                            <option value="percent" @selected(old('discount_type', $product->discount_type) == 'percent')>Percentage</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Promo Code</label>
                                    <input type="text" id="promo_code" name="promo_code" class="form-control rounded-3" value="{{ old('promo_code', $product->promo_code) }}">
                                </div>
                                
                                <div id="price-preview" class="mt-3 p-3 border rounded bg-light">
                                    <h6 class="mb-2">Price Preview for Members</h6>
                                    <div class="d-flex justify-content-between">
                                        <span>Original Price:</span>
                                        <span id="original-price">₱{{ number_format($product->price, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between text-success">
                                        <span>Discount:</span>
                                        <span id="discount-amount">-₱0.00</span>
                                    </div>
                                    <hr class="my-2">
                                    <div class="d-flex justify-content-between fw-bold">
                                        <span>Final Price for Members:</span>
                                        <span id="final-price">₱{{ number_format($product->price, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('staff.products.index') }}" class="btn btn-secondary rounded-pill px-4">
                                <i class="bi bi-arrow-left me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary rounded-pill px-4">
                                <i class="bi bi-save me-1"></i> Update Product
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

@section('js')
<script>
// Generate cashback level inputs based on max_level
function generateCashbackLevelInputs() {
    const maxLevel = parseInt($('#cashback_max_level').val());
    const container = $('#cashback-level-inputs');
    container.empty();
    
    const row = $('<div class="row"></div>');
    
    for (let i = 1; i <= maxLevel; i++) {
        // Get existing value if available
        let existingValue = '';
        @if($product->cashback_level_bonuses)
            const levelBonuses = @json($product->cashback_level_bonuses);
            if (levelBonuses && levelBonuses[i]) {
                existingValue = levelBonuses[i];
            }
        @endif
        
        const col = $(`
            <div class="col-md-4 mb-3">
                <div class="form-group">
                    <label for="cashback_level_${i}">Level ${i} Bonus (₱)</label>
                    <input type="number" step="0.01" min="0" class="form-control cashback-level-bonus"
                        id="cashback_level_${i}" name="cashback_level_bonuses[${i}]"
                        placeholder="Auto" value="${existingValue}">
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
        const originalPrice = parseFloat($('input[name="price"]').val()) || 0;
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
    $('input[name="price"], #discount_value, #discount_type').on('change input', updatePricePreview);
    
    // Gallery preview functionality for new uploads
    $('input[name="gallery[]"]').on('change', function(e) {
        const files = e.target.files;
        
        if (files.length > 0) {
            // Create a container for new previews if it doesn't exist
            let newPreviewContainer = $('#new-gallery-preview');
            if (newPreviewContainer.length === 0) {
                newPreviewContainer = $('<div id="new-gallery-preview" class="mt-2 d-flex flex-wrap gap-2"></div>');
                $(this).parent().append(newPreviewContainer);
            } else {
                newPreviewContainer.empty();
            }
            
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        newPreviewContainer.append(
                            `<img src="${e.target.result}" class="img-thumbnail" style="height: 80px;">`
                        );
                    };
                    reader.readAsDataURL(file);
                }
            }
            newPreviewContainer.show();
        }
    });
});

// Function to remove gallery image
function removeGalleryImage(index) {
    // Hide the image container
    $('#gallery-item-' + index).hide();
    
    // Remove the hidden input field
    $('#existing_gallery_' + index).remove();
}
</script>
@endsection