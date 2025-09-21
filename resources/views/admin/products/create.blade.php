@extends('adminlte::page')
@section('title', 'Add Product')

@section('content')
<div class="container-fluid px-2 py-3">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="card shadow-sm rounded-4">
                <div class="card-header bg-primary text-white rounded-top-4">
                    <h4 class="mb-0"><i class="bi bi-plus me-2"></i>Add New Product</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-floating mb-3">
                            <input type="text" name="name" id="name" class="form-control rounded-3" placeholder="Product Name" value="{{ old('name') }}" required>
                            <label for="name">Product Name</label>
                        </div>

                        <div class="form-floating mb-3">
                            <textarea name="description" id="description" class="form-control rounded-3" style="height: 100px" placeholder="Description">{{ old('description') }}</textarea>
                            <label for="description">Description</label>
                            <div class="form-text">Use line breaks to separate paragraphs. Formatting will be preserved when displaying.</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-floating mb-3">
                                <input type="number" step="0.01" name="price" id="price" class="form-control rounded-3" placeholder="Price" value="{{ old('price') }}" required>
                                <label for="price">Price (₱)</label>
                            </div>
                            <div class="col-md-6 form-floating mb-3">
                                <input type="number" step="0.01" name="cashback_amount" id="cashback_amount" class="form-control rounded-3" placeholder="Cashback" value="{{ old('cashback_amount') }}" required>
                                <label for="cashback_amount">Total Cashback (₱)</label>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cashback_max_level" class="form-label">Maximum Cashback Level</label>
                                <select name="cashback_max_level" id="cashback_max_level" class="form-select rounded-3" required>
                                    @for ($i = 1; $i <= 11; $i++)
                                        <option value="{{ $i }}" {{ old('cashback_max_level', 3) == $i ? 'selected' : '' }}>{{ $i }}</option>
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
                                        <label for="discount_value" class="form-label">Discount Value</label>
                                        <select name="discount_value" id="discount_value" class="form-select rounded-3">
                                            <option value="">-- Select Discount --</option>
                                            @foreach($discountValues as $val)
                                                <option value="{{ $val }}" @selected(old('discount_value') == $val)>₱{{ $val }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="discount_type" class="form-label">Discount Type</label>
                                        <select name="discount_type" id="discount_type" class="form-select rounded-3">
                                            <option value="">-- None --</option>
                                            <option value="flat" @selected(old('discount_type') == 'flat')>Flat</option>
                                            <option value="percent" @selected(old('discount_type') == 'percent')>Percentage</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="promo_code" class="form-label">Promo Code</label>
                                    <select name="promo_code" id="promo_code" class="form-select rounded-3">
                                        <option value="">-- Select Promo Code --</option>
                                        @foreach($promoCodes as $code)
                                            <option value="{{ $code }}" @selected(old('promo_code') == $code)>{{ $code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div id="price-preview" class="mt-3 p-3 border rounded bg-light">
                                    <h6 class="mb-2">Price Preview for Members</h6>
                                    <div class="d-flex justify-content-between">
                                        <span>Original Price:</span>
                                        <span id="original-price">₱0.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between text-success">
                                        <span>Discount:</span>
                                        <span id="discount-amount">-₱0.00</span>
                                    </div>
                                    <hr class="my-2">
                                    <div class="d-flex justify-content-between fw-bold">
                                        <span>Final Price for Members:</span>
                                        <span id="final-price">₱0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="category_id" class="form-label">Category</label>
                                <select name="category_id" class="form-select rounded-3" required>
                                    <option value="">-- Select Category --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="unit_id" class="form-label">Unit</label>
                                <select name="unit_id" class="form-select rounded-3" required>
                                    <option value="">-- Select Unit --</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}" @selected(old('unit_id') == $unit->id)>{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">Available Sizes</label>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($sizes as $size)
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="checkbox" name="sizes[]" value="{{ $size }}" {{ in_array($size, old('sizes', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label">{{ $size }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Available Colors</label>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($colors as $color)
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="checkbox" name="colors[]" value="{{ $color }}" {{ in_array($color, old('colors', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label">{{ $color }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="thumbnail" class="form-label">Thumbnail Image</label>
                            <input type="file" name="thumbnail" class="form-control rounded-3" accept="image/*">
                            <img id="thumbnail-preview" class="img-thumbnail mt-2" style="max-height: 100px; display: none;">
                        </div>

                        <div class="mb-3">
                            <label for="gallery[]" class="form-label">Gallery Images</label>
                            <input type="file" name="gallery[]" multiple class="form-control rounded-3" accept="image/*">
                        </div>

                        <div id="gallery-preview" class="d-flex flex-wrap gap-2 mt-2"></div>
                        <input type="hidden" name="gallery_order" id="gallery_order">

                        <div class="form-floating mb-4">
                            <input type="number" name="stock_quantity" id="stock_quantity" class="form-control rounded-3" placeholder="Stock Quantity" value="{{ old('stock_quantity') }}" required>
                            <label for="stock_quantity">Stock Quantity</label>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('products.index') }}" class="btn btn-secondary rounded-pill px-4">
                                <i class="bi bi-arrow-left me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-success rounded-pill px-4">
                                <i class="bi bi-save me-1"></i> Save Product
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
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
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
                        id="cashback_level_${i}" name="cashback_level_bonuses[${i}]" placeholder="Auto">
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
        url: '{{ route("admin.products.preview-cashback") }}',
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
});

document.querySelector('input[name="thumbnail"]').addEventListener('change', function(e) {
    const preview = document.getElementById('thumbnail-preview');
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(evt) {
            preview.src = evt.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
});

const galleryInput = document.querySelector('input[name="gallery[]"]');
const galleryPreview = document.getElementById('gallery-preview');
const galleryOrderInput = document.getElementById('gallery_order');

galleryInput.addEventListener('change', function() {
    galleryPreview.innerHTML = '';
    [...this.files].forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(evt) {
            const wrapper = document.createElement('div');
            wrapper.classList.add('position-relative');
            wrapper.setAttribute('data-index', index);

            const img = document.createElement('img');
            img.src = evt.target.result;
            img.className = 'img-thumbnail';
            img.style.height = '80px';

            wrapper.appendChild(img);
            galleryPreview.appendChild(wrapper);
        };
        reader.readAsDataURL(file);
    });
});

new Sortable(galleryPreview, {
    animation: 150,
    onEnd: function () {
        const order = [...galleryPreview.children].map(div => div.getAttribute('data-index'));
        galleryOrderInput.value = JSON.stringify(order);
    }
});
</script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('select').select2({
        theme: 'bootstrap4',
        width: '100%'
    });
    
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
});
</script>
@endsection