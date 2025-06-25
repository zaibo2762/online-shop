@extends('front.layouts.app')
@section('content')
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="{{ route('front.home') }}">Home</a></li>
                    <li class="breadcrumb-item active">Shop</li>
                </ol>
            </div>
        </div>
    </section>

    <section class="section-6 pt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3 sidebar">
                    <div class="sub-title">
                        <h2>Categories</h3>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="accordion accordion-flush" id="accordionExample">
                                @if ($category->isNotEmpty())
                                    @foreach ($category as $key => $catgory)
                                        <div class="accordion-item">
                                            @if ($catgory->sub_category->isNotEmpty())
                                                <h2 class="accordion-header" id="headingOne">
                                                    <button class="accordion-button collapsed " type="button"
                                                        data-bs-toggle="collapse"
                                                        data-bs-target="#collapseOne-{{ $key }}"
                                                        aria-expanded="false"
                                                        aria-controls="collapseOne-{{ $key }}">
                                                        {{ $catgory->name }}
                                                    </button>
                                                </h2>
                                            @else
                                                <a href="{{ route('front.shop', $catgory->slug) }}"
                                                    class="nav-item nav-link ">{{ $catgory->name }}</a>
                                            @endif
                                            @if ($catgory->sub_category->isNotEmpty())
                                                <div id="collapseOne-{{ $key }}"
                                                    class="accordion-collapse collapse {{ $categorySelected == $catgory->id ? 'show' : '' }}"
                                                    aria-labelledby="headingOne" data-bs-parent="#accordionExample"
                                                    style="">
                                                    <div class="accordion-body">
                                                        <div class="navbar-nav">
                                                            @foreach ($catgory->sub_category as $subcategory)
                                                                <a href="{{ route('front.shop', [$catgory->slug, $subcategory->slug]) }}"
                                                                    class="nav-item nav-link {{ $subcategorySelected == $subcategory->id ? 'text-primary' : '' }}">{{ $subcategory->name }}</a>
                                                            @endforeach


                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                @endif




                            </div>
                        </div>
                    </div>

                    <div class="sub-title mt-5">
                        <h2>Brand</h3>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            @if ($brands->isNotEmpty())
                                @foreach ($brands as $brand)
                                    <div class="form-check mb-2">
                                        <input {{ in_array($brand->id, $brandsArray) ? 'checked' : '' }}
                                            class="form-check-input brand-label" type="checkbox" name="brand[]"
                                            value="{{ $brand->id }}" id="{{ $brand->id }}">
                                        <label class="form-check-label" for="brand-{{ $brand->id }}">
                                            {{ $brand->name }}
                                        </label>
                                    </div>
                                @endforeach
                            @endif


                        </div>
                    </div>

                    <div class="sub-title mt-5">
                        <h2>Price</h3>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                                <label class="form-check-label" for="flexCheckDefault">
                                    $0-$100
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                                <label class="form-check-label" for="flexCheckChecked">
                                    $100-$200
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                                <label class="form-check-label" for="flexCheckChecked">
                                    $200-$500
                                </label>
                            </div>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked">
                                <label class="form-check-label" for="flexCheckChecked">
                                    $500+
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="row pb-3">
                        <div class="col-12 pb-1">
                            <div class="d-flex align-items-center justify-content-end mb-4">
                                <div class="ml-2">
                                    {{-- <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-light dropdown-toggle" data-bs-toggle="dropdown">Sorting</button>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a class="dropdown-item" href="#">Latest</a>
                                        <a class="dropdown-item" href="#">Price High</a>
                                        <a class="dropdown-item" href="#">Price Low</a>
                                    </div>
                                </div>                                     --}}
                                    <select name="sort" id="sort" class="form-control">
                                        <option value="latest">Latest</option>
                                        <option value="price_desc">Price high</option>
                                        <option value="price_asc">Price low</option>

                                    </select>
                                </div>
                            </div>
                        </div>


                        @if ($products->isNotEmpty())
                            @foreach ($products as $product)
                                @php
                                    $productImage = $product->product_image->first();
                                @endphp

                                <div class="col-md-4">
                                    <div class="card product-card">
                                        <div class="product-image position-relative">
                                            <a href="{{ route('front.product', $product->slug) }}" class="product-img">
                                                @if (!empty($productImage->image))
                                                    <img class="card-img-top"
                                                        src="{{ asset('temp/' . $productImage->image) }}" width="50">
                                                @else
                                                    <img src="{{ asset('admin-assets/img/default-150x150.png') }}"
                                                        width="50">
                                                @endif
                                            </a>
                                            <a onclick="addToWishlist({{ $product->id }})" class="whishlist"
                                                href="javascript:void(0)"><i class="far fa-heart"></i></a>

                                            <div class="product-action">
                                                @if ($product->track_qty == 'YES')
                                                    @if ($product->qty > 0)
                                                        <a class="btn btn-dark" href="javascript:void(0);"
                                                            onclick="addToCart({{ $product->id }})">
                                                            <i class="fa fa-shopping-cart"></i> Add To Cart
                                                        </a>
                                                    @else
                                                        <a class="btn btn-dark" href="javascript:void(0);">
                                                            Out Of Stock
                                                        </a>
                                                    @endif
                                                @else
                                                    <a class="btn btn-dark" href="javascript:void(0);"
                                                        onclick="addToCart({{ $product->id }})">
                                                        <i class="fa fa-shopping-cart"></i> Add To Cart
                                                    </a>
                                                @endif
                                            </div>
                                            <div class="card-body text-center mt-3">
                                                <a class="h6 link"
                                                    href="{{ route('front.product', $product->slug) }}">{{ $product->title }}</a>
                                                <div class="price mt-2">
                                                    <span class="h5"><strong>${{ $product->price }}</strong></span>
                                                    @if ($product->compare_price > 0)
                                                        <span
                                                            class="h6 text-underline"><del>${{ $product->compare_price }}</del></span>
                                                    @endif


                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            @endforeach
                        @endif


                        <div class="col-md-12 pt-5">
                            {{ $products->links() }}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



@endsection
@section('customJS')
    <script>
        $(".brand-label").change(function() {
            applyFilter();
        });

        function applyFilter() {
            var brands = [];
            $(".brand-label").each(function() {
                if ($(this).is(":checked") == true) {
                    brands.push($(this).val());
                }
            });

            var keyword = $('#search').val();
            if (keyword.length > 0) {
                url += '&search' + keyword;
            }

            var url = '{{ url()->current() }}?';
            window.location.href = url + ' &brand=' + brands.toString();

        }
    </script>
@endsection
