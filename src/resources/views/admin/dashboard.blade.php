<!-- resources/views/admin/dashboard.blade.php -->
@extends('layouts.app')
@php use Illuminate\Support\Str; @endphp

@section('title', 'Dashboard')

@section('content')
  <div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0">Dashboard by nong reaw</h1>
    <div class="d-flex gap-2">
      <a href="{{ route('restaurants.create') }}" class="btn btn-primary btn-sm">+ เพิ่มร้านค้า</a>
      <a href="{{ route('admin.users.index') }}" class="btn btn-outline-dark btn-sm">จัดการผู้ใช้งาน</a>
    </div>
  </div>

  <!-- KPI Cards -->
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="text-muted small">ผู้ใช้ทั้งหมด</div>
          <div class="display-6 fw-bold">{{ $stats['users'] ?? 0 }}</div>
          <div class="small text-success">+{{ $stats['today_users'] ?? 0 }} ในวันนี้  • +{{ $stats['week_users'] ?? 0 }} ใน 7 วันที่ผ่านมา</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="text-muted small">ร้านอาหาร</div>
          <div class="display-6 fw-bold">{{ $stats['restaurants'] ?? 0 }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="text-muted small">เมนู</div>
          <div class="display-6 fw-bold">{{ $stats['menus'] ?? 0 }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="text-muted small">รีวิวทั้งหมด</div>
          <div class="display-6 fw-bold">{{ $stats['reviews'] ?? 0 }}</div>
          <div class="small text-primary">+{{ $stats['today_reviews'] ?? 0 }} ในวันนี้ • +{{ $stats['week_reviews'] ?? 0 }} ใน 7 วันที่ผ่านมา</div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="text-muted small">ยอดไลค์ทั้งหมด</div>
          <div class="display-6 fw-bold">{{ $stats['likes'] ?? 0 }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="text-muted small">คอมเมนต์ทั้งหมด</div>
          <div class="display-6 fw-bold">{{ $stats['comments'] ?? 0 }}</div>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-6">
      <div class="card shadow-sm h-100">
        <div class="card-body">
          <div class="text-muted small">คะแนนเฉลี่ยรีวิว</div>
          <div class="display-6 fw-bold">{{ number_format($stats['avg_rating'] ?? 0, 2) }} / 5</div>
          <div class="small text-muted">คำนวณจาก {{ $stats['reviews'] ?? 0 }} รีวิว</div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-12 col-lg-6">
      <div class="card shadow-sm">
        <div class="card-header bg-white fw-semibold">ร้านยอดนิยม (Top 5)</div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table align-middle mb-0">
              <thead>
                <tr>
                  <th>ร้านอาหาร</th>
                  <th class="text-center">คะแนนเฉลี่ย</th>
                  <th class="text-end">จำนวนรีวิว</th>
                </tr>
              </thead>
              <tbody>
              @forelse($topRestaurants as $r)
                <tr>
                  <td>{{ $r->name }}</td>
                  <td class="text-center">{{ number_format($r->avg_rating, 2) }}</td>
                  <td class="text-end">{{ $r->review_count }}</td>
                </tr>
              @empty
                <tr><td colspan="3" class="text-center text-muted">ยังไม่มีข้อมูล</td></tr>
              @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-6">
      <div class="card shadow-sm">
        <div class="card-header bg-white fw-semibold">รีวิวล่าสุด</div>
        <div class="card-body">
          @forelse($latestReviews as $rv)
            <div class="d-flex justify-content-between align-items-start py-2 border-bottom">
              <div>
                <div class="fw-semibold">{{ $rv->restaurant_name ?? '—' }} @if($rv->menu_name) • {{ $rv->menu_name }} @endif</div>
                <div class="small text-muted">โดย {{ $rv->username }} • {{ \Carbon\Carbon::parse($rv->created_at)->diffForHumans() }}</div>
                <div class="small">"{{ Str::limit($rv->comment, 80) }}"</div>
              </div>
              <div class="ms-3 text-nowrap">
                <span class="badge bg-primary">⭐ {{ $rv->rating }}</span>
              </div>
            </div>
          @empty
            <div class="text-muted">ยังไม่มีรีวิว</div>
          @endforelse
        </div>
      </div>
    </div>
  </div>
@endsection
