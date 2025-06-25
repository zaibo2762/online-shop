@extends('admin.layouts.app')
@section('content')
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Coupon Code</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{ route('coupons.index') }}" class="btn btn-primary">Back</a>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            <form action="" method="post" id="discountForm" name="discountForm">

                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="Code">Code</label>
                                    <input value="{{ $coupon->code }}" type="text" name="code" id="code"
                                        class="form-control" placeholder="Coupon Code">
                                    <p></p>
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name">Name</label>
                                    <input value="{{ $coupon->name }}" type="text" name="name" id="name"
                                        class="form-control" placeholder="Coupon code name">
                                    <p></p>
                                </div>

                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_uses">Max Uses</label>
                                    <input type="number" value="{{ $coupon->max_uses }}" name="max_uses" id="max_uses"
                                        class="form-control" placeholder="Maximum Uses">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_uses_users">Max Uses Users</label>
                                    <input type="text" value="{{ $coupon->max_uses_user }}" name="max_uses_users"
                                        id="max_uses_users" class="form-control" placeholder="Maximum Uses Users">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type">Type</label>
                                    <select name="type" id="type" class="form-control">
                                        <option {{ $coupon->type == 'percent' ? 'selected' : '' }} value="percent">Percent
                                        </option>
                                        <option {{ $coupon->type == 'fixed' ? 'selected' : '' }} value="fixed">Fixed
                                        </option>
                                    </select>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="discount_amount">Discount Amount</label>
                                    <input type="number" value="{{ $coupon->discount_amount }}" name="discount_amount"
                                        id="discount_amount" class="form-control" placeholder="Discount Amount">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="min_amount">Minimum Amount</label>
                                    <input type="number" value="{{ $coupon->min_amount }}" name="min_amount"
                                        id="min_amount" class="form-control" placeholder="Minimum Amount">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option {{ $coupon->status == 1 ? 'selected' : '' }} value="1">Active
                                        </option>
                                        <option {{ $coupon->status == 0 ? 'selected' : '' }} value="0">Block
                                        </option>
                                    </select>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="starts_at">Start Date</label>
                                    <input autocomplete="off" value="{{ $coupon->starts_at }}" type="text"
                                        name="starts_at" id="starts_at" class="form-control" placeholder="start date">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="expires_at">Expires Date</label>
                                    <input autocomplete="off" value="{{ $coupon->expires_at }}" type="text"
                                        name="expires_at" id="expires_at" class="form-control"
                                        placeholder="expires date">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="description">Description</label>
                                    <textarea name="description" id="description" cols="30" rows="5" class="form-control"> {{ $coupon->description }} </textarea>
                                    <p></p>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>

                <div class="pb-5 pt-3">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('coupons.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
                </div>
        </div>
        </form>
        <!-- /.card -->
    </section>
    <!-- /.content -->
@endsection

@section('customJs')
    <script>
        $(document).ready(function() {
            $('#starts_at').datetimepicker({
                // options here
                format: 'Y-m-d H:i:s',
            });
            $('#expires_at').datetimepicker({
                // options here
                format: 'Y-m-d H:i:s',
            });
        });
        $('#discountForm').submit(function(event) {
            event.preventDefault();
            var form = $(this); // Get the form
            var formData = form.serialize();
            $("button[type=submit]").prop('disabled', true)
            $.ajax({
                url: '{{ route('coupons.update', $coupon->id) }}',
                type: 'put',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.status == true) {
                        $("button[type=submit]").prop('disabled', false)
                        window.location.href = "{{ route('coupons.index') }}";
                        $('#code').removeClass('is-invalid').siblings('p').removeClass(
                            'invalid-feedback').html('');
                        $('#discount_amount').removeClass('is-invalid').siblings('p').removeClass(
                            'invalid-feedback').html('');
                        $('#starts_at').removeClass('is-invalid').siblings('p').removeClass(
                            'invalid-feedback').html('');
                        $('#expires_at').removeClass('is-invalid').siblings('p').removeClass(
                            'invalid-feedback').html('');


                    } else {
                        var errors = response['errors'];
                        if (errors['code']) {
                            $('#code').addClass('is-invalid').siblings('p').addClass('invalid-feedback')
                                .html(errors['code']);
                        } else {
                            $('#code').removeClass('is-invalid').siblings('p').removeClass(
                                'invalid-feedback').html('');
                        }
                        if (errors['discount_amount']) {
                            $('#discount_amount').addClass('is-invalid').siblings('p').addClass(
                                'invalid-feedback').html(errors['discount_amount']);
                        } else {
                            $('#discount_amount').removeClass('is-invalid').siblings('p').removeClass(
                                'invalid-feedback').html('');
                        }
                        if (errors['starts_at']) {
                            $('#starts_at').addClass('is-invalid').siblings('p').addClass(
                                'invalid-feedback').html(errors['starts_at']);
                        } else {
                            $('#starts_at').removeClass('is-invalid').siblings('p').removeClass(
                                'invalid-feedback').html('');
                        }
                        if (errors['expires_at']) {
                            $('#expires_at').addClass('is-invalid').siblings('p').addClass(
                                'invalid-feedback').html(errors['expires_at']);
                        } else {
                            $('#expires_at').removeClass('is-invalid').siblings('p').removeClass(
                                'invalid-feedback').html('');
                        }
                    }

                },
                error: function(jqXHR, exception) {
                    console.log('Something went wrong');
                }
            })
        });
    </script>
@endsection
