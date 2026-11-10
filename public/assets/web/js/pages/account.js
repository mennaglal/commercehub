/* My profile: personal data, location, picture and password. */
Shop.pages.account = function () {
    if (!Shop.auth.require()) { return; }

    const $form = $('#profile-form');

    Shop.api.get('/profile').then((res) => {
        const user = res.data.user;

        Shop.auth.setUser(user);
        $form.find('[name="name"]').val(user.name);
        $form.find('[name="email"]').val(user.email);
        $form.find('[name="phone"]').val(user.phone || '');
        $('#avatar-img').attr('src', Shop.img(user.avatar_url));
        $('#profile-name').text(user.name);

        return Shop.location.init({
            $country: $('#country_id'), $city: $('#city_id'), $area: $('#area_id'),
            values: { country: user.country_id, city: user.city_id, area: user.area_id },
        });
    }).catch(Shop.ui.error);

    // ---- personal data
    $form.on('submit', function (event) {
        event.preventDefault();
        const $button = $form.find('[type="submit"]');
        const id = (selector) => ($(selector).val() ? parseInt($(selector).val(), 10) : null);

        Shop.ui.busy($button, true);

        Shop.api.put('/profile', {
            name: $form.find('[name="name"]').val(),
            phone: $form.find('[name="phone"]').val() || null,
            country_id: id('#country_id'), city_id: id('#city_id'), area_id: id('#area_id'),
        }).then((res) => {
            Shop.auth.setUser(res.data.user);
            $('.js-user-name, #profile-name').text(res.data.user.name);
            Shop.ui.busy($button, false);
            Shop.ui.showErrors($form, {});
            Shop.ui.toast(Shop.t('account.saved'), 'success');
        }).catch((error) => {
            Shop.ui.busy($button, false);
            error.status === 422 ? Shop.ui.showErrors($form, error.errors) : Shop.ui.error(error);
        });
    });

    // ---- picture
    $('#avatar-input').on('change', function () {
        const file = this.files[0];

        if (!file) { return; }

        const body = new FormData();
        body.append('avatar', file);

        Shop.api.post('/profile/avatar', body).then((res) => {
            Shop.auth.setUser(res.data.user);
            $('#avatar-img').attr('src', Shop.img(res.data.user.avatar_url));
            Shop.ui.toast(Shop.t('account.saved'), 'success');
        }).catch((error) => {
            error.status === 422 ? Shop.ui.toast([].concat(Object.values(error.errors))[0], 'error') : Shop.ui.error(error);
        });
    });

    // ---- password
    const $password = $('#password-form');

    $password.on('submit', function (event) {
        event.preventDefault();
        const $button = $password.find('[type="submit"]');

        Shop.ui.busy($button, true);

        Shop.api.put('/profile/password', {
            current_password: $password.find('[name="current_password"]').val(),
            password: $password.find('[name="password"]').val(),
            password_confirmation: $password.find('[name="password_confirmation"]').val(),
        }).then(() => {
            Shop.ui.busy($button, false);
            $password[0].reset();
            Shop.ui.showErrors($password, {});
            Shop.ui.toast(Shop.t('account.password_changed'), 'success');
        }).catch((error) => {
            Shop.ui.busy($button, false);
            error.status === 422 ? Shop.ui.showErrors($password, error.errors) : Shop.ui.error(error);
        });
    });
};
