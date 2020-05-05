<?php

declare(strict_types=1);

namespace App\Exception;

class ErrorMessages
{
    const INVALID_ENTITY_TYPE = 'Invalid Entity Type - %s';
    const INVALID_ENTITY_DATA = 'Invalid Entity Data';
    const INVALID_EXT = 'Invalid File Extension - %s';
    const INVALID_MIMETYPE = 'Invalid MimeType - %s';
    const INVALID_S3_BUCKET = 'Invalid S3 Bucket - %s';
    const INVALID_ENTITY_ID = 'Invalid EntityID format - %s';
    const INVALID_SNAPSHOT_ID = 'Invalid SnapshotId format - %s';
    const INVALID_LANGUAGE_CODE = 'Invalid language code -  %s';
    const INVALID_USERNAME = 'Invalid username - %s';
    const INVALID_JSON = 'Invalid JSON passed as argument.';
    const OPTION_REQUIRED = 'Configuration option missing - %s';

    const INVALID_DATATYPE_VALUE = 'Invalid value passed to %s data type - %s.';
    const ERROR_HYDRATING_DATATYPE = 'Error hydrating %s data type - %s';

    const REQUEST_MISSING_PARAMETER = 'Missing parameter: %s';
    const REQUEST_INVALID_FILE = 'Invalid or corrupt file uploaded.';
    const SERVER_ERROR_UPLOADING = 'Error encountered saving file.  Please try again later.';
    const SERVER_ERROR_DELETING = 'Error encountered deleting file.';

    const SERIALIZATION_ERROR = 'Error serializing - %s';
    const DESERIALIZATION_ERROR = 'Error deserializing - %s';

    const METHOD_NOT_IMPLEMENTED = 'Method not implemented yet - %s';
    const ENTITY_LANGUAGE_NOT_SET = 'Entity language not set.';

    const JWT_KEYS_MISSING = 'JWT Keys Missing.';
    const JWT_INVALID = 'Invalid JWT';
}
