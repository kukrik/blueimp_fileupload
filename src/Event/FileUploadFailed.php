<?php

namespace QCubed\Plugin\Event;

use QCubed\Event\EventBase;

class FileUploadFailed extends EventBase {
    const EVENT_NAME = 'fileuploadfailed';
}
