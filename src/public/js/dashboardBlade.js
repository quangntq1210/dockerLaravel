const DashboardManager = {
    selectors: {
        form: '#filterForm',
        table: '#campaignTable',
        totalCampaigns: '#total-campaigns',
        totalSubscribers: '#total-subscribers',
        langSwitcher: '#languageSwitcher'
    },


    handleResponse: function (response) {
        $(this.selectors.table).html(response.table);

        if (response.stats) {
            $(this.selectors.totalCampaigns).text(response.stats.total_campaigns);
            $(this.selectors.totalSubscribers).text(response.stats.total_subscribers);
        }

        if (response.lang) {
            this.applyLanguage(response.lang);
        }
    },


    applyLanguage: function (lang) {
        if (!lang) return;

        $('[data-lang]').each(function () {
            const key = $(this).data('lang');
            const keys = key.split('.');
            let value = lang;

            for (let i = 0; i < keys.length; i++) {
                if (value && value.hasOwnProperty(keys[i])) {
                    value = value[keys[i]];
                } else {
                    value = null;
                    break;
                }
            }

            if (value !== null && value !== undefined && value !== "") {
                $(this).text(value);
            }
        });
    },

    loadInitialLang: function (updateUrl, csrfToken) {
        const self = this;
        const locale = $(this.selectors.langSwitcher).val();

        $.ajax({
            url: updateUrl,
            type: "POST",
            data: {
                locale: locale,
                _token: csrfToken,
                _method: "PUT"
            },
            success: function (response) {

                self.applyLanguage(response.lang);
            }
        });
    },

    init: function (config) {
        const self = this;

        // Xử lý Filter Form
        $(this.selectors.form).on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
                data: $(this).serialize(),
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function (response) {
                    self.handleResponse(response);
                }
            });
        });

        $(document).on('click', `${this.selectors.table} .pagination a`, function (e) {
            e.preventDefault();
            const url = $(this).attr('href');
            $.ajax({
                url: url,
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                success: function (response) {
                    self.handleResponse(response);
                }
            });
        });


        $(this.selectors.langSwitcher).on('change', function () {
            const locale = $(this).val();
            $.ajax({
                url: config.localeUpdateUrl,
                method: "PUT",
                data: {
                    locale: locale,
                    _token: config.csrfToken,
                    withAdminPayload: true
                },
                success: function (response) {
                    self.handleResponse(response);
                }
            });
        });

        this.loadInitialLang(config.localeUpdateUrl, config.csrfToken);
    }
};