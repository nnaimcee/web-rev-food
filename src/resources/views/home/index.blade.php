@extends($layout)
@section('title')
@endsection
@section('css_before')
@endsection
@section('header')
@endsection
@section('content')
    <h1>ยินดีต้อนรับสู่ FoodReview</h1>
  @auth
    <p>คุณล็อกอินแล้ว สามารถเขียนรีวิวได้เลย 🎉</p>
  @else
    <p>กรุณาเข้าสู่ระบบเพื่อเขียนรีวิว</p>
  @endauth
@endsection
@section('footer')
@endsection
@section('js_before')
@endsection