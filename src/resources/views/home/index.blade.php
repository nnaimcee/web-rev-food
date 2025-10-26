@extends($layout)

@section('title', 'หน้าแรก - รีวิวอาหาร')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
<div class="container py-4">
    <h1 class="mb-4 text-center fw-bold text-danger">
        🍱 รีวิวอาหารล่าสุด
    </h1>

    @auth
    <div class="text-center mb-3">
        <a href="{{ route('member.review.create') }}" class="btn btn-primary mt-2">
            + เพิ่มรีวิว
        </a>
    </div>
    @endauth

    @if($reviews->isEmpty())
        <div class="alert alert-info text-center shadow-sm">
            😢 ยังไม่มีรีวิวในระบบตอนนี้<br>
            @auth
            <a href="{{ route('member.review.create') }}" class="btn btn-primary mt-2">
                เพิ่มรีวิวแรกของคุณเลย!
            </a>
            @else
            <a href="{{ route('login.get') }}" class="btn btn-outline-primary mt-2">
                เข้าสู่ระบบเพื่อเพิ่มรีวิวแรกของคุณ
            </a>
            @endauth
        </div>
    @else
        <div class="row g-4">
            @foreach($reviews as $review)
                <div class="col-md-4 d-flex justify-content-center">
                    <div class="card fixed-size shadow-sm border-0 rounded-3 overflow-hidden js-open-modal" data-modal-id="reviewModal-{{ $review->review_id }}">
                        <!-- รูปภาพ -->
                        @php
                            $defaultReviewImg = 'https://img.freepik.com/free-vector/hand-drawn-flat-design-thai-food-illustration_23-2149273545.jpg?t=st=1761403897~exp=1761407497~hmac=cee962c1f1986caa6a60aaad3f4462ca28cb316f262a1a4601fc6b5d6128d0ee&w=1480';
                            $imageSrc = $review->image_path
                                ? (Str::startsWith($review->image_path, ['http://', 'https://'])
                                    ? $review->image_path
                                    : asset('storage/' . $review->image_path))
                                : $defaultReviewImg;
                        @endphp
                        <img src="{{ $imageSrc }}" class="card-img-top" alt="ภาพรีวิว">

                        <div class="card-body">
                            <h5 class="card-title text-danger fw-bold mb-1">
                                <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#reviewModal-{{ $review->review_id }}">🍽️ <span class="star-heart-scale">{{ $review->menu_name }}</span></a>
                            </h5>

                            <p class="text-muted mb-1">
                                🏠 ร้าน: <strong><a href="{{ route('home.index', ['restaurant' => $review->restaurant_id]) }}" class="text-decoration-underline text-reset">{{ $review->restaurant_name }}</a></strong>
                            </p>

                            <p class="small text-secondary mb-2">
                                👤 โดย {{ $review->username }}
                            </p>

                            <p class="card-text text-truncate">{{ Str::limit($review->comment, 100, '...') }}</p>
                            @php $comments = $commentsByReview[$review->review_id] ?? []; @endphp
                            <div class="small text-muted">ความเห็น {{ count($comments) }}</div>

                            @php $tags = $tagsMap[$review->review_id] ?? []; @endphp
                            @if(count($tags))
                                <div class="mt-2">
                                    @foreach($tags as $tag)
                                        <a href="{{ route('home.index', ['tag' => $tag]) }}" class="badge rounded-pill text-bg-light me-1">#{{ $tag }}</a>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Comments preview (latest 2 top-level) --}}
                            @php
                                $comments = $commentsByReview[$review->review_id] ?? [];
                                $preview = [];
                                foreach ($comments as $c) {
                                    if (!$c->parent_id) { $preview[] = $c; }
                                    if (count($preview) >= 2) break;
                                }
                            @endphp
                            @if(count($comments))
                                <div class="mt-2 small">
                                    @foreach($preview as $pc)
                                        @php 
                                          $avatar = $pc->avatar_img ? asset('storage/'.$pc->avatar_img) : 'https://cdn-icons-png.freepik.com/512/11121/11121549.png';
                                        @endphp
                                        <div class="text-truncate d-flex align-items-center">
                                          <img src="{{ $avatar }}" class="rounded-circle me-2" width="18" height="18" alt="avatar">
                                          <div class="flex-grow-1 text-truncate">
                                            {{ $pc->username }}
                                            @if(($pc->role ?? null) === 'admin')
                                              <span class="text-danger">(แอดมิน)</span>
                                            @endif
                                            : {{ $pc->content }}
                                          </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="card-footer bg-white border-0 d-flex justify-content-between align-items-center">
                                <span class="fw-bold star-heart-scale" aria-label="rating">
                                  @php $filled = (int) ($review->rating ?? 0); @endphp
                                  @for($i=1; $i<=5; $i++)
                                    <span style="color: {{ $i <= $filled ? '#f59f00' : '#ced4da' }};">★</span>
                                  @endfor
                                </span>

                            <div class="d-flex align-items-center gap-2">
                                @auth
                                    @php
                                        $liked = isset($likedReviewIds) && in_array($review->review_id, $likedReviewIds);
                                    @endphp
                                    <div class="like-toggle d-flex align-items-center" data-review-id="{{ $review->review_id }}">
                                        <form method="POST" data-like="like" action="{{ route('member.review.like', ['id' => $review->review_id]) }}" class="m-0 p-0" style="visibility: {{ $liked ? 'hidden' : 'visible' }};">
                                            @csrf
                                            <button type="submit" class="p-0 border-0 bg-transparent" aria-label="ถูกใจ" title="ถูกใจ">
                                                <span class="fs-5 like-heart inactive" style="cursor:pointer;">&#9829;</span>
                                            </button>
                                        </form>
                                        <span class="ms-2 like-count">{{ $review->like_count ?? 0 }}</span>
                                        <form method="POST" data-like="unlike" action="{{ route('member.review.unlike', ['id' => $review->review_id]) }}" class="m-0 p-0" style="visibility: {{ $liked ? 'visible' : 'hidden' }};">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-0 border-0 bg-transparent" aria-label="เลิกถูกใจ" title="เลิกถูกใจ">
                                                <span class="fs-1 like-heart active" style="cursor:pointer;">&#9829;</span>
                                            </button>
                                        </form>
                                    </div>
                                @el
                                    <a href="{{ route('login.get') }}" class="text-decoration-none" title="เข้าสู่ระบบเพื่อกดถูกใจ">
                                        <span class="fs-5 like-heart inactive">&#9829;</span>
                                    </a>
                                @endauth
                                
                                @auth
                                    @php $canDeleteReview = ((auth()->user()->role ?? null) === 'admin') || $review->user_id === auth()->user()->user_id; @endphp
                                    @if($canDeleteReview)
                                        <form method="POST" action="{{ route('member.review.destroy', ['id' => $review->review_id]) }}" onsubmit="return confirm('ยืนยันการลบรีวิวนี้?');" class="m-0 p-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">ลบรีวิว</button>
                                        </form>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>

                    <!-- Modal for this review -->
                    <div class="modal fade" id="reviewModal-{{ $review->review_id }}" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog modal-xl modal-dialog-centered">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">{{ $review->menu_name }} • {{ $review->restaurant_name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <div class="row g-3">
                              <div class="col-md-6 d-flex justify-content-center">
                                @php
                                  $modalImg = $review->image_path
                                    ? (Str::startsWith($review->image_path, ['http://', 'https://']) ? $review->image_path : asset('storage/'.$review->image_path))
                                    : $defaultReviewImg;
                                @endphp
                                <img src="{{ $modalImg }}" class="modal-review-image rounded" alt="">
                              </div>
                              <div class="col-md-6">
                                <div class="mb-2">ให้คะแนน:
                                  <span class="star-heart-scale" aria-label="rating">
                                    @php $filled = (int) ($review->rating ?? 0); @endphp
                                    @for($i=1; $i<=5; $i++)
                                      <span style="color: {{ $i <= $filled ? '#f59f00' : '#ced4da' }};">★</span>
                                    @endfor
                                  </span>
                                </div>
                                <div class="mb-2">โดย {{ $review->username }}</div>
                                <div class="mb-3">{{ $review->comment }}</div>

                                @php $tags = $tagsMap[$review->review_id] ?? []; @endphp
                                @if(count($tags))
                                  <div class="mb-3">
                                    @foreach($tags as $tag)
                                      <a href="{{ route('home.index', ['tag' => $tag]) }}" class="badge text-bg-light me-1">#{{ $tag }}</a>
                                    @endforeach
                                  </div>
                                @endif

                                <div class="d-flex align-items-center gap-2 mb-3">
                                  @auth
                                    @php $liked = isset($likedReviewIds) && in_array($review->review_id, $likedReviewIds); @endphp
                                    <div class="like-toggle d-flex align-items-center" data-review-id="{{ $review->review_id }}">
                                      <form method="POST" data-like="like" action="{{ route('member.review.like', ['id' => $review->review_id]) }}" class="m-0 p-0" style="visibility: {{ $liked ? 'hidden' : 'visible' }};">
                                        @csrf
                                        <button type="submit" class="p-0 border-0 bg-transparent" aria-label="ถูกใจ" title="ถูกใจ">
                                          <span class="fs-5 like-heart inactive" style="cursor:pointer;">&#9829;</span>
                                        </button>
                                      </form>
                                      <span class="ms-2 like-count">{{ $review->like_count ?? 0 }}</span>
                                      <form method="POST" data-like="unlike" action="{{ route('member.review.unlike', ['id' => $review->review_id]) }}" class="m-0 p-0" style="visibility: {{ $liked ? 'visible' : 'hidden' }};">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-0 border-0 bg-transparent" aria-label="เลิกถูกใจ" title="เลิกถูกใจ">
                                          <span class="fs-5 like-heart active" style="cursor:pointer;">&#9829;</span>
                                        </button>
                                      </form>
                                    </div>
                                  @endauth
                                </div>

                                @php $comments = $commentsByReview[$review->review_id] ?? []; @endphp
                                <div class="review-comments" data-review-id="{{ $review->review_id }}" data-post-url="{{ route('member.review.comment.store', ['id' => $review->review_id]) }}">
                                  <div class="review-comments-list">
                                    @foreach($comments as $c)
                                      @if(!$c->parent_id)
                                      <div class="mb-2 comment-block" data-comment-id="{{ $c->comment_id }}">
                                        @php $avatar = $c->avatar_img ? asset('storage/'.$c->avatar_img) : 'https://cdn-icons-png.freepik.com/512/11121/11121549.png'; @endphp
                                        <div class="small text-muted d-flex align-items-center">
                                          <img src="{{ $avatar }}" class="rounded-circle me-2" width="20" height="20" alt="avatar">
                                          <div>
                                            <span class="comment-username">{{ $c->username }}</span>
                                            @if(($c->role ?? null) === 'admin')
                                              <span class="text-danger">(แอดมิน)</span>
                                            @endif
                                            • <span class="comment-created">{{ $c->created_at }}</span>
                                            @if(!empty($c->updated_at))
                                              <span class="comment-edited"> • แก้ไขแล้ว</span>
                                            @endif
                                          </div>
                                        </div>
                                        <div class="mb-1 comment-content">{{ $c->content }}</div>
                                        <div class="d-flex gap-2 align-items-center flex-wrap">
                                          @auth
                                          <button type="button" class="btn btn-sm btn-outline-primary reply-toggle" data-parent-id="{{ $c->comment_id }}">ตอบกลับ</button>
                                          @php $canDelete = ((auth()->user()->role ?? null) === 'admin') || $c->user_id === auth()->user()->user_id; @endphp
                                          @php $canEdit = $c->user_id === auth()->user()->user_id; @endphp
                                          
                                          @if($canDelete)
                                          <form class="comment-delete-form" action="{{ route('member.comment.destroy', ['id' => $c->comment_id]) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">ลบ</button>
                                          </form>
                                          @endif
                                          @endauth
                                        </div>
                                        @foreach($comments as $r)
                                          @if($r->parent_id === $c->comment_id)
                                          <div class="ms-3 mt-2 comment-block" data-comment-id="{{ $r->comment_id }}">
                                            @php $avatarR = $r->avatar_img ? asset('storage/'.$r->avatar_img) : 'https://cdn-icons-png.freepik.com/512/11121/11121549.png'; @endphp
                                            <div class="small text-muted d-flex align-items-center">
                                              <img src="{{ $avatarR }}" class="rounded-circle me-2" width="20" height="20" alt="avatar">
                                              <div>
                                                <span class="comment-username">{{ $r->username }}</span>
                                                @if(($r->role ?? null) === 'admin')
                                                  <span class="text-danger">(แอดมิน)</span>
                                                @endif
                                                • <span class="comment-created">{{ $r->created_at }}</span>
                                                @if(!empty($r->updated_at))
                                                  <span class="comment-edited"> • แก้ไขแล้ว</span>
                                                @endif
                                              </div>
                                            </div>
                                            <div class="comment-content">{{ $r->content }}</div>
                                            @auth
                                            @php $canDeleteR = ((auth()->user()->role ?? null) === 'admin') || $r->user_id === auth()->user()->user_id; @endphp
                                            @php $canEditR = $r->user_id === auth()->user()->user_id; @endphp
                                            
                                            @if($canDeleteR)
                                            <form class="comment-delete-form" action="{{ route('member.comment.destroy', ['id' => $r->comment_id]) }}" method="POST">
                                              @csrf
                                              @method('DELETE')
                                              <button type="submit" class="btn btn-sm btn-outline-danger">ลบ</button>
                                            </form>
                                            @endif
                                            @endauth
                                          </div>
                                          @endif
                                        @endforeach
                                      </div>
                                      @endif
                                    @endforeach
                                  </div>
                                </div>

                                @auth
                                <form class="comment-form mt-2" data-review-id="{{ $review->review_id }}" action="{{ route('member.review.comment.store', ['id' => $review->review_id]) }}" method="POST">
                                  @csrf
                                  <div class="input-group">
                                    <input type="text" name="content" class="form-control" placeholder="เขียนความคิดเห็น..." required>
                                    <button class="btn btn-primary" type="submit">ส่ง</button>
                                  </div>
                                </form>
                                @endauth

                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                </div>
            @endforeach
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $reviews->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection

@section('js_before')
<script>
  const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // AJAX like/unlike (toggle two forms) – ใช้ visibility กัน layout ขยับ
  document.querySelectorAll('form[data-like]').forEach(function(f){
    f.addEventListener('submit', async function(e){
      const likeType = this.getAttribute('data-like');
      if(!(likeType === 'like' || likeType === 'unlike')) return;
      e.preventDefault();
      const method = likeType === 'unlike' ? 'DELETE' : 'POST';
      const res = await fetch(this.action, { method, headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }});
      if(!res.ok) return;
      const data = await res.json();
      const wrapper = this.closest('.like-toggle');
      const reviewId = wrapper.getAttribute('data-review-id');

      // Toggle like/unlike for this review (all instances: card + modal)
      document.querySelectorAll('.like-toggle[data-review-id="'+reviewId+'"]').forEach(function(w){
        const likeForm = w.querySelector('form[data-like="like"]');
        const unlikeForm = w.querySelector('form[data-like="unlike"]');
        if (data.liked) { if (likeForm) likeForm.style.visibility = 'hidden'; if (unlikeForm) unlikeForm.style.visibility = 'visible'; }
        else { if (likeForm) likeForm.style.visibility = 'visible'; if (unlikeForm) unlikeForm.style.visibility = 'hidden'; }
        const heart = w.querySelector('.like-heart');
        if (heart) {
          heart.classList.remove('active','inactive');
          heart.classList.add(data.liked ? 'active' : 'inactive');
        }
        const countEl = w.querySelector('.like-count');
        if (countEl) {
          const val = (typeof data.up !== 'undefined') ? data.up : (typeof data.count !== 'undefined' ? data.count : countEl.textContent);
          countEl.textContent = val;
        }
      });

  // เปิด/ปิด Modal จัดการแบบ delegated กลางใน layout แล้ว (ลบ handler เฉพาะหน้านี้ออก)
</script>
<template id="comment-template">
  <div class="mb-2 comment-block">
    <div class="small text-muted d-flex align-items-center">
      <img class="comment-avatar rounded-circle me-2" width="20" height="20" alt="avatar">
      <div>
        <span class="comment-username"></span>
        <span class="comment-admin text-danger" style="display:none;"> (แอดมิน)</span>
        • <span class="comment-created"></span>
        <span class="comment-edited" style="display:none;"> • แก้ไขแล้ว</span>
      </div>
    </div>
    <div class="comment-content"></div>
  </div>
</template>
@endsection

@section('css_before')
<style>
  /* ⭐ เพิ่มขนาดไอคอนดาวและหัวใจ */
  .star-heart-scale { font-size: 120%; }

  /* ⬆️ Upvote arrow style */
  .vote-arrow {
    font-size: 1.6rem !important;
    display: inline-block;
    cursor: pointer;
    transition: transform 0.12s ease, color 0.2s ease;
    position: relative;
    line-height: 1;
  }

  .vote-arrow:hover { transform: translateY(-2px); }

  .vote-arrow.pop { animation: vote-pop 0.25s ease; }
  @keyframes vote-pop {
    0% { transform: translateY(0) scale(1); }
    50% { transform: translateY(-2px) scale(1.15); }
    100% { transform: translateY(0) scale(1); }
  }
  .like-heart.inactive { color:#9aa0a6; font-size: 2rem; }
  .like-heart.active { color:#e03131; font-size: 2rem; }
  .like-heart { font-size: 2rem; }
  /* Bigger heart only inside popup (modal) */
  .modal-body .like-heart { font-size: 2.6rem; line-height: 1; }
  /* Hover enlarge for heart icon */
  .like-heart { display:inline-block; transition: transform .15s ease, color .2s ease; }
  .like-heart:hover { transform: scale(1.25); }
  
  
  /* คงพื้นที่ปุ่มโหวตให้ไม่ขยับ */
  .vote-form { display:inline-block; width:28px; text-align:center; }
  .like-toggle { gap: 6px; }
  .like-count, .dislike-count {
    display: inline-block;
    min-width: 3ch;            /* กันกระดิกเมื่อเลขเปลี่ยนหลัก */
    text-align: center;
    font-variant-numeric: tabular-nums;
    font-feature-settings: 'tnum';
  }

  @keyframes sparkle {
    0% { transform: translate(-50%, -10px) scale(0); opacity: 0; }
    50% { transform: translate(-50%, -25px) scale(1.4); opacity: 1; }
    100% { transform: translate(-50%, -35px) scale(0.5); opacity: 0; }
  }

  /* Card layout */
  .card .card-img-top { height: 220px; object-fit: cover; }
  .card.fixed-size { width: 100%; max-width: 475px; height: 550px; cursor: pointer; }
  /* Slightly larger typography on cards */
  .card.fixed-size .card-title { font-size: 1.15rem; line-height: 1.35; }
  .card.fixed-size .card-text { font-size: 1.02rem; }
  .card.fixed-size .small, .card.fixed-size small { font-size: 0.925rem; }

  /* Shadow and hover */
  .card.fixed-size { 
    box-shadow: 0 12px 28px rgba(0,0,0,.28) !important; 
    transition: transform .15s ease, box-shadow .2s ease; 
  }
  .card.fixed-size:hover { 
    transform: translateY(-3px); 
    box-shadow: 0 18px 40px rgba(0,0,0,.38) !important; 
  }

  /* Modal style */
  .modal-dialog.modal-xl { max-width: 90vw; }
  .modal.fade .modal-dialog { transform: translateY(10px) scale(0.98); transition: transform 200ms ease, opacity 200ms ease; }
  .modal.show .modal-dialog { transform: translateY(0) scale(1); }
  .modal-content { box-shadow: 0 20px 60px rgba(0,0,0,.35); border-radius: 12px; }
  .modal-backdrop.show { opacity: .6; backdrop-filter: blur(2px); }

  .review-comments-list { max-height: 220px; overflow-y: auto; }
  /* Modal image size (reduced) */
  .modal-review-image { width: 100%; max-width: 500px; height: 500px; object-fit: cover; object-position: center; }
</style>
@endsection




