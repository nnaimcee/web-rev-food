<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'FoodReview - Guest')</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  @yield('css_before')
</head>
<body class="bg-light d-flex flex-column min-vh-100">

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container">
      <a class="navbar-brand fw-bold" href="{{ url('/') }}">FoodReview</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a href="{{ route('login.get') }}" class="nav-link">เข้าสู่ระบบ</a></li>
          <li class="nav-item"><a href="{{ route('register.get') }}" class="nav-link">สมัครสมาชิก</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <main class="flex-grow-1">
    @yield('header')
    <div class="container py-4">
      @yield('content')
    </div>
  </main>

  <footer class="bg-dark text-center text-white py-3 mt-auto">
    <div class="container">
      <p class="mb-0">© {{ date('Y') }} FoodReview</p>
    </div>
  </footer>
  @yield('footer')

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    (function(){
      if (window.bootstrap && window.bootstrap.Modal) return;
      var tried = 0;
      var urls = [
        'https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js',
        'https://unpkg.com/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js'
      ];
      function loadNext(){
        if (window.bootstrap && window.bootstrap.Modal) return;
        if (tried >= urls.length) return;
        var s = document.createElement('script');
        s.src = urls[tried++];
        s.async = true;
        s.onload = function(){};
        s.onerror = loadNext;
        document.head.appendChild(s);
      }
      setTimeout(function(){ if (!(window.bootstrap && window.bootstrap.Modal)) loadNext(); }, 400);
    })();
  </script>
  <script>
    (function(){
      function isInteractive(target){
        return target.closest('button, a, input, textarea, select, label');
      }
      function isBlockedForModal(target){
        if (target.closest('button, input, textarea, select, label, form, .like-toggle')) return true;
        var a = target.closest('a');
        if (a && !a.hasAttribute('data-bs-toggle')) return true;
        return false;
      }
      document.addEventListener('click', function(e){
        var card = e.target.closest('.js-open-modal');
        if (card) {
          if (isBlockedForModal(e.target)) return;
          var id = card.getAttribute('data-modal-id');
          var el = id ? document.getElementById(id) : null;
          if (el && window.bootstrap && bootstrap.Modal) {
            e.preventDefault();
            bootstrap.Modal.getOrCreateInstance(el).show();
          }
          return;
        }
        var a = e.target.closest('a[data-bs-toggle="modal"][data-bs-target]');
        if (a) {
          var sel = a.getAttribute('data-bs-target');
          var modalEl = sel ? document.querySelector(sel) : null;
          if (modalEl && window.bootstrap && bootstrap.Modal) {
            e.preventDefault();
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
          }
        }
      });

      // Delegated: toggle/show inline reply form under a comment
      document.addEventListener('click', function(e){
        var btn = e.target.closest('.reply-toggle');
        if (!btn) return;
        e.preventDefault();
        var commentBlock = btn.closest('.comment-block');
        var container = btn.closest('.review-comments');
        if (!container) {
          var modal = btn.closest('.modal');
          if (modal) container = modal.querySelector('.review-comments');
        }
        if (!container) {
          container = document.querySelector('.review-comments[data-review-id]');
        }
        if (!commentBlock || !container) return;
        var reviewId = container.getAttribute('data-review-id');
        var postUrl = container.getAttribute('data-post-url');
        var parentId = btn.getAttribute('data-parent-id');
        var existing = commentBlock.querySelector('form.reply-form');
        if (existing) { existing.classList.toggle('d-none'); return; }
        var form = document.createElement('form');
        form.className = 'reply-form mt-2';
        form.method = 'POST';
        form.action = postUrl;
        form.setAttribute('data-review-id', reviewId);
        form.innerHTML = '<input type="hidden" name="parent_id" value="'+(parentId||'')+'">'
          + '<div class="input-group">'
          +   '<input type="text" name="content" class="form-control" placeholder="ตอบกลับ..." required>'
          +   '<button class="btn btn-primary" type="submit">ส่ง</button>'
          + '</div>';
        commentBlock.appendChild(form);
      });

      // Delegated AJAX: submit top-level comment and reply
      document.addEventListener('submit', async function(e){
        var f = e.target;
        var isComment = f.matches && (f.matches('form.comment-form') || f.matches('form.reply-form'));
        if (!isComment) return;
        e.preventDefault();
        try {
          var csrf = document.querySelector('meta[name="csrf-token"]');
          var token = csrf ? csrf.getAttribute('content') : '';
          var fd = new FormData(f);
          var res = await fetch(f.action, { method: 'POST', headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }, body: fd });
          if (!res.ok) return;
          var data = await res.json();
          if (!data || !data.success) return;
          var reviewId = f.getAttribute('data-review-id');
          var list = document.querySelector('.review-comments[data-review-id="'+reviewId+'"] .review-comments-list');
          if (!list) return;
          var tpl = document.getElementById('comment-template');
          var node = tpl ? tpl.content.cloneNode(true) : null;
          var blockEl;
          if (node) {
            var userSpan = node.querySelector('.comment-username');
            if (userSpan) userSpan.textContent = data.comment.username || '';
            var adminBadge = node.querySelector('.comment-admin');
            if (adminBadge) adminBadge.style.display = (data.comment && data.comment.role === 'admin') ? '' : 'none';
            var avatar = node.querySelector('.comment-avatar');
      if (avatar) {
              var src = data.comment && data.comment.avatar_img ? (String(data.comment.avatar_img).startsWith('http') ? data.comment.avatar_img : ('/storage/'+data.comment.avatar_img)) : 'https://cdn-icons-png.freepik.com/512/11121/11121549.png';
              avatar.src = src;
            }
            var created = node.querySelector('.comment-created');
            if (created) created.textContent = data.comment.created_at || '';
            var content = node.querySelector('.comment-content');
            if (content) content.textContent = data.comment.content || '';
            blockEl = node.querySelector('.comment-block');
          } else {
            var div = document.createElement('div');
            div.className = 'mb-2 comment-block';
            div.innerHTML = '<div class="small text-muted">'+(data.comment.username||'')+' • '+(data.comment.created_at||'')+'</div><div class="comment-content"></div>';
            div.querySelector('.comment-content').textContent = data.comment.content || '';
            blockEl = div;
            node = document.createDocumentFragment();
            node.appendChild(div);
          }
          if (fd.get('parent_id')) { blockEl.classList.add('ms-3','mt-2'); }
          list.appendChild(node);
          var input = f.querySelector('input[name="content"]'); if (input) input.value = '';
        } catch(err) { /* noop */ }
      });

      // Delegated AJAX: like/unlike without page reload
      document.addEventListener('submit', async function(e){
        var f = e.target && e.target.closest('form[data-like]');
        if (!f) return;
        e.preventDefault();
        try {
          var likeType = f.getAttribute('data-like');
          if (!(likeType === 'like' || likeType === 'unlike')) return;
          var csrf = document.querySelector('meta[name="csrf-token"]');
          var token = csrf ? csrf.getAttribute('content') : '';
          var method = likeType === 'unlike' ? 'DELETE' : 'POST';
          var res = await fetch(f.action, { method: method, headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }});
          if (!res.ok) return;
          var data = await res.json();
          var wrap = f.closest('.like-toggle');
          var reviewId = (wrap && wrap.getAttribute('data-review-id')) || '';
          document.querySelectorAll('.like-toggle'+(reviewId?('[data-review-id="'+reviewId+'"]'):'')).forEach(function(w){
            var likeForm = w.querySelector('form[data-like="like"]');
            var unlikeForm = w.querySelector('form[data-like="unlike"]');
            if (data.liked) {
              if (likeForm) likeForm.style.visibility = 'hidden';
              if (unlikeForm) unlikeForm.style.visibility = 'visible';
            } else {
              if (likeForm) likeForm.style.visibility = 'visible';
              if (unlikeForm) unlikeForm.style.visibility = 'hidden';
            }
            var heart = w.querySelector('.like-heart');
            if (heart) { heart.classList.remove('active','inactive'); heart.classList.add(data.liked ? 'active' : 'inactive'); }
            var countEl = w.querySelector('.like-count');
            if (countEl) { var val = (typeof data.up !== 'undefined') ? data.up : (typeof data.count !== 'undefined' ? data.count : countEl.textContent); countEl.textContent = val; }
          });
        } catch(err) { /* noop */ }
      });

      // Delegated AJAX delete for comment forms
      document.addEventListener('submit', async function(e){
        var f = e.target && e.target.closest('form.comment-delete-form');
        if (!f) return;
        e.preventDefault();
        try {
          var csrf = document.querySelector('meta[name="csrf-token"]');
          var token = csrf ? csrf.getAttribute('content') : '';
          var res = await fetch(f.action, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }});
          if (!res.ok) return;
          var data = null; try { data = await res.json(); } catch(_) {}
          if (data && data.success === false) return;
          var block = f.closest('.comment-block');
          if (block) block.remove(); else f.remove();
        } catch(err) { /* noop */ }
      });
    })();
  </script>
  @yield('js_before')
</body>
</html>
