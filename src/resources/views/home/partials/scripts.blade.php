<script>
  const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  // AJAX like/unlike (toggle two forms)
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
      const countEl = footer ? footer.querySelector('span.text-muted') : null;
      if (countEl && data && typeof data.count !== 'undefined') { countEl.textContent = data.count; }
      const likeForm = wrapper.querySelector('form[data-like="like"]');
      const unlikeForm = wrapper.querySelector('form[data-like="unlike"]');
      if (data.liked) { if (likeForm) likeForm.style.display = 'none'; if (unlikeForm) unlikeForm.style.display = 'inline'; }
      else { if (likeForm) likeForm.style.display = 'inline'; if (unlikeForm) unlikeForm.style.display = 'none'; }
      const heart = wrapper.querySelector('.like-heart');
      if (heart) { heart.style.color = data.liked ? '#e03131' : '#9aa0a6'; heart.classList.add('pop'); heart.addEventListener('animationend', function handler(){ heart.classList.remove('pop'); heart.removeEventListener('animationend', handler); }); }
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
      node.querySelector('.comment-username').textContent = data.comment.username;
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

  // AJAX delete comment
  document.querySelectorAll('form.comment-delete-form').forEach(function(f){
    f.addEventListener('submit', async function(e){
      e.preventDefault();
      const res = await fetch(this.action, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }});
      if(!res.ok) return; const data = await res.json(); if(!data.success) return;
      const block = this.closest('.comment-block'); if (block) block.remove();
    });
  });

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
