@extends('adminlte::page')

@section('title', 'Edit Referral Configuration')

@section('content_header')
    <h1>Edit Referral Configuration</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.referral-configurations.update', $referralConfiguration) }}" method="POST" id="configForm">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label for="name">Configuration Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $referralConfiguration->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="description">Description (Optional)</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ old('description', $referralConfiguration->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="total_allocation">Total Allocation (₱)</label>
                            <input type="number" step="0.01" min="0" class="form-control @error('total_allocation') is-invalid @enderror" id="total_allocation" name="total_allocation" value="{{ old('total_allocation', $referralConfiguration->total_allocation) }}" required>
                            @error('total_allocation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="max_level">Maximum Level</label>
                            <select class="form-control @error('max_level') is-invalid @enderror" id="max_level" name="max_level" required>
                                @for ($i = 1; $i <= 11; $i++)
                                    <option value="{{ $i }}" {{ old('max_level', $referralConfiguration->max_level) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                            @error('max_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="card mt-4">
                    <div class="card-header bg-light">
                        <h5>Level Bonus Amounts</h5>
                        <p class="text-muted mb-0">Leave fields empty to auto-distribute the remaining amount</p>
                    </div>
                    <div class="card-body">
                        <div id="level-inputs">
                            <!-- Level inputs will be generated here -->
                        </div>
                    </div>
                </div>
                
                <div class="card mt-4">
                    <div class="card-header bg-light">
                        <h5>Preview</h5>
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
                                <tbody id="preview-table">
                                    <!-- Preview data will be generated here -->
                                </tbody>
                                <tfoot>
                                    <tr class="font-weight-bold">
                                        <td>Total</td>
                                        <td id="preview-total">₱0.00</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-primary">Update Configuration</button>
                    <a href="{{ route('admin.referral-configurations.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@stop

@section('js')
<script>
    $(function() {
        // Get existing level bonuses
        const existingBonuses = @json($referralConfiguration->level_bonuses ?? []);
        
        // Generate level inputs based on max_level
        function generateLevelInputs() {
            const maxLevel = parseInt($('#max_level').val());
            const container = $('#level-inputs');
            container.empty();
            
            const row = $('<div class="row"></div>');
            
            for (let i = 1; i <= maxLevel; i++) {
                const levelKey = i.toString();
                const value = existingBonuses[levelKey] || '';
                
                const col = $(`
                    <div class="col-md-4 mb-3">
                        <div class="form-group">
                            <label for="level_${i}">Level ${i} Bonus (₱)</label>
                            <input type="number" step="0.01" min="0" class="form-control level-bonus" 
                                id="level_${i}" name="level_bonuses[${i}]" placeholder="Auto" value="${value}">
                        </div>
                    </div>
                `);
                
                row.append(col);
            }
            
            container.append(row);
        }
        
        // Update preview based on current values
        function updatePreview() {
            const totalAllocation = parseFloat($('#total_allocation').val()) || 0;
            const maxLevel = parseInt($('#max_level').val()) || 1;
            
            // Collect custom values
            const levelBonuses = {};
            $('.level-bonus').each(function() {
                const level = $(this).attr('id').replace('level_', '');
                const value = $(this).val();
                if (value !== '' && !isNaN(parseFloat(value))) {
                    levelBonuses[level] = parseFloat(value);
                }
            });
            
            // Send to server for calculation
            $.ajax({
                url: '{{ route("admin.referral-configurations.preview") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    total_allocation: totalAllocation,
                    max_level: maxLevel,
                    level_bonuses: levelBonuses
                },
                success: function(response) {
                    const previewTable = $('#preview-table');
                    previewTable.empty();
                    
                    for (let i = 1; i <= maxLevel; i++) {
                        const amount = response.bonuses[i] || 0;
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
                    
                    $('#preview-total').text(`₱${response.total.toFixed(2)}`);
                }
            });
        }
        
        // Initial setup
        generateLevelInputs();
        updatePreview();
        
        // Event listeners
        $('#max_level').on('change', function() {
            generateLevelInputs();
            updatePreview();
        });
        
        $('#total_allocation').on('input', updatePreview);
        
        $(document).on('input', '.level-bonus', updatePreview);
    });
</script>
@stop