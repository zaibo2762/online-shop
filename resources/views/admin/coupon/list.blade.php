@extends('admin.layouts.app')
@section('content')
    <section class="content-header">
        <section class="content-header">
            <div class="container-fluid my-2">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Discount Coupons</h1>
                    </div>
                    <div class="col-sm-6 text-right">
                        <a href="{{ route('coupons.create') }}" class="btn btn-primary">New Coupon</a>
                    </div>
                </div>
            </div>
            <!-- /.container-fluid -->
        </section>
        <!-- Main content -->
        <section class="content">
            <!-- Default box -->
            <div class="container-fluid">
                @include('admin.message')
                <div class="card">
                    <form action="" method='get'>
                        <div class="card-header">
                            <div class="card-title">
                                <button type="button" onclick="window.location.href='{{ route('coupons.index') }}'"
                                    class='btn btn-default btn-sm'>Reset</button>
                            </div>
                            <div class="card-tools">
                                <div class="input-group input-group" style="width: 250px;">
                                    <input type="text" value="{{ Request::get('keyword') }}" name="keyword"
                                        class="form-control float-right" placeholder="Search">

                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-default">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </form>

                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th width="60">ID</th>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Discount</th>
                                    <th>Start Date</th>
                                    <th>Expiry Date</th>
                                    <th width="100">Status</th>
                                    <th width="100">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($discountCoupons->isNotEmpty())
                                    @foreach ($discountCoupons as $discountCoupon)
                                        <tr>
                                            <td>{{ $discountCoupon->id }}</td>
                                            <td>{{ $discountCoupon->code }}</td>
                                            <td>{{ $discountCoupon->name }}</td>
                                            <td>
                                                @if ($discountCoupon->type == 'percent')
                                                    {{ $discountCoupon->discount_amount }}%
                                                @else
                                                    ${{ $discountCoupon->discount_amount }}
                                                @endif
                                            </td>
                                            <td>{{ !empty($discountCoupon->starts_at) ? \Carbon\Carbon::parse($discountCoupon->starts_at)->format('Y/m/d H:i:s') : '' }}
                                            </td>
                                            <td>{{ !empty($discountCoupon->expires_at) ? \Carbon\Carbon::parse($discountCoupon->expires_at)->format('Y/m/d H:i:s') : '' }}
                                            </td>
                                            @if ($discountCoupon->status == 1)
                                                <td>
                                                    <svg class="text-success-500 h-6 w-6 text-success"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                        aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </td>
                                            @else
                                                <td>
                                                    <svg class="text-danger h-6 w-6" xmlns="http://www.w3.org/2000/svg"
                                                        fill="none" viewBox="0 0 24 24" stroke-width="2"
                                                        stroke="currentColor" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z">
                                                        </path>
                                                    </svg>
                                                </td>
                                            @endif

                                            <td>
                                                <a href="{{ route('coupons.edit', $discountCoupon->id) }}">
                                                    <svg class="filament-link-icon w-4 h-4 mr-1"
                                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                        fill="currentColor" aria-hidden="true">
                                                        <path
                                                            d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z">
                                                        </path>
                                                    </svg>
                                                </a>
                                                <a href="#" onclick="deleteCoupon({{ $discountCoupon->id }})"
                                                    class="text-danger w-4 h-4 mr-1">
                                                    <svg wire:loading.remove.delay="" wire:target=""
                                                        class="filament-link-icon w-4 h-4 mr-1"
                                                        xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                        fill="currentColor" aria-hidden="true">
                                                        <path ath fill-rule="evenodd"
                                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="5">Records Not Found</td>
                                    </tr>
                                @endif


                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer clearfix">
                        {{ $discountCoupons->links() }}
                    </div>
                </div>
            </div>
            <!-- /.card -->
        </section>
        <!-- /.content -->

    </section>
    <!-- /.content -->

@endsection

@section('customJs')
    <script>
        function deleteCoupon(id) {
            var url = '{{ route('coupons.delete', 'ID') }}';
            var newUrl = url.replace('ID', id)

            if (confirm("Are you sure to Delete")) {
                $.ajax({
                    url: newUrl,
                    type: 'delete',
                    data: {},
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response['status']) {
                            window.location.href = "{{ route('coupons.index') }}";
                        }
                    }
                })
            }


        }
    </script>
@endsection
