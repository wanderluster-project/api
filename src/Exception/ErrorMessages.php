<?php

declare(strict_types=1);

namespace App\Exception;

class ErrorMessages
{
    const INVALID_ENTITY_TYPE = 'Invalid Entity Type - %s';
    const INVALID_MIMETYPE = 'Invalid MimeType - %s';
    const INVALID_S3_BUCKET = 'Invalid S3 Bucket - %s';
    const INVALID_ENTITY_ID = 'Invalid EntityID format - %s';
    const INVALID_SNAPSHOT_ID = 'Invalid SnapshotId format - %s';

    const REQUEST_MISSING_PARAMETER = 'Missing parameter: %s';
    const REQUEST_INVALID_FILE = 'Invalid or corrupt file uploaded.';
    const SERVER_ERROR_UPLOADING = 'Error encountered saving file.  Please try again later.';
    const SERVER_ERROR_DELETING = 'Error encountered deleting file.';

    const SERIALIZATION_ERROR = 'Error serializing - %s';
    const DESERIALIZATION_ERROR = 'Error deserializing - %s';
}
