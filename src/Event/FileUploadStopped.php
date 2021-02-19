<?php

namespace QCubed\Plugin\Event;

use QCubed\Event\EventBase;

class FileUploadStopped extends EventBase {
    const EVENT_NAME = 'fileuploadstopped';
}
