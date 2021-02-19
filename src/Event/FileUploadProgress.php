<?php

namespace QCubed\Plugin\Event;

use QCubed\Event\EventBase;

class FileUploadProgress extends EventBase {
    const EVENT_NAME = 'fileuploadprogress';
}
