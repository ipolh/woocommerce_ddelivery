(function ($) {
    $(document).ready(function () {
        if (typeof woocommerce_ddelivery_settings === 'undefined') {
            return void (0);
        }
        var settings = woocommerce_ddelivery_settings;
        var controller = {
            url: '',
            $errorContainer: $('<p/>', {
                css: {
                    'color': 'red',
                    'fontSize': '0.75em'
                }
            }),
            $eventbus: $({}).on('myCustomEvent', function (event, data) {
                switch (data.name) {
                    case 'init':
                        controller.init();
                        break;
                    case 'cart.get':
                        controller.getCart();
                        break;
                    case 'cart.recieved':
                        controller.getToken(data);
                        break;
                    case 'token.recieved':
                        controller.appendDiv();
                        break;
                    case 'load':
                        controller.loadSDK();
                        break;
                    case 'widget.price':
                        controller.savePrice(data);
                        controller.pub('checkout.updated');
                        break;
                    case 'widget.change':
                        controller.saveSDK(data);
                        controller.pub('checkout.updated');
                        break;
                    case 'checkout.update':
                        controller.updateCheckout(data);
                        break;
                    case 'checkout.updated':
                        controller.updatedCheckout(data);
                        break;
                }
            }),
            appended: false,
            log: function () {
                if (console && !!settings.debugMode) {
                    console.log.apply(console, arguments);
                }
            },
            pub: function (event, data) {
                if (!!settings.debugMode) {
                    console.groupCollapsed(event);
                    console.dir(data);
                    console.groupCollapsed('trace');
                    console.trace();
                    console.groupEnd();
                    console.groupEnd(event);
                }
                this.$eventbus.trigger('myCustomEvent', {
                    'name': event,
                    'data': data
                })
            },
            init: function () {
                var _ = this;
                $('body').on('updated_checkout', function () {
                    _.pub('checkout.updated');
                });
                $.getScript("https://sdk.ddelivery.ru/assets/js/ddelivery_v2.js?" + Math.random(), function () {
                    _.pub('cart.get');
                });
                if (!this.$form) {
                    this.$form = $('form[name=checkout]');
                    this.$form.on('click', '[type="submit"]', function () {
                        return controller.onSubmit();
                    })
                }
            },
            getCart: function () {
                $.get(settings.cart, function (data) {
                    controller.pub('cart.recieved', data)
                })
            },
            getToken: function (data) {
                controller.log(data.data);
                $.post(settings.token, {
                    'products': data.data,
                    'discount': 0,
                    'protocol': window.location.protocol,
                    'processData': false
                }, function (response) {
                    controller.url = response.url;
                    controller.pub('token.recieved', response);
                });
            },
            appendDiv: function () {
                if (this.appended) {
                    this.pub('load');
                    return;
                }
                var $target = $(settings.bindElement).eq(0);
                $target.append($('<div/>', {
                    id: settings.containerId,
                    css: {
                        background: 'transparent',
                        display: 'block',
                        width: '100%',
                        clear: 'both'
                    }
                })).append($('<div/>', {
                    html: this.$errorContainer
                }));
                var inline_style = '#' + settings.containerId + '>iframe{max-width:100%!important;';
                inline_style += '-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;}';
                $('head').append($('<style/>', {
                    text: inline_style,
                    type: 'text/css'
                }));
                this.appended = true;
                this.pub('load')
            },
            loadSDK: function () {
                var _ = this;
                var params = settings.ddeliveryParams;
                var urlParams = {};
                urlParams['to_street'] = '';
                urlParams['to_home'] = '';
                urlParams['to_flat'] = '';
                params.url = controller.url + '?' + $.param(urlParams);
                DDeliveryModule.init(params, {
                    open: function () {
                        _.pub('widget.open');
                        // без этого не пашет
                        return true;
                    },
                    resize_event: function () {
                        _.pub('widget.resize_event');
                    },
                    change: function (data) {
                        _.pub('widget.change', data);
                    },
                    close_map: function () {
                        _.pub('widget.close_map');
                    },
                    price: function (data) {
                        _.pub('widget.price', data);
                    }
                }, settings.containerId);
            },
            savePrice: function (data) {
                var _ = this;
                $.post(settings.save, data, function (response) {
                    if (response && response.status && response.status === 'ok') {
                        _.pub('checkout.update');
                    } else {
                        _.pub('error');
                    }
                });
            },
            updateCheckout: function (data) {
                /**
                 * @see woocommerce/assets/js/frontend/checkout.js
                 */
                $('body').trigger('update_checkout');
            },
            updatedCheckout: function () {
                if (typeof DDeliveryModule !== 'undefined') {
                    if (DDeliveryModule.validate()) {
                        controller.log('validating ddelivery success');
                        this.$errorContainer.text('').parent().hide();
                    } else {
                        controller.log('validating ddelivery failed');
                        this.$errorContainer.text(DDeliveryModule.getErrorMsg()).parent().show();
                    }
                }
            },
            debug: function (anything) {
                $.post(settings.debug, anything, function (response) {
                    controller.log(response);
                })
            },
            saveSDK: function (data) {
                var _ = this;
                var sdkId = data.data.id;
                if (this.sdkId && this.sdkId === sdkId) {
                    return _.pub('sdk.saved');
                }
                controller.log(data);
                $.post(settings.saveSDK, data.data, function (response) {
                    controller.log(response);
                    _.sdkId = sdkId;
                    return _.pub('sdk.saved');
                })
            },
            onSubmit: function () {
                DDeliveryModule.sendForm({
                    success: function () {
                        controller.log('submit success');
                        controller.$errorContainer.text('').parent().hide();
                        controller.$form.submit();
                    },
                    error: function () {
                        controller.log('submit prevented');
                        if (settings.debugMode) {
                            console.trace(DDeliveryModule.getErrorMsg());
                        }
                        controller.$errorContainer.text(DDeliveryModule.getErrorMsg()).parent().show();
                        $('html, body').animate({
                            scrollTop: ( $('#' + settings.containerId).offset().top  )
                        }, 1000);
                    }
                });
                return false;
            }
        };
        controller.pub('init');
    })
})(window.jQuery);