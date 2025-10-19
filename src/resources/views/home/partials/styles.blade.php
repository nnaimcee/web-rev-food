<style>
  /* ไอคอนและแอนิเมชันหัวใจ/ดาว */
  .star-heart-scale { font-size: 120%; }
  .like-heart { font-size: 150%; display:inline-block; }
  .like-heart.pop { animation: heart-pop 300ms ease; }
  @keyframes heart-pop { 0% { transform: scale(1); } 50% { transform: scale(1.4); } 100% { transform: scale(1); } }

  /* การ์ดและภาพ (ครอปไม่ใช้ภาพเต็ม) */
  .card.fixed-size { width:100%; max-width:450px; height:500px; cursor:pointer; }
  .card .card-img-top { height: 220px; object-fit: cover; }

  /* รายการคอมเมนต์ย่อย */
  .review-comments-list { max-height: 160px; overflow-y: auto; }

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
