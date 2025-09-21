@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
             
                    
        <div class="col-md-12">

            
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    
               <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title"  style="color:white !important">Order Management</h3>
                </div>
                <div class="card-body">
                    @include('admin.orders._order_list')
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" style="color:white !important">AliExpress Integration</h3>
                </div>
                <div class="card-body">
                    <p>Connect your AliExpress account to import orders and products.</p>
               
                    <div class="mt-4">
                        <a href="{{ route('aliexpress.redirect') }}" class="btn btn-primary">
                            <i class="bi bi-alipay"></i> Connect AliExpress Account
                        </a>
                    </div>
                    
                    <div class="mt-4">
                        <h4>How to use:</h4>
                        <ol>
                            <li>Click the "Connect AliExpress Account" button above</li>
                            <li>You'll be redirected to AliExpress to authorize the application</li>
                            <li>After authorization, you'll be redirected back to this page</li>
                            <li>Your AliExpress access token will be securely stored</li>
                            <li>You can then import orders and products from your AliExpress account</li>
                        </ol>
                    </div>
                </div>
            </div>
            
         
        </div>
    </div>
</div>
@endsection
@include('partials.mobile-footer')
@section('js')
    @stack('js')
@endsection
