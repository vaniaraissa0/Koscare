/**
 * KosCare - JavaScript Interaktif
 * Menangani validasi client-side, preview foto, dan konfirmasi tindakan
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. Inisialisasi Tooltip Bootstrap jika ada
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // 2. Preview Gambar Unggahan & Validasi Ukuran (Maks 2MB)
    const photoInput = document.getElementById('report_photo');
    const photoPreviewContainer = document.getElementById('photo_preview_container');
    const photoPreviewImage = document.getElementById('photo_preview_image');
    const photoFeedback = document.getElementById('photo_feedback');

    if (photoInput) {
        photoInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (!file) {
                if (photoPreviewContainer) photoPreviewContainer.classList.add('d-none');
                return;
            }

            // Validasi Ukuran (Maks 2MB = 2 * 1024 * 1024 byte)
            const maxSize = 2 * 1024 * 1024;
            const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

            if (!allowedTypes.includes(file.type)) {
                alert('Format file tidak didukung! Harap unggah foto JPG, JPEG, PNG, atau WebP.');
                photoInput.value = '';
                if (photoPreviewContainer) photoPreviewContainer.classList.add('d-none');
                return;
            }

            if (file.size > maxSize) {
                alert('Ukuran file foto melebihi batas maksimal 2 MB!');
                photoInput.value = '';
                if (photoPreviewContainer) photoPreviewContainer.classList.add('d-none');
                return;
            }

            // Tampilkan Preview
            const reader = new FileReader();
            reader.onload = function (event) {
                if (photoPreviewImage) {
                    photoPreviewImage.src = event.target.result;
                }
                if (photoPreviewContainer) {
                    photoPreviewContainer.classList.remove('d-none');
                }
                if (photoFeedback) {
                    photoFeedback.textContent = `${file.name} (${(file.size / 1024).toFixed(1)} KB) siap diunggah.`;
                }
            };
            reader.readAsDataURL(file);
        });
    }

    // 3. Konfirmasi Tindakan Hapus
    const deleteButtons = document.querySelectorAll('.btn-confirm-delete');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            const itemName = this.getAttribute('data-item-name') || 'laporan ini';
            const confirmed = confirm(`Apakah Anda yakin ingin menghapus ${itemName}? Tindakan ini tidak dapat dibatalkan.`);
            if (!confirmed) {
                e.preventDefault();
            }
        });
    });

    // 4. Form Ubah Status Dinamis pada Pengelola
    const statusSelect = document.getElementById('new_status_select');
    const completedFields = document.getElementById('completed_extra_fields');
    const notesLabel = document.getElementById('status_notes_label');
    const notesInput = document.getElementById('status_notes');

    if (statusSelect) {
        function updateStatusFormUI() {
            const val = statusSelect.value;
            if (val === 'Selesai') {
                if (completedFields) completedFields.classList.remove('d-none');
                if (notesLabel) notesLabel.innerHTML = 'Catatan Hasil Perbaikan <span class="text-danger">*</span>';
                if (notesInput) {
                    notesInput.setAttribute('required', 'required');
                    notesInput.placeholder = 'Jelaskan tindakan perbaikan yang telah dilakukan dan komponen yang diganti...';
                }
            } else if (val === 'Ditolak') {
                if (completedFields) completedFields.classList.add('d-none');
                if (notesLabel) notesLabel.innerHTML = 'Alasan Penolakan <span class="text-danger">*</span>';
                if (notesInput) {
                    notesInput.setAttribute('required', 'required');
                    notesInput.placeholder = 'Berikan alasan mengapa laporan ini ditolak...';
                }
            } else if (val === 'Menunggu Informasi') {
                if (completedFields) completedFields.classList.add('d-none');
                if (notesLabel) notesLabel.innerHTML = 'Keterangan Informasi yang Diperlukan <span class="text-danger">*</span>';
                if (notesInput) {
                    notesInput.setAttribute('required', 'required');
                    notesInput.placeholder = 'Tuliskan detail informasi atau foto tambahan yang dibutuhkan dari penghuni...';
                }
            } else {
                if (completedFields) completedFields.classList.add('d-none');
                if (notesLabel) notesLabel.innerHTML = 'Catatan Perubahan Status (Opsional)';
                if (notesInput) {
                    notesInput.removeAttribute('required');
                    notesInput.placeholder = 'Tambahkan catatan jika diperlukan...';
                }
            }
        }

        statusSelect.addEventListener('change', updateStatusFormUI);
        updateStatusFormUI(); // Inisialisasi awal
    }
});
