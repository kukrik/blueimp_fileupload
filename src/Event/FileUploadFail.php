<?php

namespace QCubed\Plugin\Event;

use QCubed\Event\EventBase;

class FileUploadFail extends EventBase {
    const EVENT_NAME = 'fileuploadfail';
}
