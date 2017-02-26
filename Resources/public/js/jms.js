/**
 * JMSTranslationManager
 *
 * JS object that drives AJAX functionality on JMS Translation Bundle UI
 *
 *  Constructor arguments:
 *  @string updateMessagePath: uri to which translation data is sent
 *  @boolean isWritable: Whether the source translation files are actually writable
 *
 *  Configuration:
 *  @object domain
 *      @string selector: jquery selector for domain changer fields
 *      @function handlers: event handlers to be attached to domain fields
 *
 *  @object truncator
 *      @string selector: jquery selector for fields to be truncated (requires Trunk8 JQuery plugin)
 *      @string side: left|right side to truncate
 *      @string fill: html element to use to untruncate text
 *      @string untruncateSelector: jquery selector for fill field, used by handlers in default truncate function
 *      @function truncate: function that actually defines the truncation behaviour
 *
 *  @object translation
 *      @string selector: jquery selector for field that will contain the translation text
 *      @object ajax
 *          @string type: http request type for ajax request
 *          @object headers: http headers to be sent with request
 *          @string dataMethod: ajax _dataMethod
 *          @function beforeSend|error|success|complete: ajax request event handlers
 *          @string errorMessageContent|savedMessageContent|unsavedMessageContent: message text used by the default ajax request handlers
 *      @function blur: translation field onBlur handler
 *      @function focus: translation field onFocus handler
 *
 *  @function ready: inits the JMSTranslationManager
 *
 *  @function writable: attaches translation field handlers if isWritable is true
 */
function JMSTranslationManager(updateMessagePath, isWritable)
{
    if(!window.jQuery)
    {
        console.error('JMSTranslationManager requires JQuery.');
        return;
    }

    this.updateMessagePath = updateMessagePath;
    this.isWritable        = isWritable ? isWritable : false;

    this.domain  = {
        selector: '#config select',
        handlers: function(JMS)
        {
            $(JMS.domain.selector).change(function() {
                $(this).parent().submit();
            });
        }
    };

    this.truncator = {
        selector: '.truncate-left',
        side:     'left',
        fill:     '<a href="#" class="untruncate">&hellip;</a>',
        untruncateSelector: '.untruncate',
        truncate: function(JMS)
        {
            if(jQuery().trunk8)
            {
                $(JMS.truncator.selector).trunk8({
                    side: JMS.truncator.side,
                    fill: JMS.truncator.fill
                });

                $(document).on('click', JMS.truncator.untruncateSelector, function (event) {
                    var $elem = $(this);
                    $elem.parent().trunk8('revert');
                    event.preventDefault();
                });
            }
            else
            {
                console.error('Truncator requires jQuery Trunk8 plugin.');
            }
        }
    };

    this.translation = {
        selector: 'textarea',
        ajax: {
            type: 'POST',
            headers: {'X-HTTP-METHOD-OVERRIDE': 'PUT'},
            dataMethod: 'PUT',
            beforeSend: function(data, event, JMS)
            {
                var $elem = $(event.target);
                $elem.parent().closest('td').prev('td').children('.alert-message').remove();
            },
            error: function(data, event, JMS)
            {
                var $elem = $(event.target);
                $elem.parent().closest('td').prev('td').append(JMS.translation.ajax.errorMessageContent);
            },
            errorMessageContent: '<span class="alert-message label error">Could not be saved.</span>',
            success: function(data, event, JMS)
            {
                var $elem = $(event.target);

                if (data == 'Translation was saved')
                {
                    $elem.parent().closest('td').prev('td').append(JMS.translation.ajax.savedMessageContent);
                } else
                {
                    $elem.parent().closest('td').prev('td').append(JMS.translation.ajax.unsavedMessageContent);
                }
            },
            savedMessageContent: '<span class="alert-message label success">Translation was saved.</span>',
            unsavedMessageContent: '<span class="alert-message label error">Could not be saved.</span>',
            complete: function(data, event, JMS)
            {
                var $elem = $(event.target);
                var $parent = $elem.parent();
                $elem.data('timeoutId', setTimeout(function ()
                {
                    $elem.data('timeoutId', undefined);
                    $parent.closest('td').prev('td').children('.alert-message').fadeOut(300, function ()
                    {
                        var $message = $(this);
                        $message.remove();
                    });
                }, 10000));
            }
        },
        blur: function(event, JMS)
        {
            var $elem = $(event.target);
            $.ajax(JMS.updateMessagePath + '?id=' + encodeURIComponent($elem.data('id')), {
                type: JMS.translation.ajax.type,
                headers: JMS.translation.ajax.headers,
                data: {'_method': JMS.translation.ajax.dataMethod, 'message': $elem.val()},
                beforeSend: function (data)
                {
                    JMS.translation.ajax.beforeSend(data, event, JMS);
                },
                error: function (data)
                {
                    JMS.translation.ajax.error(data, event, JMS);
                },
                success: function (data)
                {
                    JMS.translation.ajax.success(data, event, JMS);
                },
                complete: function (data)
                {
                    JMS.translation.ajax.complete(data, event, JMS);
                }
            });
        },
        focus: function(event, JMS)
        {
            var $elem = $(event.target);
            $elem.select();

            var timeoutId = $elem.data('timeoutId');
            if (timeoutId)
            {
                clearTimeout(timeoutId);
                $elem.data('timeoutId', undefined);
            }

            $elem.parent().children('.alert-message').remove();
        }
    };

    this.ready = function()
    {
        var JMS = this;
        $(document).ready(function(event) {
            JMS.domain.handlers(JMS);
            JMS.truncator.truncate(JMS);
            if(JMS.isWritable)
            {
                JMS.writable(JMS);
            }
        });
    };

    this.writable = function(JMS)
    {
        $(JMS.translation.selector)
            .blur(function(event)
            {
                JMS.translation.blur(event, JMS);
            })
            .focus(function ()
            {
                JMS.translation.focus(event, JMS);
            })
        ;
    };
};