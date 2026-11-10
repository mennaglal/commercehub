/* Login and register. After a successful login the visitor goes back to the page he came from (?redirect=...). */

// Only a path of THIS website is accepted (never another site): no "open redirect".
const safeRedirect = (path) => (typeof path === 'string' && path.charAt(0) === '/' && path.charAt(1) !== '/' && path.charAt(1) !== '\\') ? path : '/';

Shop.pages.login = function () {
    if (Shop.auth.isLoggedIn()) { window.location.href = safeRedirect(Shop.qs.get().redirect); return; }

    const $form = $('#login-form');

    $form.on('submit', function (event) {
        event.preventDefault();
        const $button = $form.find('[type="submit"]');

        Shop.ui.busy($button, true);

        Shop.api.post('/login', { email: $form.find('[name="email"]').val(), password: $form.find('[name="password"]').val() }, { silent: true }).then((res) => {
            Shop.auth.login(res.data.token, res.data.user);
            window.location.href = safeRedirect(Shop.qs.get().redirect);
        }).catch((error) => {
            Shop.ui.busy($button, false);
            error.status === 422 ? Shop.ui.showErrors($form, error.errors) : $form.find('.form-errors').removeClass('d-none').text(error.message);
        });
    });
};

Shop.pages.register = function () {
    if (Shop.auth.isLoggedIn()) { window.location.href = '/'; return; }

    const $form = $('#register-form');

    $form.on('submit', function (event) {
        event.preventDefault();
        const $button = $form.find('[type="submit"]');

        Shop.ui.busy($button, true);

        Shop.api.post('/register', {
            name: $form.find('[name="name"]').val(),
            email: $form.find('[name="email"]').val(),
            phone: $form.find('[name="phone"]').val() || null,
            password: $form.find('[name="password"]').val(),
            password_confirmation: $form.find('[name="password_confirmation"]').val(),
        }, { silent: true }).then((res) => {
            Shop.auth.login(res.data.token, res.data.user);
            window.location.href = safeRedirect(Shop.qs.get().redirect);
        }).catch((error) => {
            Shop.ui.busy($button, false);
            error.status === 422 ? Shop.ui.showErrors($form, error.errors) : $form.find('.form-errors').removeClass('d-none').text(error.message);
        });
    });
};
