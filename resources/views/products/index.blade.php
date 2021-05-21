@extends('layouts.app')

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Products</h1>
    </div>


    <div class="card">
        <form action="{{ route('product.index') }}" method="POST" class="card-header">
            @csrf
            <div class="form-row justify-content-between">
                <div class="col-md-2">
                    <input type="text" name="search[title]" placeholder="Product Title" class="form-control">
                </div>
                <div class="col-md-2">
                    <select name="search[variant_id]" id="" class="form-control">
                        <option value="">Select A Option</option>
                        @foreach($variants as $key=>$row)
                            <option value="{{ $row->id }}">{{ $row->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Price Range</span>
                        </div>
                        <input type="text" name="search[price_from]" aria-label="First name" placeholder="From" class="form-control">
                        <input type="text" name="search[price_to]" aria-label="Last name" placeholder="To" class="form-control">
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="date" name="search[date]" placeholder="Date" class="form-control">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary float-right"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>

        <div class="card-body">
            <div class="table-response">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Variant</th>
                        <th width="150px">Action</th>
                    </tr>
                    </thead>

                    <tbody>
                        @if(!$products->isEmpty()) 
                        @foreach($products as $key=>$row)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{{ $row->title }} <br> Created at : {{ date('d-M-Y', strtotime($row->created_at)) }}</td>
                        <td>{{ $row->description }}</td>
                        <td>
                        @if($row->variant_info)
                            @foreach($row->variant_info as $key=>$info)
                            <dl class="row mb-0" style="height: 80px; overflow: hidden" id="variant">
                                <dt class="col-sm-3 pb-0">
                                    {{ $info->variant->variant ?? 'N/A' }}
                                </dt>
                                <dd class="col-sm-9">
                                    <dl class="row mb-0">
                                        <dt class="col-sm-4 pb-0">Price : {{ number_format(($info->price ?? 0),2) }}</dt>
                                        <dd class="col-sm-8 pb-0">InStock : {{ number_format(($info->stock ?? 0),2) }}</dd>
                                    </dl>
                                </dd>
                            </dl>
                            @endforeach
                        @endif
                            <button onclick="$('#variant').toggleClass('h-auto')" class="btn btn-sm btn-link">Show more</button>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('product.edit', $row->id) }}" class="btn btn-success">Edit</a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @endif

                    </tbody>

                </table>
            </div>

        </div>

        <div class="card-footer">
            <div class="row justify-content-between">
                <div class="col-md-6">
                    <p>Showing 1 to 10 out of 100</p>
                </div>
                <div class="col-md-2">

                </div>
            </div>
        </div>
    </div>

@endsection
