<?php

/*
 * Copyright 2013 Dieter Peeters <peetersdiet@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace JMS\TranslationBundle\Model\Message;

use JMS\TranslationBundle\Model\Message;

/**
 * XLIFF message state.
 *
 * See: http://docs.oasis-open.org/xliff/v1.2/os/xliff-core.html#state
 */
class XliffMessageState extends Message
{
    const STATE_NONE = null;
    const STATE_FINAL = 'final';
    const STATE_NEEDS_ADAPTATION = 'needs-adaptation';
    const STATE_NEEDS_L10N = 'needs-l10n';
    const STATE_NEEDS_REVIEW_ADAPTATION = 'needs-review-adaptation';
    const STATE_NEEDS_REVIEW_L10N = 'needs-review-l10n';
    const STATE_NEEDS_REVIEW_TRANSLATION = 'needs-review-translation';
    const STATE_NEEDS_TRANSLATION = 'needs-translation';
    const STATE_NEW = 'new';
    const STATE_SIGNED_OFF = 'signed-off';
    const STATE_TRANSLATED = 'translated';
}
