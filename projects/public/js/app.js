
document.addEventListener('DOMContentLoaded', function () {
    // Fitur lihat password login
    const passwordLogin = document.getElementById('loginPassword');
    const tombolLihat = document.getElementById('lihatPassword');

    if (passwordLogin && tombolLihat) {
        tombolLihat.addEventListener('click', function () {
            const terlihat = passwordLogin.type === 'text';

            passwordLogin.type = terlihat ? 'password' : 'text';
            tombolLihat.textContent = terlihat ? 'Lihat' : 'Sembunyikan';
        });
    }

    // Fitur lihat password registrasi
    const tampilPassword = document.getElementById('tampilPassword');

    if (tampilPassword) {
        tampilPassword.addEventListener('change', function () {
            const tipe = this.checked ? 'text' : 'password';

            document.getElementById('registerPassword').type = tipe;
            document.getElementById('confirmPassword').type = tipe;
        });
    }

    // Format Rupiah
    ['budget_min', 'budget_max'].forEach(function (id) {
        const input = document.getElementById(id);

        if (input) {
            input.addEventListener('input', function () {
                const angka = input.value.replace(/\D/g, '');

                input.value = angka.replace(
                    /\B(?=(\d{3})+(?!\d))/g,
                    '.'
                );
            });
        }
    });

    // Konfirmasi tindakan
    document.querySelectorAll('form[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!window.confirm(form.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });
});

window.addEventListener('pageshow', function (event) {
    if (event.persisted) {
        window.location.reload();
    }
});
