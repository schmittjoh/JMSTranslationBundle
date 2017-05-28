/**
 * JMSTranslationManager
 *
 * JS object that drives AJAX functionality on JMS Translation Bundle UI
 *
 *  Constructor arguments:
 *  @string updateMessagePath: uri to which translation data is sent
 *  @boolean isWritable: Whether the source translation files are actually writable
 *  @boolean isXliff: Whether or not the file is xliff (used for feature enabling)
 *
 *  Configuration:
 *  @object domain
 *      @string selector: jquery selector for domain changer fields
 *      @function handlers: event handlers to be attached to domain fields
 *
 *  @object copier
 *      @string selector: jquery selector for the translations that can be copied
 *      @string copyAllLabel: label for the copy all select box
 *      @string copyAllSelector: jquery selector for the copy all select dropdown
 *      @string copyLinkContent: link to dynamically write in to each alternative translation
 *      @string copySelector: jquery selector for the copy link (to bind the event handler)
 *      @string localeSelector: jquery selector for the main translator locale dropdown
 *      @string headerSelector: jquery selector for the header section of the UI
 *      @function elements: adds the controls for copying into the UI
 *      @function handlers: attaches the event handlers for copying
 *      @function copy: copies the selected alternate translation to the active translation
 *      @function copyClick: event handler for copying alternate translations to the current locale
 *      @function copyAll: event handler, calls copy event for all alternate translations of the chosen locale
 *
 *  @object fadeAndRemove
 *      @object element: element to fade and remove
 *      @boolean fast:   if true, avoids fade in and delay
 *
 *  @object notes
 *      @string addNoteLinkContent: HTML for the "Add Note" link
 *      @string addNoteSelector: jquery selector to get the add note link
 *      @string columnSelector: jquery selector to get the first column
 *      @string deleteNoteSelector: jquery selector to get the delete note link
 *      @string deleteNoteLinkContent: HTML for the "Delete Note" link
 *      @string noteLabel: HTML to use for labelling the notes (when dynamically created)
 *      @string noteSelector: jquery selector to get the notes themselves
 *      @function addNote: Adds a new note field and label to the UI
 *      @function checkEmpty: Checks to see if a note is empty, and removes it from the UI if it is
 *      @function delete: Event handler for clicking the "Delete Note" link
 *      @function elements: Creates the UI elements for manipulating notes
 *      @function handlers: event handlers to be attached to the note UI
 *
 *
 *  @object truncator
 *      @string selector: jquery selector for fields to be truncated (requires Trunk8 JQuery plugin)
 *      @string side: left|right side to truncate
 *      @string fill: html element to use to untruncate text
 *      @string untruncateSelector: jquery selector for fill field, used by handlers in default truncate function
 *      @function truncate: function that actually defines the truncation behaviour
 *
 *  @object translation
 *      @string abbrSelector: jquery selector to get the abbr tag (to get the ID)
 *      @string selector: jquery selector for field that will contain the translation text
 *      @object ajax
 *          @string type: http request type for ajax request
 *          @object headers: http headers to be sent with request
 *          @string dataMethod: ajax _dataMethod
 *          @function error|success: ajax request event handlers
 *          @string errorMessageContent|savedMessageContent|unsavedMessageContent: message text used by the default ajax request handlers
 *      @function blur: translation field onBlur handler
 *      @function focus: translation field onFocus handler
 *
 *  @function ready: inits the JMSTranslationManager
 *
 *  @function writable: attaches translation field handlers if isWritable is true
 */

function JMSTranslationManager(updateMessagePath, isWritable, isXliff) {
    if (!window.jQuery) {
        console.error('JMSTranslationManager requires JQuery.');
        return;
    }

    this.updateMessagePath = updateMessagePath;
    this.isWritable = isWritable ? isWritable : false;
    this.isXliff = isXliff ? isXliff : false;

    this.domain = {
        selector: '#config select',
        handlers: function (JMS) {
            $(JMS.domain.selector).change(function () {
                $(this).parent().submit();
            });
        }
    };

    this.copier = {
        selector: '.jms-alt-message',
        copyAllLabel: 'Copy all translations from: ',
        copyAllSelector: '.jms-copy-all-select',
        copyLinkContent: '<a href="#" class="jms-copy-link" alt="Copy">Copy</a>',
        copySelector: '.jms-copy-link',
        localeSelector: '#config select[name=locale] > option',
        headerSelector: '.jms-trans-header',
        elements: function (JMS) {
            // Add the copy link to all other translations
            $(JMS.copier.selector).prev().prepend(JMS.copier.copyLinkContent);

            // Add the copy all selector to the new translation section
            // $(JMS.copier.newMessageSelector).prepend(JMS.copier.copySelectorContent);
            var locales = $(JMS.copier.localeSelector).clone();

            // Change the current locale to a label, and make it first in the list
            var localeSelect = $('<select/>', {
                'class': 'jms-copy-all-select'
            });
            localeSelect.append(locales.filter(':selected').text('[Select Locale]'));
            localeSelect.append(locales.not(':selected'));

            var selectSpan = $('<span/>', {
                'class': 'jms-copy-all'
            }).append(JMS.copier.copyAllLabel).append(localeSelect);
            $(JMS.copier.headerSelector).prepend(selectSpan);
        },
        handlers: function (JMS) {
            $(JMS.copier.copySelector).on('click', null, {"JMS": JMS}, JMS.copier.copyClick);
            $(JMS.copier.copyAllSelector).on('change', null, {"JMS": JMS}, JMS.copier.copyAll);
        },
        copy: function (index, alt_translation) {
            var translation = $(alt_translation).closest('td').prev('td').children('textarea');
            translation.text($(alt_translation).text());
            translation.blur();
        },
        copyClick: function (event) {
            var JMS = event.data.JMS;
            event.preventDefault();
            var alt_translation = $(this).closest('p').next('pre');
            alt_translation.each(JMS.copier.copy);
        },
        copyAll: function (event) {
            var JMS = event.data.JMS;
            var elem = $(event.target);

            var selectedLocale = elem.val();
            var closest_div = elem.closest('div');
            var alt_translations = elem.closest('div').find(JMS.copier.selector).filter(
                function () {
                    return selectedLocale == $(this).data('locale')
                }
            );

            alt_translations.each(JMS.copier.copy);
        }
    };

    this.fadeAndRemove = function (element, fast) {
        if (!fast) {
            element
                .fadeIn(300)
                .delay(5000);
        }
        element
            .css('display', 'block')
            .fadeOut(300, function () {
                $(this).remove();
            });
        return element;
    };

    this.notes = {
        addNoteLinkContent: '<a href="#" class="jms-add-note-link" alt="Add Note">Add Note</a>',
        addNoteSelector: '.jms-add-note-link',
        columnSelector: '.jms-translation-col-1',
        deleteNoteSelector: '.jms-delete-note-link',
        deleteNoteLinkContent: '<a href="#" class="jms-delete-note-link" alt="Delete Note">Delete</a>',
        noteLabel: '<h6>Note</h6>',
        noteSelector: '.jms-translation-note',
        addNote: function (event) {
            event.preventDefault();

            var JMS = event.data.JMS;
            var elem = $(event.target);

            var newLabel = $(JMS.notes.noteLabel);
            var prev = elem.prev('textarea');
            var newNote = $('<textarea>', {
                'data-id': elem.siblings('.jms-translation-anchor').attr('id'),
                'data-type': 'note',
                'data-index': prev.length > 0 ? (prev.data('index') + 1) : 0,
                'class': 'jms-translation-note'
            }).on('blur', null, {"JMS": JMS}, JMS.notes.checkEmpty);

            JMS.writable(JMS, newNote);

            elem.before(newLabel, newNote);
            newNote.focus();
        },
        checkEmpty: function (event) {
            var elem = $(event.target);
            if (0 == elem.val().length) {
                JMS.fadeAndRemove(elem.prev('h6'), true);
                JMS.fadeAndRemove(elem, true);
            }
        },
        delete: function (event) {
            var elem = $(event.target);
            var textarea = elem.parent().next('textarea');
            textarea.val('').blur();
        },
        elements: function (JMS) {
            if (JMS.isXliff) {
                $(JMS.notes.columnSelector).append(JMS.notes.addNoteLinkContent);
                $(JMS.notes.noteSelector).prev('h6').append(JMS.notes.deleteNoteLinkContent);
            }
        },
        handlers: function (JMS) {
            $(JMS.notes.addNoteSelector).on('click', null, {"JMS": JMS}, JMS.notes.addNote);
            $(JMS.notes.deleteNoteSelector).on('click', null, {"JMS": JMS}, JMS.notes.delete);
            $(JMS.notes.noteSelector).on('blur', null, {"JMS": JMS}, JMS.notes.checkEmpty);
        }
    },

    this.truncator = {
        selector: '.truncate-left',
        side: 'left',
        fill: '<a href="#" class="untruncate">&hellip;</a>',
        untruncateSelector: '.untruncate',
        truncate: function (JMS) {
            if (jQuery().trunk8) {
                $(JMS.truncator.selector).trunk8({
                    side: JMS.truncator.side,
                    fill: JMS.truncator.fill
                });

                $(document).on('click', JMS.truncator.untruncateSelector, function (event) {
                    var elem = $(this);
                    elem.parent().trunk8('revert');
                    event.preventDefault();
                });
            }
            else {
                console.error('Truncator requires jQuery Trunk8 plugin.');
            }
        }
    };

    this.translation = {
        abbrSelector: 'abbr',
        selector: 'textarea',
        ajax: {
            type: 'POST',
            headers: {'X-HTTP-METHOD-OVERRIDE': 'PUT'},
            dataMethod: 'PUT',
            error: function (textStatus, event, JMS, errorThrown) {
                var elem = $(event.target);
                var errorMessage = $(JMS.translation.ajax.unsavedMessageContent.replace('%s', errorThrown).replace ('%d', textStatus));
                elem.parent().closest('tr').find(JMS.translation.abbrSelector).after(JMS.fadeAndRemove(errorMessage));
            },
            errorMessageContent: '<span class="alert-message label error">Error %d: %s</span>',
            success: function (data, event, JMS) {
                var elem = $(event.target);

                if (data.status == 'SUCCESS') {
                    var savedMessage = JMS.fadeAndRemove($(JMS.translation.ajax.savedMessageContent.replace('%s', data.message)));
                    elem.parent().closest('tr').find(JMS.translation.abbrSelector).after(savedMessage);

                } else {
                    var unsavedMessage = JMS.fadeAndRemove($(JMS.translation.ajax.unsavedMessageContent.replace('%s', data.message)));
                    elem.parent().closest('tr').find(JMS.translation.abbrSelector).after(unsavedMessage);
                }
            },
            savedMessageContent: '<span class="alert-message label success">%s</span>',
            unsavedMessageContent: '<span class="alert-message label error">%s</span>'
        },
        blur: function (event, JMS) {
            var elem = $(event.target);
            var noteQuery = '';
            if (elem.data('type') == 'note') {
                noteQuery = '&type=' + encodeURIComponent(elem.data('type')) + '&index=' + encodeURIComponent(elem.data('index'));
            }
            $.ajax(JMS.updateMessagePath + '?id=' + encodeURIComponent(elem.data('id')) + noteQuery, {
                type: JMS.translation.ajax.type,
                headers: JMS.translation.ajax.headers,
                data: {'_method': JMS.translation.ajax.dataMethod, 'message': elem.val()},
                error: function (jqXHR, textStatus, errorThrown) {
                    JMS.translation.ajax.error(textStatus, event, JMS, errorThrown);
                },
                success: function (data) {
                    JMS.translation.ajax.success(data, event, JMS);
                }
            });
        },
        focus: function (event, JMS) {
            var elem = $(event.target);
            elem.select();

            var timeoutId = elem.data('timeoutId');
            if (timeoutId) {
                clearTimeout(timeoutId);
                elem.data('timeoutId', undefined);
            }

            elem.parent().children('.alert-message').remove();
        }
    };

    this.ready = function () {
        var JMS = this;
        $(document).ready(function (event) {
            JMS.copier.elements(JMS);
            JMS.copier.handlers(JMS);
            JMS.domain.handlers(JMS);
            JMS.notes.elements(JMS);
            JMS.notes.handlers(JMS);
            JMS.truncator.truncate(JMS);
            if (JMS.isWritable) {
                JMS.writable(JMS);
            }
        });
    };

    this.writable = function (JMS, item) {
        if (!item) {
            item = $(JMS.translation.selector);
        }

        item
            .blur(function (event) {
                JMS.translation.blur(event, JMS);
            })
            .focus(function (event) {
                JMS.translation.focus(event, JMS);
            })
        ;
    };
};