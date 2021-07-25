@extends('layouts.app')


@section('content')

<h3> My Cart</h3>
<table class="table">
  <thead>
    <tr>
      <th scope="col">Name</th>
      <th scope="col">Price</th>
      <th scope="col">Quantity</th>
        <th scope="col">Action</th>
    </tr>
  </thead>
@foreach ($cartItems as $item)
  <tbody>
    <tr>
      <th scope="row">{{$item->name}}</th>
      <td>{{$item->price}}


          {{\Cart::session(auth()->id())->get($item->id)->getPriceSum()}}
      </td>
        <td>
          <form action="{{route('cart.update',$item->id)}}">
              <input type="number" name="quantity"  value="{{$item->quantity}}">
              <input type="submit" value="Save">
          </form>
      </td>
        <td>
            <a href="{{route('cart.destroy',$item->id)}}">Delete</a>
        </td>
    </tr>
  </tbody>
    @endforeach
</table>
<h3>
    Total Price=$ {{number_format(Cart::session(auth()->id())->getTotal())}}
</h3>
<a  class="btn btn-success" href="{{route('cart.checkout')}}">Proceed to checkout</a>


{{--{{dd(\Cart::session(auth()->id())->getContent())}}--}}

@endsection
