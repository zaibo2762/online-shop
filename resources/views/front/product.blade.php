@extends('front.layouts.app')
@section('content')
<section class="section-5 pt-3 pb-3 mb-3 bg-white">
    <div class="container">
        <div class="light-font">
            <ol class="breadcrumb primary-color mb-0">
                <li class="breadcrumb-item"><a class="white-text" href="{{ route('front.home') }}">Home</a></li>
                <li class="breadcrumb-item"><a class="white-text" href="{{ route('front.shop') }}">Shop</a></li>
                <li class="breadcrumb-item">{{ $product->title }}</li>
            </ol>
        </div>
    </div>
</section>

<section class="section-7 pt-3 mb-3">
    <div class="container">
        <div class="row ">
            @include('front.account.common.message')
            <div class="col-md-5">
                <div id="product-carousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner bg-light">

                        @if ($product->product_image)
                        @foreach ($product->product_image as $key => $productImage)
                        <div class="carousel-item {{ ($key==0) ? "active" : ''  }}">
                            <img class="w-100 h-100" src="{{ asset("temp/".$productImage->image) }}" alt="Image">
                        </div>
                        @endforeach
                            
                        @endif

                        
                    </div>
                    <a class="carousel-control-prev" href="#product-carousel" data-bs-slide="prev">
                        <i class="fa fa-2x fa-angle-left text-dark"></i>
                    </a>
                    <a class="carousel-control-next" href="#product-carousel" data-bs-slide="next">
                        <i class="fa fa-2x fa-angle-right text-dark"></i>
                    </a>
                </div>
            </div>
            <div class="col-md-7">
                <div class="bg-light right">
                    <h1>{{ $product->title }}</h1>
                    
                    @if ($product->compare_price > 0)
                    <h2 class="price text-secondary"><del>${{ $product->compare_price }}</del></h2>
                    @endif
                    
                    <h2 class="price ">${{ $product->price }}</h2>

                        <p>
                        {!! $product->short_description !!}
                        </p>
                            
    @if ($product->track_qty == "YES")
        @if ($product->qty > 0) 
            <a class="btn btn-dark" href="javascript:void(0);" onclick="addToCart({{ $product->id }})">
                <i class="fa fa-shopping-cart"></i> Add To Cart
            </a>
        @else
            <a class="btn btn-dark" href="javascript:void(0);">
                Out Of Stock
            </a>
        @endif
    @else
        <a class="btn btn-dark" href="javascript:void(0);" onclick="addToCart({{ $product->id }})">
            <i class="fa fa-shopping-cart"></i> Add To Cart
        </a>
    @endif
                </div>
            </div>

            <div class="col-md-12 mt-5">
                <div class="bg-light">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab" aria-controls="description" aria-selected="true">Description</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="shipping-tab" data-bs-toggle="tab" data-bs-target="#shipping" type="button" role="tab" aria-controls="shipping" aria-selected="false">Shipping & Returns</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab" aria-controls="reviews" aria-selected="false">Reviews</button>
                        </li>
                    </ul>

                    
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="description" role="tabpanel" aria-labelledby="description-tab">
                            <p>
                            {!! $product->description !!}
                            </p>
                        </div>
                        <div class="tab-pane fade" id="shipping" role="tabpanel" aria-labelledby="shipping-tab">
                            <p>
                            {!! $product->shipping_returns !!}
                            </p>
                        </div>
                        <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                            <div class="col-md-8">
                                <div class="row">
                                    <form action="" method="POSTcomment" name="productRatingForm" id="productRatingForm">
                                    <h3 class="h4 pb-3">Write a Review</h3>
                                    <div class="form-group col-md-6 mb-3">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control" name="name" id="name" placeholder="Name">
                                        <p></p>
                                    </div>
                                    <div class="form-group col-md-6 mb-3">
                                        <label for="email">Email</label>
                                        <input type="text" class="form-control" name="email" id="email" placeholder="Email">
                                        <p></p>
                                    </div>
                                    <div class="mb-3">
                                        <label for="rating" class="form-label">Rating</label>
                                        <div class="d-flex justify-content-start rating-stars">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <input type="radio" class="btn-check" name="rating" id="rating-{{ $i }}" value="{{ $i }}">
                                                <label class="btn btn-light p-1" for="rating-{{ $i }}">
                                                    <i class="fas fa-star"></i>
                                                </label>
                                            @endfor
                                        </div>
                                        <p class="product-rating-error text-danger"></p>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="">How was your overall experience?</label>
                                        <textarea name="comment"  id="comment" class="form-control" cols="30" rows="10" placeholder="How was your overall experience?"></textarea>
                                        <p></p>
                                    </div>
                                    <div>
                                        <button class="btn btn-dark">Submit</button>
                                    </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-md-12 mt-5">
                                @if($product->product_ratings->isNotEmpty())
                                @foreach ($product->product_ratings as $rating)
                                <div class="rating-group mb-4">
                                   <span> <strong>{{ $rating->username }} </strong></span>
                                    <div class="star-rating mt-2">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fa fa-star{{ $i <= $rating->rating ? ' text-warning' : ' text-secondary' }}" aria-hidden="true"></i>
                                        @endfor
                                    </div>
                                    <div class="my-3">
                                        <p> {{ $rating->comment }} </p>
                                    </div>
                                </div>
                                 @endforeach   
                                @endif
                               

                               
                            </div>
                        </div>

                    </div>
                </div>
            </div> 
        </div>           
    </div>
</section>
<section class="pt-5 section-8">
    @if(!empty($relatedProducts))
    <div class="container">
        <div class="section-title">
            <h2>Related Products</h2>
        </div> 
        <div class="col-md-12">
            <div id="related-products" class="carousel">
                
                @foreach ($relatedProducts as $relatedProduct)
                @php
                $relatedProductImage = $relatedProduct->product_image->first();
            @endphp
                
                <div class="card product-card">
                    <div class="product-image position-relative">
                        <a href="" class="product-img">
                            @if (!empty($productImage->image))
                            <img class="card-img-top" src="{{ asset('temp/'.$relatedProductImage->image) }}" width="50" >
                            @else
                            <img src="{{ asset('admin-assets/img/default-150x150.png') }}" width="50" >
                            @endif
                        </a>
                        <a class="whishlist" href="222"><i class="far fa-heart"></i></a>                            

                       <div class="product-action">
    @if ($relatedProduct->track_qty == "YES")
        @if ($relatedProduct->qty > 0) 
            <a class="btn btn-dark" href="javascript:void(0);" onclick="addToCart({{ $relatedProduct->id }})">
                <i class="fa fa-shopping-cart"></i> Add To Cart
            </a>
        @else
            <a class="btn btn-dark" href="javascript:void(0);">
                Out Of Stock
            </a>
        @endif
    @else
        <a class="btn btn-dark" href="javascript:void(0);" onclick="addToCart({{ $relatedProduct->id }})">
            <i class="fa fa-shopping-cart"></i> Add To Cart
        </a>
    @endif
</div>
                    </div>                        
                    <div class="card-body text-center mt-3">
                        <a class="h6 link" href="">{{ $relatedProduct->title }}</a>
                        <div class="price mt-2">
                            <span class="h5"><strong>${{ $relatedProduct->price }}</strong></span>
                            @if ($relatedProduct->compare_price > 0)
                            <span class="h6 text-underline"><del>${{ $relatedProduct->compare_price }}</del></span>
                            @endif
                            
                        </div>
                    </div>                        
                </div> 
                @endforeach
               
            </div>
        </div>
    </div>
    @endif
</section>
@endsection
@section('customJS')
<script src="{{ asset('front-assets/js/rating-stars.js') }}"></script>
<script>
    $("#productRatingForm").submit(function(event){
        event.preventDefault()
        $.ajax({
            url: '{{ route('front.saveRating',$product->id) }}',
            type: 'post',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response){
                var errors = response.errors
                if(response.status == false){
                    if(errors.name){
                    $('#name').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.name)
                }else{
                    $('#name').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                }
                if(errors.email){
                    $('#email').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.email)
                }else{
                    $('#email').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                }
                if(errors.comment){
                    $('#comment').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors.comment)
                }else{
                    $('#comment').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('')
                }
                if(errors.rating){
                  $('.product-rating-error').html(errors.rating);
                }else{
                    $('.product-rating-error').html('');
                }
                }else{
                    window.location.href="{{ route('front.product',$product->slug) }}"

                }
            }
        })
    })
</script>
@endsection