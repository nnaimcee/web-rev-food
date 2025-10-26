<style>
  /* ไอคอนและแอนิเมชันดาว/อัปโหวต */
  .star-heart-scale { font-size: 120%; }
  .vote-arrow { font-size: 150%; display:inline-block; }
  .vote-arrow.pop { animation: vote-pop 250ms ease; }
  @keyframes vote-pop { 0% { transform: translateY(0) scale(1); } 50% { transform: translateY(-2px) scale(1.15); } 100% { transform: translateY(0) scale(1); } }
  .vote-arrow.inactive { filter: grayscale(100%); opacity: .6; }
  .vote-up.active { color: #198754 !important; }
  .vote-down.active { color: #dc3545 !important; }

  /* กันปุ่ม+ตัวเลขกระดิกเวลา toggle */
  .vote-form { display:inline-block; width:28px; text-align:center; }
  .like-toggle { gap: 6px; }
  .like-count, .dislike-count { display:inline-block; min-width:3ch; text-align:center; font-variant-numeric: tabular-nums; font-feature-settings: 'tnum'; }
  /* ฟอร์มที่ถูกซ่อนด้วย visibility ไม่ดักคลิก เพื่อให้คลิกการ์ดเปิด modal ได้ */
  .vote-form[style*="visibility: hidden"] { pointer-events: none; }

  /* การ์ดและภาพ (ครอปไม่ใช้ภาพเต็ม) */
  .card.fixed-size { width:100%; max-width:450px; height:500px; cursor:pointer; }
  .card .card-img-top { height: 220px; object-fit: cover; }

  /* รายการคอมเมนต์ย่อย */
  .review-comments-list { max-height: 200px; overflow-y: auto; padding-right: 4px; }

  /* ปรับหน้าตาคอมเมนต์ให้ดูดีขึ้น */
  .comment-block {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-left: 4px solid #0d6efd;
    border-radius: 10px;
    padding: 8px 12px;
  }
  .comment-block + .comment-block { margin-top: 8px; }
  .comment-block .small.text-muted { font-size: .84rem; margin-bottom: 4px; }
  .comment-content { white-space: pre-wrap; }

  /* Modal เกือบเต็มจอ + แอนิเมชัน + เงา */
  .modal-dialog.modal-xl { max-width: 90vw; }
  .modal.fade .modal-dialog { transform: translateY(10px) scale(0.98); transition: transform 200ms ease, opacity 200ms ease; }
  .modal.show .modal-dialog { transform: translateY(0) scale(1); }
  .modal-content { box-shadow: 0 20px 60px rgba(0,0,0,.35); border-radius: 12px; }
  .modal-backdrop.show { opacity: .6; backdrop-filter: blur(2px); }

  /* เงาการ์ดเข้ม + hover */
  .card.fixed-size { box-shadow: 0 12px 28px rgba(0,0,0,.28) !important; transition: transform .15s ease, box-shadow .2s ease; }
  .card.fixed-size:hover { transform: translateY(-3px); box-shadow: 0 18px 40px rgba(0,0,0,.38) !important; }

  /* รูปใน modal ครอป 800x800 */
  .modal-review-image { width: 100%; max-width: 450px; height: 450px; object-fit: cover; object-position: center; }
</style>
