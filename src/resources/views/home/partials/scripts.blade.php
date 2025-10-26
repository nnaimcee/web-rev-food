<script>
  const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  // AJAX upvote/unupvote (toggle two forms)
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
      const footer = this.closest('.card-footer, .modal-body');
      const likeEl = footer ? footer.querySelector('.like-count') : null; const dislikeEl = footer ? footer.querySelector('.dislike-count') : null; if (likeEl && typeof data.up !== 'undefined') likeEl.textContent = data.up; if (dislikeEl && typeof data.down !== 'undefined') dislikeEl.textContent = data.down;
      const likeForm = wrapper.querySelector('form[data-like="like"]');
      const unlikeForm = wrapper.querySelector('form[data-like="unlike"]');
      if (data.liked) { if (likeForm) likeForm.style.visibility = 'hidden'; if (unlikeForm) unlikeForm.style.visibility = 'visible'; }
      else { if (likeForm) likeForm.style.visibility = 'visible'; if (unlikeForm) unlikeForm.style.visibility = 'hidden'; }
      const up = wrapper.querySelector('.vote-up');
      if (up) { up.style.color = data.liked ? '#198754' : '#9aa0a6'; up.classList.add('pop'); up.addEventListener('animationend', function handler(){ up.classList.remove('pop'); up.removeEventListener('animationend', handler); }); }
      const down = wrapper.querySelector('.vote-down'); if (down) { down.style.color = '#9aa0a6'; }
    });
  });

  // AJAX downvote/undownvote (toggle two forms)
  document.querySelectorAll('form[data-down]').forEach(function(f){
    f.addEventListener('submit', async function(e){
      const downType = this.getAttribute('data-down');
      if(!(downType === 'downvote' || downType === 'undownvote')) return;
      e.preventDefault();
      const method = downType === 'undownvote' ? 'DELETE' : 'POST';
      const res = await fetch(this.action, { method, headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }});
      if(!res.ok) return; 
      const data = await res.json();
      const wrapper = this.closest('.like-toggle');
      const footer = this.closest('.card-footer, .modal-body');
      const likeEl = footer ? footer.querySelector('.like-count') : null; const dislikeEl = footer ? footer.querySelector('.dislike-count') : null; if (likeEl && typeof data.up !== 'undefined') likeEl.textContent = data.up; if (dislikeEl && typeof data.down !== 'undefined') dislikeEl.textContent = data.down;
      const dvForm = wrapper.querySelector('form[data-down="downvote"]');
      const undvForm = wrapper.querySelector('form[data-down="undownvote"]');
      const upForm = wrapper.querySelector('form[data-like="like"]');
      const unupForm = wrapper.querySelector('form[data-like="unlike"]');
      if (data.downvoted) { if (dvForm) dvForm.style.display='none'; if (undvForm) undvForm.style.display='inline'; if (unupForm) unupForm.style.display='none'; if (upForm) upForm.style.display='inline'; }
      else { if (dvForm) dvForm.style.display='inline'; if (undvForm) undvForm.style.display='none'; }
      const up2 = wrapper.querySelector('.vote-up'); if (up2) { up2.style.color = '#9aa0a6'; }
      const down2 = wrapper.querySelector('.vote-down'); if (down2) { down2.style.color = data.downvoted ? '#dc3545' : '#9aa0a6'; down2.classList.add('pop'); down2.addEventListener('animationend', function handler(){ down2.classList.remove('pop'); down2.removeEventListener('animationend', handler); }); }
    });
  });

  // AJAX comment submit (top-level and reply)
  function wireCommentForm(form){
    form.addEventListener('submit', async function(e){
      e.preventDefault();
      const fd = new FormData(this);
      const res = await fetch(this.action, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }, body: fd });
      if(!res.ok) return;
      const data = await res.json();
      if(!data.success) return;
      const reviewId = this.getAttribute('data-review-id');
      const list = document.querySelector('.review-comments[data-review-id="'+reviewId+'"] .review-comments-list');
      const isReply = fd.get('parent_id');
      const tpl = document.getElementById('comment-template');
      const node = tpl.content.cloneNode(true);
      const userSpan = node.querySelector('.comment-username');
      if (userSpan) {
        userSpan.textContent = data.comment.username;
        const adminBadge = node.querySelector('.comment-admin');
        if (adminBadge) adminBadge.style.display = (data.comment && data.comment.role === 'admin') ? '' : 'none';
      }
      const avatar = node.querySelector('.comment-avatar');
      if (avatar) {
        const src = data.comment && data.comment.avatar_img ? (data.comment.avatar_img.startsWith('http') ? data.comment.avatar_img : ('/storage/'+data.comment.avatar_img)) : 'https://cdn-icons-png.freepik.com/512/11121/11121549.png';
        avatar.src = src;
      }
      node.querySelector('.comment-created').textContent = data.comment.created_at;
      node.querySelector('.comment-content').textContent = data.comment.content;
      const block = node.querySelector('.comment-block');
      if (isReply) block.classList.add('ms-3', 'mt-2');
      list.appendChild(node);
      const input = this.querySelector('input[name="content"]'); if(input) input.value = '';
    });
  }
  document.querySelectorAll('form.comment-form').forEach(wireCommentForm);
  document.querySelectorAll('form.reply-form').forEach(wireCommentForm);

  // การลบคอมเมนต์แบบ AJAX ถูกทำแบบ delegated ใน layout แล้ว

  // Open modal on card click (except on interactive elements)
  function isInteractive(el){ return el.closest('button, a, input, textarea, select, label, form, .like-toggle'); }
  document.querySelectorAll('.js-open-modal').forEach(function(card){
    card.addEventListener('click', function(e){
      if (isInteractive(e.target)) return;
      const id = this.getAttribute('data-modal-id');
      const el = document.getElementById(id);
      if (el && window.bootstrap && bootstrap.Modal) { const m = bootstrap.Modal.getOrCreateInstance(el); m.show(); }
    });
  });
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


