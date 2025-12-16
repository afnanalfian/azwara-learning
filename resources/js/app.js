import '../css/app.css';
import './bootstrap';
import './theme';
import './sidebar';
import './toast';

import Alpine from 'alpinejs'
import collapse from '@alpinejs/collapse'

import postTestQuestionPicker from './components/posttest-question-picker'

// expose ke global SEBELUM Alpine.start()
window.Alpine = Alpine
window.postTestQuestionPicker = postTestQuestionPicker

Alpine.plugin(collapse)
Alpine.start()

import Swal from "sweetalert2";

// GLOBAL SWEETALERT CONFIRM FOR ALL FORMS
document.addEventListener('submit', function (e) {
    const form = e.target;

    if (form.classList.contains('sweet-confirm')) {
        e.preventDefault();

        const message = form.dataset.message || "Yakin ingin melanjutkan tindakan ini?";

        Swal.fire({
            title: "Konfirmasi",
            text: message,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, lanjutkan",
            cancelButtonText: "Batal",
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
});

