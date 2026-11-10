/* Contact form -> POST /contact (the server limits it to 5 messages per minute). */
Shop.pages.contact = function () {
    const $form = $('#contact-form');

    $form.on('submit', function (event) {
        event.preventDefault();
        const $button = $form.find('[type="submit"]');

        Shop.ui.busy($button, true);

        Shop.api.post('/contact', {
            name: $form.find('[name="name"]').val(),
            email: $form.find('[name="email"]').val(),
            phone: $form.find('[name="phone"]').val() || null,
            subject: $form.find('[name="subject"]').val(),
            message: $form.find('[name="message"]').val(),
            website: $form.find('[name="website"]').val(),   // hidden field for bots
        }).then(() => {
            Shop.ui.busy($button, false);
            $form[0].reset();
            Shop.ui.showErrors($form, {});
            Shop.ui.toast(Shop.t('contact.sent'), 'success');
        }).catch((error) => {
            Shop.ui.busy($button, false);
            error.status === 422 ? Shop.ui.showErrors($form, error.errors) : Shop.ui.error(error);
        });
    });
};

/* About us, privacy policy, return policy, terms: the text is written in the admin panel. */
Shop.pages.page = function () {
    const slug = $('#page-root').data('slug');

    Shop.api.get('/pages/' + encodeURIComponent(slug)).then((res) => {
        $('#page-title').text(res.data.title);
        document.title = res.data.title + ' | ' + APP.config.siteName;
        $('#page-content').html(Shop.sanitize(res.data.content));   // cleaned first (no scripts, no event attributes)
    }).catch((error) => {
        $('#page-content').html(Shop.ui.empty('bi-file-earmark-x', error.status === 404 ? Shop.t('pages.not_found') : error.message, ''));
    });
};
